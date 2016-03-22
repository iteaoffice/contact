<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Contact\Service;

use Affiliation\Entity\Affiliation;
use Affiliation\Service\AffiliationService;
use Calendar\Entity\Calendar;
use Contact\Entity\AddressType;
use Contact\Entity\Contact;
use Contact\Entity\ContactOrganisation;
use Contact\Entity\Facebook;
use Contact\Entity\Note;
use Contact\Entity\OptIn;
use Contact\Entity\Phone;
use Contact\Entity\PhoneType;
use Contact\Entity\Selection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\QueryBuilder;
use Event\Entity\Booth\Booth;
use General\Entity\Country;
use General\Entity\Gender;
use General\Entity\Title;
use Organisation\Entity\Organisation;
use Organisation\Entity\Web;
use Organisation\Service\OrganisationService;
use Project\Entity\Project;
use Project\Service\ProjectService;
use Zend\Crypt\Password\Bcrypt;
use Zend\Validator\EmailAddress;

/**
 * ContactService.
 *
 * this is a generic wrapper service for all the other services
 *
 * First parameter of all methods (lowercase, underscore_separated)
 * will be used to fetch the correct model service, one exception is the 'linkModel'
 * method.
 */
class ContactService extends ServiceAbstract
{
    /**
     * Constant to determine which affiliations must be taken from the database.
     */
    const WHICH_ALL = 1;
    const WHICH_ONLY_ACTIVE = 2;
    const WHICH_ONLY_EXPIRED = 3;


    /**
     * @var Contact[]
     */
    protected $contacts = [];

    /**
     * This function returns the contact by the hash. The hash has as format contactId-CHECKSUM which needs to be checked.
     *
     * @param $hash
     *
     * @return null|Contact
     */
    public function findContactByHash($hash)
    {
        list($contactId, $hash) = explode('-', $hash);

        $contact = $this->setContactId($contactId)->getContact();

        if ($contact->parseHash() !== $hash) {
            return null;
        }

        return $contact;
    }

    /**
     * @param int $which
     *
     * @return int
     */
    public function getAffiliationCount($which = AffiliationService::WHICH_ALL)
    {
        return ($this->getContact()->getAffiliation()->filter(function (Affiliation $affiliation) use ($which) {
            switch ($which) {
                case AffiliationService::WHICH_ONLY_ACTIVE:
                    return is_null($affiliation->getDateEnd());
                case AffiliationService::WHICH_ONLY_INACTIVE:
                    return !is_null($affiliation->getDateEnd());
                default:
                    return true;
            }

        })->count());
    }

    /** @param int $id
     * @return ContactService;
     */
    public function setContactId($id)
    {
        $this->setContact($this->findEntityById('contact', $id));

        return $this;
    }

    /**
     * @return array
     */
    public function toFormValueOptions()
    {
        $contacts = [];
        foreach ($this->contacts as $contact) {
            $contacts[$contact->getId()] = $contact->getFormName();
        }
        asort($contacts);

        return $contacts;
    }

    /**
     * @return \Doctrine\ORM\Query
     */
    public function findAllContacts()
    {
        return $this->getEntityManager()->getRepository(Contact::class)->findContacts();
    }

    /**
     * Find all contacts which are active and have a date of birth.
     *
     * @return Contact[]
     */
    public function findContactsWithDateOfBirth()
    {
        return $this->getEntityManager()->getRepository(Contact::class)->findContactsWithDateOfBirth();
    }

    /**
     * Find all contacts which are active and have a CV.
     *
     * @return Contact[]
     */
    public function findContactsWithCV()
    {
        return $this->getEntityManager()->getRepository(Contact::class)->findContactsWithCV();
    }

    /**
     * Find all contacts which are active and have a CV.
     *
     * @param  bool $onlyPublic
     *
     * @return Contact[]
     */
    public function findContactsWithActiveProfile($onlyPublic = true)
    {
        return $this->getEntityManager()->getRepository(Contact::class)->findContactsWithActiveProfile($onlyPublic);
    }

    /**
     * Find a list of upcoming meetings were a user has not registered yet.
     *
     * @param Contact $contact
     *
     * @return \Event\Entity\Meeting\Meeting[]
     */
    public function findUnregisteredMeetingsByContact(Contact $contact)
    {
        return $this->getMeetingService()->findUnregisteredMeetingsByContact($contact);
    }

