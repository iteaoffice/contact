<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category  Search
 *
 * @author    Bart van Eijck <bart.van.eijck@itea3.org>
 * @copyright Copyright (c) 2004-2015 ITEA Office (http://itea3.org)
 */

namespace Contact\Search\Service;

use Contact\Entity\Contact;
use Contact\Entity\Photo;
use Contact\Entity\Profile;
use Contact\Service\ContactService;
use Search\Service\AbstractSearchService;
use Solarium\QueryType\Select\Query\Query;

/**
 * Contact Solr search service
 */
class ContactSearchService extends AbstractSearchService
{
    const SOLR_CONNECTION = 'contact';

    /**
     * The contact service
     *
     * @var ContactService
     */
    protected $contactService;

    /**
     * Update or insert a contact document
     *
     * @param Contact $contact
     *
     * <field name="id" type="string" indexed="true" stored="true" required="true" multiValued="false" />
     * <field name="contact_id" type="int" indexed="true" stored="true" omitNorms="true"/>
     * <field name="fullname" type="text_general" indexed="true" stored="true" omitNorms="true"/>
     * <field name="lastname" type="string" indexed="true" stored="true" omitNorms="true"/>
     * <field name="position" type="text_en_splitting" indexed="true" stored="true" omitNorms="true"/>
     * <field name="type" type="string" indexed="true" stored="true" omitNorms="true"/>
     * <field name="photo_url" type="string" indexed="true" stored="true" omitNorms="true"/>
     * <field name="organisation" type="string" indexed="true" stored="true" omitNorms="true"/>
     * <field name="organisation_type" type="string" indexed="true" stored="true" omitNorms="true"/>
     * <field name="country" type="string" indexed="true" stored="true" omitNorms="true"/>
     * <field name="profile" type="text_en_splitting" indexed="true" stored="true" omitNorms="true"/>
     * <field name="cv" type="text_en_splitting" indexed="true" stored="true" omitNorms="true"/>
     *
     * @return \Solarium\Core\Query\Result\ResultInterface
     * @throws \Solarium\Exception\HttpException
     */
    public function updateDocument($contact)
    {
        // Get an update query instance
        $update = $this->getSolrClient()->createUpdate();

        $contactDocument = $update->createDocument();
        $contactDocument->id = $contact->getResourceId();
        $contactDocument->contact_id = $contact->getId();
        $contactDocument->type = 'contact';
        $contactDocument->fullname = $contact->getDisplayName();
        $contactDocument->lastname = $contact->getLastName();
        $contactDocument->position = $contact->getPosition();

        if (!is_null($contact->getProfile())) {
            $contactDocument->profile = str_replace(PHP_EOL, '', strip_tags($contact->getProfile()->getDescription()));

            if (($contact->getProfile()->getHidePhoto() === Profile::NOT_HIDE_PHOTO)
                && ($contact->getPhoto()->count() > 0)
            ) {
                /** @var Photo $photo */
                $photo = $contact->getPhoto()->first();
                $contactDocument->photo_url = $this->getServiceLocator()->get('viewhelpermanager')->get('url')
                    ->__invoke('assets/contact-photo', [
                        'hash' => $photo->getHash(),
                        'ext'  => $photo->getContentType()->getExtension(),
                        'id'   => $photo->getId(),
                    ]);
            }
        }

        if (!is_null($contact->getContactOrganisation())) {
            $contactDocument->organisation = $contact->getContactOrganisation()->getOrganisation()->getOrganisation();
            $contactDocument->organisation_type = $contact->getContactOrganisation()->getOrganisation()->getType();
            $contactDocument->country = $contact->getContactOrganisation()->getOrganisation()->getCountry()
                ->getCountry();
        }

        if (!is_null($contact->getCv())) {
            $contactDocument->cv = str_replace(
                PHP_EOL,
                '',
                strip_tags(stream_get_contents($contact->getCv()->getCv()))
            );
        }

        $update->addDocument($contactDocument);
        $update->addCommit();

        return $this->executeUpdateDocument($update);
    }

    /**
     * Update the current index and optionally clear all existing data.
     *
     * @param boolean $clear
     */
    public function updateIndex($clear = false)
    {
        $contacts = $this->getContactService()->findContactsWithActiveProfile(true);
        $this->updateIndexWithCollection($contacts, $clear);
    }

    /**
     * @param string $searchTerm
     * @param string $order
     * @param string $direction
     *
     * @return ContactSearchService
     */
    public function setSearch($searchTerm, $order = 'lastname', $direction = Query::SORT_ASC)
    {
        $this->setQuery($this->getSolrClient()->createSelect());
        $this->getQuery()->setQuery(str_replace('%s', $searchTerm, implode(' ' . Query::QUERY_OPERATOR_OR . ' ', [
            'fullname:*%s*',
            'position:*%s*',
            'organisation:*%s',
            'profile:*%s*',
            'country:*%s*',
        ])));

        $this->getQuery()->addSort($order, $direction);

        $facetSet = $this->getQuery()->getFacetSet();
        $facetSet->createFacetField('organisation_type')->setField('organisation_type')->setMinCount(0)
            ->setExcludes(['organisation_type']);
        if (('*' !== $searchTerm) && (strlen($searchTerm) > 2)) {
            $facetSet->createFacetField('organisation')->setField('organisation')->setMinCount(1);
        }
        $facetSet->createFacetField('country')->setField('country')->setMinCount(1)->setExcludes(['country']);

        return $this;
    }

    /**
     * @return ContactService
     */
    public function getContactService()
    {
        return $this->contactService;
    }

    /**
     * @param ContactService $contactService
     *
     * @return ContactSearchService
     */
    public function setContactService(ContactService $contactService)
    {
        $this->contactService = $contactService;

        return $this;
    }
}
