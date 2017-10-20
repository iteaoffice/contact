<?php
/**
 * ITEA Office all rights reserved
 *
 * @category  Search
 *
 * @author    Bart van Eijck <bart.van.eijck@itea3.org>
 * @copyright Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Search\Service;

use Contact\Entity\Contact;
use Contact\Entity\Photo;
use Contact\Entity\Profile;
use Contact\Service\ContactService;
use Search\Service\AbstractSearchService;
use Search\Service\SearchServiceInterface;
use Solarium\QueryType\Select\Query\Query;

/**
 * Contact Solr search service
 */
class ProfileSearchService extends AbstractSearchService
{
    /**
     *
     */
    const SOLR_CONNECTION = 'contact_profile';

    /**
     * The contact service
     *
     * @var ContactService
     */
    protected $contactService;

    /**
     * @param Contact $contact
     * @return \Solarium\QueryType\Update\Result
     */
    public function updateDocument($contact)
    {
        // Get an update query instance
        $update = $this->getSolrClient()->createUpdate();

        $contactDocument = $update->createDocument();
        $contactDocument->id = $contact->getResourceId();
        $contactDocument->contact_id = $contact->getId();
        $contactDocument->contact_hash = $contact->parseHash();

        $contactDocument->fullname = $contact->getDisplayName();
        $contactDocument->fullname_search = $contact->getDisplayName();
        $contactDocument->fullname_sort = $contact->getDisplayName();

        $contactDocument->lastname = $contact->getLastName();
        $contactDocument->lastname_search = $contact->getLastName();
        $contactDocument->lastname_sort = $contact->getLastName();

        $contactDocument->position = $contact->getPosition();
        $contactDocument->position_search = $contact->getPosition();
        $contactDocument->position_sort = $contact->getPosition();

        if (!is_null($contact->getProfile())) {
            $contactDocument->profile = str_replace(PHP_EOL, '', strip_tags((string) $contact->getProfile()->getDescription()));
            $contactDocument->profile_sort = str_replace(
                PHP_EOL,
                '',
                strip_tags($contact->getProfile()->getDescription())
            );
            $contactDocument->profile_search = str_replace(
                PHP_EOL,
                '',
                strip_tags($contact->getProfile()->getDescription())
            );

            if (($contact->getProfile()->getHidePhoto() === Profile::NOT_HIDE_PHOTO)
                && ($contact->getPhoto()->count() > 0)
            ) {
                /** @var Photo $photo */
                $photo = $contact->getPhoto()->first();
                $contactDocument->photo_url = $this->getUrl(
                    'assets/contact-photo',
                    [
                        'hash'  => $photo->getHash(),
                        'ext'   => $photo->getContentType()->getExtension(),
                        'id'    => $photo->getId(),
                        'width' => 200,
                    ]
                );
            }
        }

        if (!is_null($contact->getContactOrganisation())) {
            $contactDocument->organisation = $contact->getContactOrganisation()->getOrganisation()->getOrganisation();
            $contactDocument->organisation_sort = $contact->getContactOrganisation()->getOrganisation()->getOrganisation();
            $contactDocument->organisation_search = $contact->getContactOrganisation()->getOrganisation()->getOrganisation();
            $contactDocument->organisation_type = $contact->getContactOrganisation()->getOrganisation()->getType();
            $contactDocument->organisation_type_sort = $contact->getContactOrganisation()->getOrganisation()->getType();
            $contactDocument->organisation_type_search = $contact->getContactOrganisation()->getOrganisation()->getType();
            $contactDocument->country = $contact->getContactOrganisation()->getOrganisation()->getCountry()->getCountry();
            $contactDocument->country_sort = $contact->getContactOrganisation()->getOrganisation()->getCountry()->getCountry();
            $contactDocument->country_search = $contact->getContactOrganisation()->getOrganisation()->getCountry()->getCountry();
        }

        if (!is_null($contact->getCv())) {
            $cv = str_replace(
                PHP_EOL,
                '',
                strip_tags((string) stream_get_contents($contact->getCv()->getCv()))
            );

            $contactDocument->cv = $cv;
            $contactDocument->cv_search = $cv;
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
     * @return ContactService
     */
    public function getContactService()
    {
        return $this->contactService;
    }

    /**
     * @param ContactService $contactService
     *
     * @return ProfileSearchService
     */
    public function setContactService(ContactService $contactService): ProfileSearchService
    {
        $this->contactService = $contactService;

        return $this;
    }

    /**
     * @param string $searchTerm
     * @param array $searchFields
     * @param string $order
     * @param string $direction
     *
     * @return SearchServiceInterface
     */
    public function setSearch(
        string $searchTerm,
        array $searchFields = [],
        string $order = '',
        string $direction = Query::SORT_ASC
    ): SearchServiceInterface {
        $this->setQuery($this->getSolrClient()->createSelect());
        $this->getQuery()->setQuery(static::parseQuery($searchTerm, $searchFields));

        $hasTerm = !in_array($searchTerm, ['*', '']);
        $hasSort = ($order !== '');

        if ($hasSort) {
            $this->getQuery()->addSort($order, $direction);
        }
        if ($hasTerm) {
            $this->getQuery()->addSort('score', Query::SORT_DESC);
        } else {
            $this->getQuery()->addSort('lastname_sort', Query::SORT_ASC);
        }

        $facetSet = $this->getQuery()->getFacetSet();
        $facetSet->createFacetField('organisation_type')->setField('organisation_type')->setMinCount(0)
            ->setExcludes(['organisation_type']);
        if (('*' !== $searchTerm) && (strlen($searchTerm) > 2)) {
            $facetSet->createFacetField('organisation')->setField('organisation')->setMinCount(1);
        }
        $facetSet->createFacetField('country')->setField('country')->setMinCount(1)->setExcludes(['country']);

        return $this;
    }
}