    /**
     * Find a list of upcoming meetings were a user can registerre
     *
     * @param Contact $contact
     *
     * @return \Event\Entity\Meeting\Meeting[]
     */
    public function findUpcomingMeetingsByContact(Contact $contact)
    {
        return $this->getMeetingService()->findUpcomingMeetingByContact($contact);
    }

    /**
     * Get the last name.
     *
     * @return string
     */
    public function parseLastName()
    {
        return trim(implode(' ', [$this->getContact()->getMiddleName(), $this->getContact()->getLastName()]));
    }

    /**
     * Create the attention of a contact.
     *
     * @return string
     */
    public function parseAttention()
    {
        /*
         * Return nothing when the contact object is created and does not have all the relevant information
         */
        if (is_null($this->getContact()->getTitle()) || is_null($this->getContact()->getGender())) {
            return '';
        }

        if (!is_null($this->getContact()->getTitle()->getAttention())) {
            return $this->getContact()->getTitle()->getAttention();
        } elseif ((int)$this->getContact()->getGender()->getId() !== 0) {
            return $this->getContact()->getGender()->getAttention();
        }

        return '';
    }

    /**
     * Dedicated function to have the organisation of a contact (or null).
     *
     * @return null|string
     */
    public function parseOrganisation()
    {
        if (!$this->hasOrganisation()) {
            return null;
        }

        return $this->findOrganisationService()->parseOrganisationWithBranch($this->getContact()
            ->getContactOrganisation()->getBranch());
    }

    /**
     * Boolean value to check if  contact has a contactOrganisation (and is thus linked to an organisation.
     *
     * @return bool
     */
    public function hasOrganisation()
    {
        return !is_null($this->getContact()->getContactOrganisation());
    }

    /**
     * @return bool
     */
    public function isFunder()
    {
        return !is_null($this->getContact()->getFunder());
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return is_null($this->getContact()->getDateEnd());
    }

    /**
     * @return OrganisationService|null
     */
    public function findOrganisationService()
    {
        /*
         * Return null when the contactOrganisation is not defined
         */
        if (!$this->hasOrganisation()) {
            return null;
        }

        return $this->getOrganisationService()->setOrganisationId($this->getContact()->getContactOrganisation()
            ->getOrganisation()->getId());
    }

    /**
     * Dedicated function to have the organisation of a contact (or null).
     *
     * @return null|Country
     */
    public function parseCountry()
    {
        if (!$this->hasOrganisation()) {
            return null;
        }

        return $this->getContact()->getContactOrganisation()->getOrganisation()->getCountry();
    }

    /**
     * Find the mail address of a contact.
     *
     * @throws \InvalidArgumentException
     *
     * @return AddressService
     */
    public function getMailAddress()
    {
        return $this->getAddressByTypeId(AddressType::ADDRESS_TYPE_MAIL);
    }

    /**
     * @param $typeId
     *
     * @return AddressService|null
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function getAddressByTypeId($typeId)
    {
        if (is_null($this->getContact())) {
            throw new \InvalidArgumentException(sprintf("A contact should be set"));
        }

        /*
         * @var AddressType
         */
        $addressType = $this->getEntityManager()->find($this->getFullEntityName('AddressType'), $typeId);

        if (is_null($this->getContact())) {
            throw new \InvalidArgumentException(sprintf("A invalid AddressType (%s) requested", $addressType));
        }

