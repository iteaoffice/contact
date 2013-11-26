<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Contact
 * @package     Service
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
namespace Contact\Service;

use Contact\Entity\AddressType;
use Contact\Entity\PhoneType;
use Contact\Entity\Contact;
use Contact\Entity\Selection;

use Contact\Service\AddressService;
use Contact\Options\CommunityOptionsInterface;
use Doctrine\ORM\PersistentCollection;
use Project\Service\ProjectService;
use Organisation\Service\OrganisationService;

/**
 * ContactService
 *
 * this is a generic wrapper service for all the other services
 *
 * First parameter of all methods (lowercase, underscore_separated)
 * will be used to fetch the correct model service, one exception is the 'linkModel'
 * method.
 *
 */
class ContactService extends ServiceAbstract
{
    /**
     * @var AddressService
     */
    protected $addressService;
    /**
     * @var ProjectService
     */
    protected $projectService;
    /**
     * @var OrganisationService
     */
    protected $organisationService;
    /**
     * @var CommunityOptionsInterface
     */
    protected $communityOptions;
    /**
     * @var Contact
     */
    protected $contact;

    /** @param int $id
     *
     * @return ContactService;
     */
    public function setContactId($id)
    {
        $this->setContact($this->findEntityById('contact', $id));

        return $this;
    }

    /**
     * @param $email
     *
     * @return null|Contact
     */
    public function findContactByEmail($email)
    {
        return $this->getEntityManager()
            ->getRepository($this->getFullEntityName('contact'))
            ->findContactByEmail($email);
    }

    /**
     * Find a contact based on a hash
     *
     * @param $hash
     *
     * @return null|Contact
     */
    public function findContactByHash($hash)
    {
        $contact   = new Contact();
        $contactId = $contact->decryptHash($hash);

        return $this->findEntityById('contact', $contactId);
    }

