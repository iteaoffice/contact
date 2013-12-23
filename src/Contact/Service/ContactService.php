<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     Service
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Contact\Service;

use Contact\Entity\AddressType;
use Contact\Entity\PhoneType;
use Contact\Entity\Contact;
use Contact\Entity\Selection;

use Deeplink\Service\DeeplinkService;
use Contact\Options\CommunityOptionsInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Organisation\Entity\Organisation;
use Project\Service\ProjectService;
use Organisation\Service\OrganisationService;
use General\Service\GeneralService;
use Event\Service\MeetingService;

use ZfcUser\Options\UserServiceOptionsInterface;
use Zend\Crypt\Password\Bcrypt;

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
     * @var DeeplinkService
     */
    protected $deeplinkService;
    /**
     * @var ProjectService
     */
    protected $projectService;
    /**
     * @var MeetingService
     */
    protected $meetingService;
    /**
     * @var OrganisationService
     */
    protected $organisationService;
    /**
     * @var GeneralService
     */
    protected $generalService;
    /**
     * @var CommunityOptionsInterface
     */
    protected $communityOptions;
    /**
     * @var UserServiceOptionsInterface
     */
    protected $zfcUserOptions;
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
     * Find a list of upcoming meetings were a user has not registered yet
     *
     * @return \Event\Entity\Meeting[]
     */
    public function findUpcomingMeetings()
    {
        return $this->getMeetingService()->findUnregisteredMeetingsByContact($this->getContact());
    }

    /**
     * Parse the fullname of a project
     *
     * @return string
     */
    public function parseFullName()
    {
        return $this->getContact()->getDisplayName();
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
     * @return OrganisationService|null
     */
    public function findOrganisationService()
    {
        /**
         * Return null when the contactOrganisation is not defined
         */
        if (is_null($this->getContact()->getContactOrganisation())) {
            return null;
        }

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
     * Create an account and send an email address
     *
     * @param $emailAddress
     *
     * @return Contact
     */
    public function register($emailAddress)
    {
        //Create the account
        $contact = new Contact();
        $contact->setEmail($emailAddress);

        //Fix the gender
        $contact->setGender($this->getGeneralService()->findEntityById('gender', 0)); //Unknown
        $contact->setTitle($this->getGeneralService()->findEntityById('title', 0)); //Unknown

        $contact = $this->newEntity($contact);

        //Create a target
        $target = $this->getDeeplinkService()->createTargetFromRoute('contact/profile');
        //Create a deeplink for the user which redirects to the profile-page
        $deeplink = $this->getDeeplinkService()->createDeeplink($contact, $target);

        /**
         * Send the email tot he user
         */
        $emailService = $this->getServiceLocator()->get('email');
        $emailService->setTemplate("/auth/register:mail");
        $email = $emailService->create();
        $email->addTo($emailAddress);
        $email->setUrl($this->getDeeplinkService()->parseDeeplinkUrl($deeplink));
        $emailService->send($email);

        return $contact;
    }

    /**
     * Create an account and send an email address
     *
     * @param $emailAddress
     *
     * @throws \InvalidArgumentException
     *
     * @return Contact
     */
    public function lostPassword($emailAddress)
    {
        //Create the account
        $contact = $this->findContactByEmail($emailAddress);

        if (is_null($contact)) {
            throw new \InvalidArgumentException(
                sprintf("The contact with emailAddress %s cannot be found", $emailAddress)
            );
        }

        $contactService = $this->createServiceElement($contact);

        //Create a target
        $target = $this->getDeeplinkService()->createTargetFromRoute('contact/change-password');
        //Create a deeplink for the user which redirects to the profile-page
        $deeplink = $this->getDeeplinkService()->createDeeplink($contact, $target);

        /**
         * Send the email tot he user
         */
        $emailService = $this->getServiceLocator()->get('email');
        $emailService->setTemplate("/auth/forgotpassword:mail");
        $email = $emailService->create();
        $email->addTo($emailAddress, $contactService->parseFullName());
        $email->setFullname($contactService->parseFullName());
        $email->setUrl($this->getDeeplinkService()->parseDeeplinkUrl($deeplink));
        $emailService->send($email);

        return $contact;
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
     * Update the password for a contact. Check with the current password when given
     * New accounts have no password so this check is not always needed
     *
     * @param string  $password
     * @param Contact $contact
     *
     * @return bool
     */
    public function updatePasswordForContact($password, Contact $contact)
    {
        $Bcrypt = new Bcrypt;
        $Bcrypt->setCost($this->getZfcUserOptions()->getPasswordCost());

        $pass = $Bcrypt->create(md5($password));
        $contact->setPassword(md5($password));
        $contact->setSaltedPassword($pass);

        $this->updateEntity($contact);

        return true;
    }

    /**
     * We use this function to update the contactOrganisation of a user.
     * As input we use the corresponding contact entity and the array containing the
     * contactOrganisation information
     *
     * $contactOrganisation['organisation'] > Name of the organisation
     * $contactOrganisation['country'] > CountryId
     *
     * @param Contact $contact
     * @param array   $contactOrganisation
     */
    public function updateContactOrganisation(Contact $contact, array $contactOrganisation)
    {
        $country = $this->getGeneralService()->findEntityById('country', (int)$contactOrganisation['country']);

        $organisation = $this->getOrganisationService()->findOrganisationByNameCountryAndEmailAddress(
            $contactOrganisation['organisation'],
            $country,
            $contact->getEmail()
        );

        $currentContactOrganisation = $contact->getContactOrganisation();

        /**
         * We did nt find an organisation, so we need to create it
         */
        if (sizeof($organisation) === 0) {
            $organisation = new Organisation();
            $organisation->setOrganisation($contactOrganisation['organisation']);
            $organisation->setCountry($country);
            $organisation->setType($this->organisationService->findEntityById('type', 0)); //Unknown
            $currentContactOrganisation->setOrganisation($organisation);
        } else {
            /**
             * Go over the found organisation to match the branching
             */

            foreach ($organisation as $foundOrganisation) {
                if (strpos($foundOrganisation->getOrganisation(), $contactOrganisation['organisation']) !== false &&
                    strlen($foundOrganisation->getOrganisation()) > $contactOrganisation['organisation']
                ) {
                    /**
                     * We have found a match of the organisation in the string and
                     */
                    $currentContactOrganisation->setBranch(
                        str_replace($contactOrganisation['organisation'], '~', $foundOrganisation->getOrganisation())
                    );
                }
                $currentContactOrganisation->setOrganisation($foundOrganisation);
            }
        }

        $this->updateEntity($currentContactOrganisation);
    }

    /**
     * @param int     $optInId
     * @param bool    $enable
     * @param Contact $contact
     *
     * @return void
     */
    public function updateOptInForContact($optInId, $enable, Contact $contact)
    {
        $optIn      = $this->findEntityById('optIn', $optInId);
        $collection = new ArrayCollection();
        $collection->add($optIn);

        if ($enable) {
            $contact->addOptIn($collection);
        } else {
            $contact->removeOptIn($collection);
        }

        $this->updateEntity($contact);
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
     * @param GeneralService $generalService
     */
    public function setGeneralService($generalService)
    {
        $this->generalService = $generalService;
    }

    /**
     * @return GeneralService
     */
    public function getGeneralService()
    {
        if (!$this->generalService instanceof GeneralService) {
            $this->setGeneralService($this->getServiceLocator()->get('general_general_service'));
        }

        return $this->generalService;
    }

    /**
     * @param MeetingService $meetingService
     */
    public function setMeetingService($meetingService)
    {
        $this->meetingService = $meetingService;
    }

    /**
     * @return MeetingService
     */
    public function getMeetingService()
    {
        if (!$this->meetingService instanceof MeetingService) {
            $this->setMeetingService($this->getServiceLocator()->get('event_meeting_service'));
        }

        return $this->meetingService;
    }

    /**
     * @param DeeplinkService $deeplinkService
     */
    public function setDeeplinkService($deeplinkService)
    {
        $this->deeplinkService = $deeplinkService;
    }

    /**
     * @return DeeplinkService
     */
    public function getDeeplinkService()
    {
        if (!$this->deeplinkService instanceof DeeplinkService) {
            $this->setDeeplinkService($this->getServiceLocator()->get('deeplink_deeplink_service'));
        }

        return $this->deeplinkService;
    }

    /**
     * @param \Contact\Options\CommunityOptionsInterface $communityOptions
     */
    public function setCommunityOptions($communityOptions)
    {
        $this->communityOptions = $communityOptions;
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
     * @param UserServiceOptionsInterface $zfcUserOptions
     */
    public function setZfcUserOptions($zfcUserOptions)
    {
        $this->zfcUserOptions = $zfcUserOptions;
    }

    /**
     * get service options
     *
     * @return UserServiceOptionsInterface
     */
    public function getZfcUserOptions()
    {
        if (!$this->zfcUserOptions instanceof UserServiceOptionsInterface) {
            $this->setZfcUserOptions($this->getServiceLocator()->get('zfcuser_module_options'));
        }

        return $this->zfcUserOptions;
    }
}