        return $this->getAddressService()->findAddressByContactAndType($this->getContact(), $addressType);
    }


    /**
     * Find the visit address of a contact.
     *
     * @throws \InvalidArgumentException
     *
     * @return AddressService
     */
    public function getVisitAddress()
    {
        return $this->getAddressByTypeId(AddressType::ADDRESS_TYPE_VISIT);
    }

    /**
     * Find the financial address of a contact.
     *
     * @throws \InvalidArgumentException
     *
     * @return AddressService
     */
    public function getFinancialAddress()
    {
        return $this->getAddressByTypeId(AddressType::ADDRESS_TYPE_FINANCIAL);
    }

    /**
     * Find the financial address of a contact.
     *
     * @throws \InvalidArgumentException
     *
     * @return AddressService
     */
    public function getBoothFinancialAddress()
    {
        return $this->getAddressByTypeId(AddressType::ADDRESS_TYPE_BOOTH_FINANCIAL);
    }

    /**
     * Find the direct phone number of a contact.
     *
     * @return Phone
     *
     * @throws \InvalidArgumentException
     */
    public function getDirectPhone()
    {
        if (is_null($this->getContact())) {
            throw new \InvalidArgumentException(sprintf("A contact should be set"));
        }

        return $this->getPhoneByContactAndType($this->getContact(), PhoneType::PHONE_TYPE_DIRECT);
    }

    /**
     * @param Contact $contact
     * @param int     $type
     *
     * @return null|Phone
     */
    private function getPhoneByContactAndType(Contact $contact, $type)
    {
        if (!in_array($type, PhoneType::getPhoneTypes())) {
            throw new \InvalidArgumentException(sprintf("A invalid phone type chosen"));
        }

        return $this->getEntityManager()->getRepository($this->getFullEntityName('phone'))->findOneBy([
            'contact' => $contact,
            'type'    => $type,
        ]);
    }

    /**
     * Find the mobile phone number of a contact.
     *
     * @return Phone
     *
     * @throws \InvalidArgumentException
     */
    public function getMobilePhone()
    {
        if (is_null($this->getContact())) {
            throw new \InvalidArgumentException(sprintf("A contact should be set"));
        }

        return $this->getPhoneByContactAndType($this->getContact(), PhoneType::PHONE_TYPE_MOBILE);
    }

    /**
     * @return \Project\Service\ProjectService[]
     */
    public function findProjects()
    {
        return $this->getProjectService()->findProjectByContact($this->getContact());
    }


    /**
     * Create an account and send an email address.
     *
     * @param string $emailAddress
     * @param string $firstName
     * @param string $middleName
     * @param string $lastName
     *
     * @return Contact
     */
    public function register($emailAddress, $firstName, $middleName, $lastName)
    {
        //Create the account
        $contact = new Contact();
        $contact->setEmail($emailAddress);
        $contact->setFirstName($firstName);
        if (strlen($middleName) > 0) {
            $contact->setMiddleName($middleName);
        }

        $contact->setLastName($lastName);
        //Fix the gender
        $contact->setGender($this->getGeneralService()->findEntityById('gender', Gender::GENDER_UNKNOWN));
        $contact->setTitle($this->getGeneralService()->findEntityById('title', Title::TITLE_UNKNOWN));
        /*
         * Include all the optIns
         */
        $contact->setOptIn($this->getEntityManager()->getRepository('Contact\Entity\OptIn')
            ->findBy(['autoSubscribe' => OptIn::AUTO_SUBSCRIBE]));
        /**
         * @var $contact Contact
         */
        $contact = $this->newEntity($contact);
        //Create a target
        $target = $this->getDeeplinkService()->createTargetFromRoute('community/contact/profile/view');
        //Create a deep link for the user which redirects to the profile-page
        $deeplink = $this->getDeeplinkService()->createDeeplink($target, $contact);
        $email = $this->getEmailService()->create();
        $this->getEmailService()->setTemplate("/auth/register:mail");
        $email->setDisplayName($contact->getDisplayName());
        $email->addTo($emailAddress);
        $email->setUrl($this->getDeeplinkService()->parseDeeplinkUrl($deeplink));
        $this->getEmailService()->send();

        return $contact;
    }

    /**
     * Create an account and send an email address.
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
        $contact = $this->findContactByEmail($emailAddress, true);
        if (is_null($contact)) {
            throw new \InvalidArgumentException(sprintf(
                "The contact with emailAddress %s cannot be found",
                $emailAddress
            ));
        }
        $contactService = $this->createServiceElement($contact);
        //Create a target
        $target = $this->getDeeplinkService()->createTargetFromRoute('community/contact/change-password');
        //Create a deeplink for the user which redirects to the profile-page
        $deeplink = $this->getDeeplinkService()->createDeeplink($target, $contact);
        /*
         * Send the email tot he user
         */
        $email = $this->getEmailService()->create();
        $this->getEmailService()->setTemplate("/auth/forgotpassword:mail");
        $email->addTo($emailAddress, $contactService->parseFullName());
        $email->setFullname($contactService->parseFullName());
        $email->setUrl($this->getDeeplinkService()->parseDeeplinkUrl($deeplink));
        $this->getEmailService()->send();

        return $contact;
    }

    /**
     * @param      $email
     * @param bool $onlyMain
     *
     * @return null|Contact
     */
    public function findContactByEmail($email, $onlyMain = false)
    {
        return $this->getEntityManager()->getRepository(Contact::class)->findContactByEmail($email, $onlyMain);
    }

    /**
     * @param $name
     *
     * @return null|Selection
     */
    public function findSelectionByName($name)
    {
        return $this->getEntityManager()->getRepository(Selection::class)->findOneBy([
            'selection' => $name,
        ]);
    }

    /**
     * @param Contact $contact
     *
     * @return ContactService
     */
    private function createServiceElement(Contact $contact)
    {
        $contactService = clone $this;
        $contactService->setContact($contact);

        return $contactService;
    }

    /**
     * Parse the full name of a project.
     *
     * @return string
     */
    public function parseFullName()
    {
        return $this->getContact()->getDisplayName();
    }

    /**
     * Find the relevant items out of the notes tree.
     *
     * @return string
     */
    public function parseSignature()
    {
        /*
         * Go over the notes and find the signature of the contact
         */
        foreach ($this->contact->getNote() as $note) {
            if ($note->getSource() === Note::SOURCE_SIGNATURE) {
                return $note->getNote();
            }
        }
    }

    /**
     * Return true or false depending if a user is in the community.
     *
     * @param Contact $contact
     *
     * @return bool
     */
    public function isCommunity(Contact $contact)
    {
        return $this->getEntityManager()->getRepository(Contact::class)
            ->findIsCommunityMember($contact, $this->getModuleOptions());
    }


    /**
     * @param Contact         $contact
     * @param array|Selection $selections
     *
     * @return bool
     */
    public function contactInSelection(Contact $contact, $selections)
    {
        if (!is_array($selections) && !$selections instanceof PersistentCollection) {
            $selections = [$selections];
        }
        foreach ($selections as $selection) {
            if (!$selection instanceof Selection) {
                throw new \InvalidArgumentException("Selection should be instance of Selection");
            }
            if (is_null($selection->getId())) {
                throw new \InvalidArgumentException("The given selection cannot be empty");
            }
            if ($this->findContactInSelection($contact, $selection)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Contact   $contact
     * @param Selection $selection
     *
     * @return bool
     */
    public function findContactInSelection(Contact $contact, Selection $selection)
    {
        if (!is_null($selection->getSql())) {
            try {
                //We have a dynamic query, check if the contact is in the selection
                return $this->getEntityManager()->getRepository(Contact::class)
                    ->isContactInSelectionSQL($contact, $selection->getSql());
            } catch (\Exception $e) {
                print sprintf("Selection %s is giving troubles ()", $selection->getId(), $e->getMessage());
            }
        }
        /*
         * The selection contains contacts, do an extra query to find the contact
         */
        if (sizeof($selection->getSelectionContact()) > 0) {
            $findContact = $this->getEntityManager()->getRepository($this->getFullEntityName('SelectionContact'))
                ->findOneBy([
                    'contact'   => $contact,
                    'selection' => $selection,
                ]);
            /*
             * Return true when we found a contact
             */
            if (!is_null($findContact)) {
                return true;
            }
        }
    }

    /**
     * Returns of contact is:
     * - Associate
     * - Affiliate
     * - Project leader
     * - Work package leader.
     *
     * @param Contact $contact
     * @param Project $project
     *
     * @return bool
     */
    public function isContactInProject(Contact $contact, Project $project)
    {
        $this->getProjectService()->setProject($project);
        $projectContacts = $this->findContactsInProject($this->getProjectService());

        return array_key_exists($contact->getId(), $projectContacts);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return is_null($this->contact) || is_null($this->contact->getId());
    }

    /**
     * Produce a list of contacts which are active in a project.
     *
     * @param ProjectService $projectService
     *
     * @return Contact[]
     *
     * @throws \InvalidArgumentException
     */
    public function findContactsInProject(ProjectService $projectService)
    {
        /*
         * Throw an exception when no project is selected
         */
        if (is_null($projectService->getProject())) {
            throw new \InvalidArgumentException(sprintf("No project selected"));
        }
        $contacts = [];
        /*
         * Add the project leader
         */
        $contacts[$projectService->getProject()->getContact()->getId()] = $projectService->getProject()->getContact();
        /*
         * Add the contacts form the affiliations and the associates
         */
        foreach ($projectService->getProject()->getAffiliation() as $affiliation) {
            $contacts[$affiliation->getContact()->getId()] = $affiliation->getContact();
            foreach ($affiliation->getAssociate() as $associate) {
                $contacts[$associate->getId()] = $associate;
            }
        }
        /*
         * Add the workpackage leaders
         */
        foreach ($projectService->getProject()->getWorkpackage() as $workpackage) {
            $contacts[$workpackage->getContact()->getId()] = $workpackage->getContact();
        }

        return $contacts;
    }

    /**
     * returns true when the contact is in the booth.
     *
     * @param Contact $contact
     * @param Booth   $booth
     *
     * @return bool
     */
    public function isContactInBooth(Contact $contact, Booth $booth)
    {
        foreach ($booth->getBoothContact() as $boothContact) {
            if ($contact == $boothContact->getContact()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Contact  $contact
     * @param Facebook $facebook
     *
     * @return bool
     */
    public function isContactInFacebook(Contact $contact, Facebook $facebook)
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName('facebook'))
            ->isContactInFacebook($contact, $facebook);
    }

    /**
     * @param Contact $contact
     * @param         $role
     * @param         $entity
     *
     * @return bool
     */
    public function contactHasPermit(Contact $contact, $role, $entity)
    {
        if (is_null($entity)) {
            throw new \InvalidArgumentException("Permit can only be determined of an existing entity, null given");
        }

        return $this->getAdminService()->contactHasPermit(
            $contact,
            $role,
            str_replace('doctrineormmodule_proxy___cg___', '', strtolower($entity->get('underscore_full_entity_name'))),
            $entity->getId()
        );
    }

    /**
     * @param Selection $selection
     * @param bool      $toArray
     *
     * @return Contact[]
     */
    public function findContactsInSelection(Selection $selection, $toArray = false)
    {
        /*
         * A selection can have 2 methods, either SQL or a contacts. We need to query both
         */
        if (!is_null($selection->getSql())) {
            //We have a dynamic query, check if the contact is in the selection
            return $this->getEntityManager()->getRepository(Contact::class)
                ->findContactsBySelectionSQL($selection->getSql(), $toArray);
        } else {
            return $this->getEntityManager()->getRepository(Contact::class)->findContactsBySelectionContact($selection);
        }
    }

    /**
     * @param Selection $selection
     *
     * @return Contact[]
     */
    public function findContactsInSelectionAsArray(Selection $selection)
    {
        /*
         * A selection can have 2 methods, either SQL or a contacts. We need to query both
         */
        if (!is_null($selection->getSql())) {
            //We have a dynamic query, check if the contact is in the selection
            $contacts = $this->getEntityManager()->getRepository(Contact::class)
                ->findContactsBySelectionSQL($selection->getSql(), true);
        } else {
            $contacts = $this->getEntityManager()->getRepository(Contact::class)
                ->findContactsBySelectionContact($selection, true);
        }

        return $contacts;
    }

    /**
     * Get a list of facebooks by contact (based on the access role).
     *
     * @param Contact $contact
     *
     * @return Facebook[]
     */
    public function findFacebookByContact(Contact $contact)
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName('facebook'))
            ->findFacebookByContact($contact);
    }

    /**
     * @param Facebook $facebook
     *
     * @return Contact[]
     */
    public function findContactsInFacebook(Facebook $facebook)
    {

        /*
         * This function has a special feature to fill the array with contacts.
         * We can for instance try to find the country, organisation or position
         *
         * A dedicated array will therefore be created
         */
        $contacts = [];
        foreach ($this->getEntityManager()->getRepository(Contact::class)->findContactsInFacebook($facebook) as $contact) {
            $singleContact = [];

            $singleContact['contact'] = $contact;
            $singleContact['title'] = $this->facebookTitleParser($facebook->getTitle(), $contact);
            $singleContact['subTitle'] = $this->facebookTitleParser($facebook->getSubtitle(), $contact);
            $singleContact['email'] = $contact->getEmail();
            $singleContact['phone'] = $this->getPhoneByContactAndType($contact, PhoneType::PHONE_TYPE_DIRECT);
            $singleContact['mobile'] = $this->getPhoneByContactAndType($contact, PhoneType::PHONE_TYPE_MOBILE);

            $contacts[] = $singleContact;
        }


        return $contacts;
    }

    /**
     * Special function which translates the output of the facebook to a known value.
     *
     * @param         $titleGetter
     * @param Contact $contact
     *
     * @return string
     */
    private function facebookTitleParser($titleGetter, Contact $contact)
    {
        if (strlen($titleGetter) === 0) {
            return '';
        }

        //Format the $getter
        switch (intval($titleGetter)) {
            case Facebook::DISPLAY_ORGANISATION:
                if (is_null($contact->getContactOrganisation())) {
                    return 'Unknown';
                }

                return (string)$contact->getContactOrganisation()->getOrganisation();
            case Facebook::DISPLAY_COUNTRY:
                if (is_null($contact->getContactOrganisation())) {
                    return 'Unknown';
                }

                return (string)$contact->getContactOrganisation()->getOrganisation()->getCountry();
            case Facebook::DISPLAY_PROJECTS:
                $projects = [];

                /*
                 * We need to construct the link to the project, so use the linkParser here.
                 *
                 * @var ProjectLink
                 */
                $projectLink = $this->getServiceLocator()->get('ViewHelperManager')->get('projectLink');

                foreach ($this->getProjectService()->findProjectsByProjectContact($contact) as $project) {
                    $projectService = $this->getProjectService()->setProject($project);
                    $projects[] = $projectLink($projectService, 'view', 'name-without-number');
                }

                return implode(', ', $projects);
            case Facebook::DISPLAY_POSITION:
                return $contact->getPosition();
            case Facebook::DISPLAY_NONE:
            default:
                return '';
        }
    }

    /**
     * Update the password for a contact. Check with the current password when given
     * New accounts have no password so this check is not always needed.
     *
     * @param string  $password
     * @param Contact $contact
     *
     * @return bool
     */
    public function updatePasswordForContact(
        $password,
        Contact $contact
    ) {
        $Bcrypt = new Bcrypt();
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
     * contactOrganisation information.
     *
     * $contactOrganisation['organisation_id'] > id of the chosen organisation
     * $contactOrganisation['branch'] > value of the branch (if an organisation_id is chosen)
     * $contactOrganisation['organisation'] > Name of the organisation
     * $contactOrganisation['country'] > CountryId
     *
     * @param Contact $contact
     * @param array   $contactOrganisation
     */
    public function updateContactOrganisation(
        Contact $contact,
        array $contactOrganisation
    ) {
        /**
         * Find the current contactOrganisation, or create a new one if this empty (in case of a new contact)
         */
        $currentContactOrganisation = $contact->getContactOrganisation();
        if (is_null($currentContactOrganisation)) {
            $currentContactOrganisation = new ContactOrganisation();
            $currentContactOrganisation->setContact($contact);
        }

        /**
         * The trigger for this update is the presence of a $contactOrganisation['organisation_id'].
         * If this value != 0, a choice has been made from the dropdown and we will then take the branch as default
         */
        if (isset($contactOrganisation['organisation_id']) && $contactOrganisation['organisation_id'] != '0') {
            $organisation = $this->getOrganisationService()
                ->findEntityById('organisation', (int)$contactOrganisation['organisation_id']);
            $currentContactOrganisation->setOrganisation($organisation);
            //Take te branch form the form element ($contactOrganisation['branch'])
            if (!empty($contactOrganisation['branch'])) {
                $currentContactOrganisation->setBranch($contactOrganisation['branch']);
            } else {
                $currentContactOrganisation->setBranch(null);
            }
        } else {
            /**
             * No organisation is chosen (the option 'none of the above' was taken, so create the organisation
             */

            /**
             * Don't do anything when the organisationName = empty
             */
            if (empty($contactOrganisation['organisation'])) {
                return;
            }
            $country = $this->getGeneralService()->findEntityById('country', (int)$contactOrganisation['country']);

            /*
             * Look for the organisation based on the name (without branch) and country + email
             */
            $organisation = $this->getOrganisationService()
                ->findOrganisationByNameCountryAndEmailAddress(
                    $contactOrganisation['organisation'],
                    $country,
                    $contact->getEmail()
                );
            $organisationFound = false;
            /*
             * We did not find an organisation, so we need to create it
             */
            if (sizeof($organisation) === 0) {
                $organisation = new Organisation();
                $organisation->setOrganisation($contactOrganisation['organisation']);
                $organisation->setCountry($country);
                $organisation->setType($this->organisationService->findEntityById('Type', 0)); //Unknown
                /*
                 * Add the domain in the saved domains for this new company
                 * Use the ZF2 EmailAddress validator to strip the hostname out of the EmailAddress
                 */
                $validateEmail = new EmailAddress();
                $validateEmail->isValid($contact->getEmail());
                $organisationWeb = new Web();
                $organisationWeb->setOrganisation($organisation);
                $organisationWeb->setWeb($validateEmail->hostname);
                $organisationWeb->setMain(Web::MAIN);

                //Skip hostnames like yahoo, gmail and hotmail, outlook
                if (!in_array($organisation->getWeb(), ['gmail.com', 'hotmail.com', 'outlook.com', 'yahoo.com'])) {
                    $this->getOrganisationService()->newEntity($organisationWeb);
                }

                $currentContactOrganisation->setOrganisation($organisation);
            } else {
                $foundOrganisation = null;
                /*
                 * Go over the found organisation to match the branching
                 */
                foreach ($organisation as $foundOrganisation) {
                    /*
                     * Stop when we have found an exact match and reset the branch if set
                     */
                    if ($foundOrganisation->getOrganisation() === $contactOrganisation['organisation']
                        && $country->getId() === $foundOrganisation->getCountry()->getId()
                    ) {
                        $currentContactOrganisation->setOrganisation($foundOrganisation);
                        $currentContactOrganisation->setBranch(null);
                        break;
                    }
                    if (!$organisationFound) {
                        //Create only a branch when the name is found and the given names do not match in length
                        if (strlen($foundOrganisation->getOrganisation())
                            < (strlen($contactOrganisation['organisation'])
                                - strlen($currentContactOrganisation->getBranch()))
                        ) {
                            $currentContactOrganisation->setBranch(str_replace(
                                $contactOrganisation['organisation'],
                                '~',
                                $foundOrganisation->getOrganisation()
                            ));
                        } else {
                            //Reset the branch otherwise
                            $currentContactOrganisation->setBranch(null);
                        }
                        /*
                         * We have found a match of the organisation in the string and
                         */
                        $organisationFound = true;
                    }
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
     */
    public function updateOptInForContact(
        $optInId,
        $enable,
        Contact $contact
    ) {
        $optIn = $this->findEntityById('optIn', $optInId);
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
     * @param OptIn $optIn
     *
     * @return Contact[]
     */
    public function findContactsByOptInAsArray(OptIn $optIn)
    {
        return $this->getEntityManager()->getRepository(Contact::class)->findContactsByOptIn($optIn, true);
    }

    /**
     * @param int     $optInId
     * @param Contact $contact
     *
     * @return OptIn
     */
    public function hasOptInEnabledByContact($optInId, Contact $contact)
    {
        /*
         * The OptIn is a n:m entity, so we just check if the contact has the optIn
         */
        foreach ($contact->getOptIn() as $optIn) {
            if ($optIn->getId() === $optInId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Search for contacts based on a search-item.
     *
     * @param $searchItem
     *
     * @return QueryBuilder;
     */
    public function searchContacts($searchItem)
    {
        return $this->getEntityManager()->getRepository(Contact::class)->searchContacts($searchItem);
    }


    /**
     * Returns which template is to  be used for facebook
     *
     * @return string
     */
    public function getFacebookTemplate()
    {
        return $this->getModuleOptions()->getFacebookTemplate();
    }

    /**
     * Create an array with the incomplete items in the profile and the relative weight.
     *
     * @param Contact $contact
     *
     * @return array;
     */
    public function getProfileInCompleteness(Contact $contact)
    {
        $inCompleteness = [];
        $totalWeight = 0;
        $totalWeight += 10;
        if (is_null($contact->getFirstName())) {
            $inCompleteness['firstName']['message'] = _("txt-first-name-is-missing");
            $inCompleteness['firstName']['weight'] = 10;
        }
        $totalWeight += 10;
        if (is_null($contact->getLastName())) {
            $inCompleteness['lastName']['message'] = _("txt-last-name-is-missing");
            $inCompleteness['lastName']['weight'] = 10;
        }
        $totalWeight += 10;
        if (sizeof($contact->getPhone()) === 0) {
            $inCompleteness['phone']['message'] = _("txt-no-telephone-number-known");
            $inCompleteness['phone']['weight'] = 10;
        }
        $totalWeight += 10;
        if (sizeof($contact->getAddress()) === 0) {
            $inCompleteness['address']['message'] = _("txt-no-address-known");
            $inCompleteness['address']['weight'] = 10;
        }
        $totalWeight += 10;
        if (sizeof($contact->getPhoto()) === 0) {
            $inCompleteness['photo']['message'] = _("txt-no-profile-photo-given");
            $inCompleteness['photo']['weight'] = 10;
        }
        $totalWeight += 10;
        if (is_null($contact->getSaltedPassword())) {
            $inCompleteness['password']['message'] = _("txt-no-password-given");
            $inCompleteness['password']['weight'] = 10;
        }
        $totalWeight += 20;
        if (is_null($contact->getContactOrganisation()) === 0) {
            $inCompleteness['organisation']['message'] = _("txt-no-organisation-known");
            $inCompleteness['organisation']['weight'] = 20;
        }
        /*
         * Determine the total weight
         */
        $incompletenessWeight = 0;
        /*
         * Update the values in the table to create a total weight of 100%
         */
        foreach ($inCompleteness as &$itemPerType) {
            $itemPerType['weight'] = ($itemPerType['weight'] / $totalWeight * 100);
            $incompletenessWeight += $itemPerType['weight'];
        }

        return [
            'items'                => $inCompleteness,
            'incompletenessWeight' => $incompletenessWeight,
        ];
    }

    /**
     * @param Affiliation $affiliation
     *
     * @return array
     */
    public function findContactsInAffiliation(Affiliation $affiliation)
    {
        $contacts = [];
        $contactRole = [];


        /*
         * Add the technical contact
         */
        $contacts[$affiliation->getContact()->getId()] = $affiliation->getContact();
        $contactRole[$affiliation->getContact()->getId()][] = 'Technical Contact';

        /*
         * Add the financial contact
         */
        if (!is_null($affiliation->getFinancial())) {
            $contacts[$affiliation->getFinancial()->getContact()->getId()] = $affiliation->getFinancial()->getContact();
            $contactRole[$affiliation->getFinancial()->getContact()->getId()][] = 'Financial Contact';
        }

        /*
         * Add the associates
         */
        foreach ($affiliation->getAssociate() as $associate) {
            /*
             * Add the associates
             */
            $contacts[$associate->getId()] = $associate;
            $contactRole[$associate->getId()][] = 'Associate';
        }

        /*
         * Add the workpackage leaders
         */
        foreach ($affiliation->getProject()->getWorkpackage() as $workpackage) {
            /*
             * Add the work package leaders
             */
            if (!is_null($workpackage->getContact()->getContactOrganisation())
                && $workpackage->getContact()->getContactOrganisation()->getOrganisation()->getId()
                === $affiliation->getOrganisation()->getId()
            ) {
                $contacts[$workpackage->getContact()->getId()] = $workpackage->getContact();
                $contactRole[$workpackage->getContact()->getId()][] = 'Workpackage leader';
            }
        }

        $contactRole = array_map('array_unique', $contactRole);

        //Store the values local for the use of the toArray function
        $this->contacts = $contacts;

        return ['contacts' => $contacts, 'contactRole' => $contactRole];
    }

    /**
     * @param Calendar $calendar
     *
     * @return Contact[]
     *
     * @throws \Exception
     */
    public function findPossibleContactByCalendar(Calendar $calendar)
    {
        if (is_null($calendar->getProjectCalendar())) {
            throw new \Exception("A projectCalendar is required to find the contacts");
        }

        return $this->getEntityManager()->getRepository(Contact::class)->findPossibleContactByCalendar($calendar);
    }

    /**
     * @param Organisation $organisation
     *
     * @return Contact[]
     */
    public function findContactsInOrganisation(Organisation $organisation)
    {
        return $this->getEntityManager()->getRepository(Contact::class)->findContactsInOrganisation($organisation);
    }
}