    /**
     * Give the access object, based on the name of the access
     *
     * @param $name
     *
     * @return \Contact\Entity\Access|null
     */
    public function findAccessByName($name)
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName('access'))->findOneBy(
            array('access' => $name)
        );
    }

    /**
     * Parse the fullname of a project
     *
     * @return string
     */
    public function parseFullName()
    {
        return
            trim(
                $this->getContact()->getFirstName() . ' ' .
                $this->getContact()->getMiddleName()) . ' ' .
            $this->getContact()->getLastName();
    }

    /**
     * Find the visit address of a contact
     *
     * @throws \RunTimeException
     * @return AddressService
     */
    public function getVisitAddress()
    {
        if (is_null($this->getContact())) {
            throw new \RunTimeException(sprintf("A contact should be set"));
        }

        return $this->getAddressService()->findAddressByContactAndType(
            $this->getContact(),
            AddressType::ADDRESS_TYPE_VISIT
        );
    }

    /**
     * Find the direct phone number of a contact
     *
     * @return null|object
     * @throws \RunTimeException
     */
    public function getDirectPhone()
    {
        if (is_null($this->getContact())) {
            throw new \RunTimeException(sprintf("A contact should be set"));
        }

        return $this->getEntityManager()->getRepository($this->getFullEntityName('phone'))->findOneBy(
            array('contact' => $this->contact,
                  'type'    => PhoneType::PHONE_TYPE_DIRECT)
        );
    }

    /**
     * Find the mobile phone number of a contact
     *
     * @return null|object
     * @throws \RunTimeException
     */
    public function getMobilePhone()
    {
        if (is_null($this->getContact())) {
            throw new \RunTimeException(sprintf("A contact should be set"));
        }

        return $this->getEntityManager()->getRepository($this->getFullEntityName('phone'))->findOneBy(
            array('contact' => $this->contact,
                  'type'    => PhoneType::PHONE_TYPE_MOBILE)
        );
    }

    /**
     * @return OrganisationService
     */
    public function findOrganisationService()
    {
        return $this->getOrganisationService()->setOrganisationId(
            $this->getContact()->getContactOrganisation()->getOrganisation()->getId());
    }


    /**
     * @return \Project\Service\ProjectService[]
     */
    public function findProjects()
    {
        return $this->getProjectService()->findProjectByContact($this->getContact());
    }

    /**
     * @param $emailAddress
     */
    public function register($emailAddress)
    {
        $emailService = $this->getServiceLocator()->get('email');
        $emailService->setTemplate("/auth/register:mail");

        $email = $emailService->create();
        $email->addTo($emailAddress, "Johan van der heide");
        $email->setVariable('test');

        $emailService->send($email);
    }

    /**
     * Return true or false depending if a user is in the community
     *
     * @return bool
     */
    public function isCommunity()
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName('contact')
        )->findIsCommunityMember($this->getContact(), $this->getCommunityOptions());
    }

    /**
     * Returns true when a user is in a selection
     *
     * @param Selection|Selection[] $selections
     *
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function inSelection($selections)
    {

        if (is_null($this->getContact())) {
            throw new \InvalidArgumentException("The contact cannot be null");
        }

        if (!is_array($selections) && !$selections instanceof PersistentCollection) {
            $selections = array($selections);
        }

        foreach ($selections as $selection) {
            if (!$selection instanceof Selection) {
                throw new \InvalidArgumentException("Selection should be instance of Selection");
            }

            if (is_null($selection->getId())) {
                throw new \InvalidArgumentException("The given selection cannot be empty");
            }

            if ($this->findContactInSelection($selection)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Selection $selection
     *
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function findContactInSelection(Selection $selection)
    {

        if (is_null($this->getContact())) {
            throw new \InvalidArgumentException("The contact cannot be null");
        }

        if (!is_null($selection->getSql())) {
            //We have a dynamic query, check if the contact is in the selection
            return $this->getEntityManager()->getRepository(
                $this->getFullEntityName('contact')
            )->isContactInSelectionSQL($this->getContact(), $selection->getSql());
        }

        /**
         * The selection contains contacts, do an extra query to find the contact
         */
        if (sizeof($selection->getSelectionContact()) > 0) {
            $contact = $this->getEntityManager()->getRepository($this->getFullEntityName('SelectionContact'))->findOneBy(
                array(
                    'contact'   => $this->getContact(),
                    'selection' => $selection
                )
            );

            /**
             * Return true when we found a contact
             */
            if (!is_null($contact)) {
                return true;
            }
        }
    }

    /**
     * @param Contact $contact
     *
     * @return ContactService
     */
    private function createServiceElement(Contact $contact)
    {
        $contactService = new self();
        $contactService->setServiceLocator($this->getServiceLocator());
        $contactService->setContact($contact);

        return $contactService;
    }

    /**
     * @param \Contact\Entity\Contact $contact
     *
     * @return ContactService;
     */
    public function setContact($contact)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * @return \Contact\Entity\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param ProjectService $projectService
     */
    public function setProjectService($projectService)
    {
        $this->projectService = $projectService;
    }

    /**
     * @return ProjectService
     */
    public function getProjectService()
    {
        if (!$this->projectService instanceof ProjectService) {
            $this->setProjectService($this->getServiceLocator()->get('project_project_service'));
        }

        return $this->projectService;
    }

    /**
     * @param OrganisationService $organisationService
     */
    public function setOrganisationService($organisationService)
    {
        $this->organisationService = $organisationService;
    }

    /**
     * @return OrganisationService
     */
    public function getOrganisationService()
    {
        if (!$this->organisationService instanceof OrganisationService) {
            $this->setOrganisationService($this->getServiceLocator()->get('organisation_organisation_service'));
        }

        return $this->organisationService;
    }

    /**
     * @param AddressService $addressService
     */
    public function setAddressService($addressService)
    {
        $this->addressService = $addressService;
    }

    /**
     * @return AddressService
     */
    public function getAddressService()
    {
        if (!$this->addressService instanceof AddressService) {
            $this->setAddressService($this->getServiceLocator()->get('contact_address_service'));
        }

        return $this->addressService;
    }

    /**
     * get community options
     *
     * @return CommunityOptionsInterface
     */
    public function getCommunityOptions()
    {
        if (!$this->communityOptions instanceof CommunityOptionsInterface) {
            $this->setCommunityOptions($this->getServiceLocator()->get('contact_community_options'));
        }

        return $this->communityOptions;
    }

    /**
     * @param \Contact\Options\CommunityOptionsInterface $communityOptions
     */
    public function setCommunityOptions($communityOptions)
    {
        $this->communityOptions = $communityOptions;
    }
}
