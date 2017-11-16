<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Service;

use Affiliation\Entity\Affiliation;
use Calendar\Entity\Calendar;
use Contact\Entity\Address;
use Contact\Entity\AddressType;
use Contact\Entity\Contact;
use Contact\Entity\ContactOrganisation;
use Contact\Entity\Facebook;
use Contact\Entity\Note;
use Contact\Entity\OptIn;
use Contact\Entity\Phone;
use Contact\Entity\PhoneType;
use Contact\Entity\Selection;
use Contact\Entity\SelectionContact;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Event\Entity\Booth\Booth;
use General\Entity\Country;
use General\Entity\Gender;
use General\Entity\Title;
use Organisation\Entity\Organisation;
use Organisation\Service\OrganisationService;
use Project\Entity\Project;
use Zend\Crypt\Password\Bcrypt;

/**
 * Class ContactService
 * @package Contact\Service
 */
class ContactService extends ServiceAbstract
{
    /**
     * Constant to determine which affiliations must be taken from the database.
     */
    public const WHICH_ALL = 1;
    public const WHICH_ONLY_ACTIVE = 2;
    public const WHICH_ONLY_EXPIRED = 3;


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
    public function findContactByHash($hash): ?Contact
    {
        list($contactId, $hash) = explode('-', $hash);

        /** @var Contact $contact */
        $contact = $this->getEntityManager()->find(Contact::class, $contactId);

        if ($contact->parseHash() !== $hash) {
            return null;
        }

        return $contact;
    }

    /**
     * @param int $id
     *
     * @return Contact|null|object
     */
    public function findContactById($id): ?Contact
    {
        return $this->getEntityManager()->find(Contact::class, $id);
    }

    /**
     * @param Contact[] $contacts
     *
     * @return array $return
     */
    public function toFormValueOptions(array $contacts): array
    {
        $return = [];
        foreach ($contacts as $contact) {
            $return[$contact->getId()] = $contact->getFormName();
        }
        asort($return);

        return $return;
    }

    /**
     * @return \Doctrine\ORM\Query
     */
    public function findAllContacts(): \Doctrine\ORM\Query
    {
        /** @var \Contact\Repository\Contact $repository */
        $repository = $this->getEntityManager()->getRepository(Contact::class);

        return $repository->findContacts();
    }

    /**
     * Find all contacts which are active and have a date of birth.
     *
     * @return Contact[]
     */
    public function findContactsWithDateOfBirth()
    {
        /** @var \Contact\Repository\Contact $repository */
        $repository = $this->getEntityManager()->getRepository(Contact::class);

        return $repository->findContactsWithDateOfBirth();
    }

    /**
     * Find all contacts which are active and have a CV.
     *
     * @return Contact[]
     */
    public function findContactsWithCV()
    {
        /** @var \Contact\Repository\Contact $repository */
        $repository = $this->getEntityManager()->getRepository(Contact::class);

        return $repository->findContactsWithCV();
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
        /** @var \Contact\Repository\Contact $repository */
        $repository = $this->getEntityManager()->getRepository(Contact::class);

        return $repository->findContactsWithActiveProfile($onlyPublic);
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
     * @param Contact $contact
     *
     * @return string
     */
    public function parseLastName(Contact $contact)
    {
        return trim(implode(' ', [$contact->getMiddleName(), $contact->getLastName()]));
    }

    /**
     * Create the attention of a contact.
     *
     * @param Contact $contact
     *
     * @return string
     */
    public function parseAttention(Contact $contact): string
    {
        /*
         * Return nothing when the contact object is created and does not have all the relevant information
         */
        if (\is_null($contact->getTitle()) || \is_null($contact->getGender())) {
            return '';
        }

        if (!\is_null($contact->getTitle()->getAttention())) {
            return $contact->getTitle()->getAttention();
        }

        if ((int)$contact->getGender()->getId() !== 0) {
            return $contact->getGender()->getAttention();
        }

        return '';
    }

    /**
     * Dedicated function to have the organisation of a contact (or null).
     *
     * @param Contact $contact
     *
     * @return null|string
     */
    public function parseOrganisation(Contact $contact): ?string
    {
        if (!$this->hasOrganisation($contact)) {
            return null;
        }

        return $this->getOrganisationService()->parseOrganisationWithBranch(
            $contact->getContactOrganisation()->getBranch(),
            $contact->getContactOrganisation()->getOrganisation()
        );
    }

    /**
     * Boolean value to check if  contact has a contactOrganisation (and is thus linked to an organisation.
     *
     * @param Contact $contact
     *
     * @return bool
     */
    public function hasOrganisation(Contact $contact): bool
    {
        return !\is_null($contact->getContactOrganisation());
    }

    /**
     * @param Contact $contact
     *
     * @return bool
     */
    public function isFunder(Contact $contact): bool
    {
        return !\is_null($contact->getFunder());
    }

    /**
     * @param Contact $contact
     *
     * @return bool
     */
    public function isActive(Contact $contact): bool
    {
        return \is_null($contact->getDateEnd());
    }

    /**
     * Dedicated function to have the organisation of a contact (or null).
     *
     * @param Contact $contact
     *
     * @return null|Country
     */
    public function parseCountry(Contact $contact): ?Country
    {
        if (!$this->hasOrganisation($contact)) {
            return null;
        }

        return $contact->getContactOrganisation()->getOrganisation()->getCountry();
    }

    /**
     * Find the mail address of a contact.
     *
     * @param Contact $contact
     *
     *
     * @return Address
     */
    public function getMailAddress(Contact $contact): ?Address
    {
        return $this->getAddressByTypeId($contact, AddressType::ADDRESS_TYPE_MAIL);
    }

    /**
     * @param         $typeId
     *
     * @return Address|null
     *
     * @param Contact $contact
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function getAddressByTypeId(Contact $contact, $typeId): ?Address
    {
        /**
         * @var AddressType $addressType
         */
        $addressType = $this->getEntityManager()->find(AddressType::class, $typeId);

        return $this->getAddressService()->findAddressByContactAndType($contact, $addressType);
    }


    /**
     * Find the visit address of a contact.
     *
     * @throws \InvalidArgumentException
     *
     * @param Contact $contact
     *
     * @return Address
     */
    public function getVisitAddress(Contact $contact): ?Address
    {
        return $this->getAddressByTypeId($contact, AddressType::ADDRESS_TYPE_VISIT);
    }

    /**
     * Find the financial address of a contact.
     *
     * @throws \InvalidArgumentException
     *
     * @param Contact $contact
     *
     * @return Address
     */
    public function getFinancialAddress(Contact $contact): ?Address
    {
        return $this->getAddressByTypeId($contact, AddressType::ADDRESS_TYPE_FINANCIAL);
    }

    /**
     * Find the financial address of a contact.
     *
     * @throws \InvalidArgumentException
     *
     * @param Contact $contact
     *
     * @return Address
     */
    public function getBoothFinancialAddress(Contact $contact): ?Address
    {
        return $this->getAddressByTypeId($contact, AddressType::ADDRESS_TYPE_BOOTH_FINANCIAL);
    }

    /**
     * Find the direct phone number of a contact.
     *
     * @param Contact $contact
     *
     * @return Phone
     *
     * @throws \InvalidArgumentException
     */
    public function getDirectPhone(Contact $contact): ?Phone
    {
        return $this->getPhoneByContactAndType($contact, PhoneType::PHONE_TYPE_DIRECT);
    }

    /**
     * @param Contact $contact
     * @param int $type
     *
     * @return null|Phone
     */
    private function getPhoneByContactAndType(Contact $contact, $type): ?Phone
    {
        if (!\in_array($type, PhoneType::getPhoneTypes())) {
            throw new \InvalidArgumentException(sprintf("A invalid phone type chosen"));
        }

        return $this->getEntityManager()->getRepository(Phone::class)->findOneBy(
            [
                'contact' => $contact,
                'type'    => $type,
            ]
        );
    }

    /**
     * Find the mobile phone number of a contact.
     *
     * @param Contact $contact
     *
     * @return Phone
     *
     * @throws \InvalidArgumentException
     */
    public function getMobilePhone(Contact $contact): ?Phone
    {
        return $this->getPhoneByContactAndType($contact, PhoneType::PHONE_TYPE_MOBILE);
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
        $contact->setGender($this->getGeneralService()->findEntityById(Gender::class, Gender::GENDER_UNKNOWN));
        $contact->setTitle($this->getGeneralService()->findEntityById(Title::class, Title::TITLE_UNKNOWN));
        /*
         * Include all the optIns
         */
        $contact->setOptIn(
            $this->getEntityManager()->getRepository('Contact\Entity\OptIn')
                ->findBy(['autoSubscribe' => OptIn::AUTO_SUBSCRIBE])
        );
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
     * @param string $emailAddress
     * @param string $note
     * @param string|null $firstName
     * @param string|null $middleName
     * @param string|null $lastName
     * @return Contact
     */
    public function createContact(
        string $emailAddress,
        string $note = '',
        string $firstName = null,
        string $middleName = null,
        string $lastName = null
    ): Contact {
        //Create the account
        $contact = new Contact();
        $contact->setEmail($emailAddress);
        if (null !== $firstName) {
            $contact->setFirstName($firstName);
        }
        if (null !== $middleName) {
            $contact->setMiddleName($middleName);
        }
        if (null !== $lastName) {
            $contact->setLastName($lastName);
        }
        //Fix the gender
        $contact->setGender($this->getGeneralService()->findEntityById(Gender::class, Gender::GENDER_UNKNOWN));
        $contact->setTitle($this->getGeneralService()->findEntityById(Title::class, Title::TITLE_UNKNOWN));
        /*
         * Include all the optIns
         */
        $contact->setOptIn(
            $this->getEntityManager()->getRepository('Contact\Entity\OptIn')
                ->findBy(['autoSubscribe' => OptIn::AUTO_SUBSCRIBE])
        );

        /**
         * @var $contact Contact
         */
        $contact = $this->newEntity($contact);

        if (!empty($note)) {
            $this->addNoteToContact($note, 'Account creation', $contact);
        }

        return $contact;
    }

    /**
     * @param $emailAddress
     * @return Contact
     * @throws \Exception
     */
    public function lostPassword($emailAddress): Contact
    {
        //Create the account
        $contact = $this->findContactByEmail($emailAddress, true);
        if (\is_null($contact)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "The contact with emailAddress %s cannot be found",
                    $emailAddress
                )
            );
        }
        //Create a target
        $target = $this->getDeeplinkService()->createTargetFromRoute('community/contact/change-password');
        //Create a deeplink for the user which redirects to the profile-page
        $deeplink = $this->getDeeplinkService()->createDeeplink($target, $contact);
        /*
         * Send the email tot he user
         */
        $email = $this->getEmailService()->create();
        $this->getEmailService()->setTemplate("/auth/forgotpassword:mail");
        $email->addTo($emailAddress, $contact->parseFullName());
        $email->setFullname($contact->parseFullName());
        $email->setUrl($this->getDeeplinkService()->parseDeeplinkUrl($deeplink));
        $this->getEmailService()->send();

        return $contact;
    }

    /**
     * @param string $text
     * @param string $source
     * @param Contact $contact
     * @return Note
     */
    public function addNoteToContact(string $text, string $source, Contact $contact): Note
    {
        $note = new Note();
        $note->setNote($text);
        $note->setContact($contact);
        $note->setSource($source);
        $this->newEntity($note);

        return $note;
    }

    /**
     * @param $email
     * @param bool $onlyMain
     * @return Contact|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findContactByEmail($email, $onlyMain = false): ?Contact
    {
        /** @var \Contact\Repository\Contact $repository */
        $repository = $this->getEntityManager()->getRepository(Contact::class);

        return $repository->findContactByEmail($email, $onlyMain);
    }

    /**
     * @param $name
     *
     * @return null|Selection|object
     */
    public function findSelectionByName($name)
    {
        return $this->getEntityManager()->getRepository(Selection::class)->findOneBy(
            [
                'selection' => $name,
            ]
        );
    }

    /**
     * @param Contact $contact
     * @return string
     */
    public function parseSignature(Contact $contact): ?string
    {
        /*
         * Go over the notes and find the signature of the contact
         */
        foreach ($contact->getNote() as $note) {
            if ($note->getSource() === Note::SOURCE_SIGNATURE) {
                return $note->getNote();
            }
        }

        return '';
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
        /** @var \Contact\Repository\Contact $repository */
        $repository = $this->getEntityManager()->getRepository(Contact::class);

        return $repository->findIsCommunityMember($contact, $this->getModuleOptions());
    }


    /**
     * @param Contact $contact
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
            if (\is_null($selection->getId())) {
                throw new \InvalidArgumentException("The given selection cannot be empty");
            }
            if ($this->findContactInSelection($contact, $selection)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Contact $contact
     * @param Selection $selection
     *
     * @return bool
     */
    public function findContactInSelection(Contact $contact, Selection $selection)
    {
        /** @var \Contact\Repository\Contact $repository */
        $repository = $this->getEntityManager()->getRepository(Contact::class);

        if (!\is_null($selection->getSql())) {
            try {
                //We have a dynamic query, check if the contact is in the selection
                return $repository->isContactInSelectionSQL($contact, $selection->getSql());
            } catch (\Throwable $e) {
                print sprintf('Selection %s is giving troubles (%s)', $selection->getId(), $e->getMessage());
            }
        }
        /*
         * The selection contains contacts, do an extra query to find the contact
         */
        if (\count($selection->getSelectionContact()) > 0) {
            $findContact = $this->getEntityManager()->getRepository(SelectionContact::class)->findOneBy(
                [
                    'contact'   => $contact,
                    'selection' => $selection,
                ]
            );
            /*
             * Return true when we found a contact
             */
            if (!\is_null($findContact)) {
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
    public function isContactInProject(Contact $contact, Project $project): bool
    {
        $projectContacts = $this->findContactsInProject($project);

        return array_key_exists($contact->getId(), $projectContacts);
    }

    /**
     * Produce a list of contacts which are active in a project.
     *
     * @param Project $project
     *
     * @return Contact[]
     *
     * @throws \InvalidArgumentException
     */
    public function findContactsInProject(Project $project)
    {
        $contacts = [];
        /*
         * Add the project leader
         */
        $contacts[$project->getContact()->getId()] = $project->getContact();
        /*
         * Add the contacts form the affiliations and the associates
         */
        foreach ($project->getAffiliation() as $affiliation) {
            $contacts[$affiliation->getContact()->getId()] = $affiliation->getContact();
            foreach ($affiliation->getAssociate() as $associate) {
                $contacts[$associate->getId()] = $associate;
            }
        }
        /*
         * Add the workpackage leaders
         */
        foreach ($project->getWorkpackage() as $workpackage) {
            $contacts[$workpackage->getContact()->getId()] = $workpackage->getContact();
        }

        /*
         * Add the country coordinators
         */
        foreach ($project->getRationale() as $rationale) {
            $contacts[$rationale->getContact()->getId()] = $rationale->getContact();
        }

        return $contacts;
    }

    /**
     * returns true when the contact is in the booth.
     *
     * @param Contact $contact
     * @param Booth $booth
     *
     * @return bool
     */
    public function isContactInBooth(Contact $contact, Booth $booth): bool
    {
        foreach ($booth->getBoothContact() as $boothContact) {
            if ($contact === $boothContact->getContact()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Contact $contact
     * @param Facebook $facebook
     *
     * @return bool
     */
    public function isContactInFacebook(Contact $contact, Facebook $facebook): bool
    {
        /** @var \Contact\Repository\Facebook $repository */
        $repository = $this->getEntityManager()->getRepository(Facebook::class);

        return $repository->isContactInFacebook($contact, $facebook);
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
        if (\is_null($entity)) {
            throw new \InvalidArgumentException("Permit can only be determined of an existing entity, null given");
        }

        return $this->getAdminService()->contactHasPermit(
            $contact,
            $role,
            str_replace('doctrineormmodule_proxy___cg___', '', strtolower($entity->get('underscore_entity_name'))),
            $entity->getId()
        );
    }

    /**
     * @param Selection $selection
     * @param bool $toArray
     *
     * @return Contact[]
     */
    public function findContactsInSelection(Selection $selection, $toArray = false): array
    {
        /** @var \Contact\Repository\Contact $repository */
        $repository = $this->getEntityManager()->getRepository(Contact::class);

        /*
         * A selection can have 2 methods, either SQL or a contacts. We need to query both
         */
        if (!\is_null($selection->getSql())) {
            //We have a dynamic query, check if the contact is in the selection
            return $repository->findContactsBySelectionSQL($selection->getSql(), $toArray);
        }

        return $repository->findContactsBySelectionContact($selection, $toArray);
    }

    /**
     * Get a list of facebooks by contact (based on the access role).
     *
     * @param Contact $contact
     *
     * @return Facebook[]
     */
    public function findFacebookByContact(Contact $contact): array
    {
        /** @var \Contact\Repository\Facebook $repository */
        $repository = $this->getEntityManager()->getRepository(Facebook::class);

        return $repository->findFacebookByContact($contact);
    }

    /**
     * @param Facebook $facebook
     *
     * @return Contact[]
     */
    public function findContactsInFacebook(Facebook $facebook): array
    {

        /** @var \Contact\Repository\Contact $repository */
        $repository = $this->getEntityManager()->getRepository(Contact::class);

        /*
         * This function has a special feature to fill the array with contacts.
         * We can for instance try to find the country, organisation or position
         *
         * A dedicated array will therefore be created
         */
        $contacts = [];
        /** @var Contact $contact */
        foreach ($repository->findContactsInFacebook($facebook) as $contact) {
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
        if (empty($titleGetter)) {
            return '';
        }

        //Format the $getter
        switch ((int)$titleGetter) {
            case Facebook::DISPLAY_ORGANISATION:
                if (\is_null($contact->getContactOrganisation())) {
                    return 'Unknown';
                }

                return (string)$contact->getContactOrganisation()->getOrganisation();
            case Facebook::DISPLAY_COUNTRY:
                if (\is_null($contact->getContactOrganisation())) {
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
                    $projects[] = $projectLink($project, 'view', 'name-without-number');
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
     * @param string $password
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
     * @param array $contactOrganisation
     */
    public function updateContactOrganisation(
        Contact $contact,
        array $contactOrganisation
    ) {
        /**
         * Find the current contactOrganisation, or create a new one if this empty (in case of a new contact)
         */
        $currentContactOrganisation = $contact->getContactOrganisation();
        if (\is_null($currentContactOrganisation)) {
            $currentContactOrganisation = new ContactOrganisation();
            $currentContactOrganisation->setContact($contact);
        }

        /**
         * The trigger for this update is the presence of a $contactOrganisation['organisation_id'].
         * If this value != 0, a choice has been made from the dropdown and we will then take the branch as default
         */
        if (isset($contactOrganisation['organisation_id']) && $contactOrganisation['organisation_id'] !== '0') {
            $organisation = $this->getOrganisationService()
                ->findOrganisationById($contactOrganisation['organisation_id']);
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
            $country = $this->getGeneralService()->findEntityById(Country::class, (int)$contactOrganisation['country']);

            /*
             * Look for the organisation based on the name (without branch) and country + email
             */
            $organisations = $this->getOrganisationService()
                ->findOrganisationByNameCountryAndEmailAddress(
                    $contactOrganisation['organisation'],
                    $country,
                    $contact->getEmail()
                );


            $organisation = false;

            //First go over the organisations and try to see if we can find one with the same name and stop if we find one
            foreach ($organisations as $foundOrganisation) {
                if (!$organisation //Continue until the organisation is found
                    && $foundOrganisation->getOrganisation() === $contactOrganisation['organisation']
                ) {
                    $organisation = $foundOrganisation;
                }
            }

            //We have not found an organisation with an exact match so we will now try to see if we find one
            //With a almost perfect match and use that. We want to see if we can find the company name _in_ the given name
            foreach ($organisations as $foundOrganisation) {
                if (!$organisation //Continue until the organisation is found
                    && strpos($contactOrganisation['organisation'], $foundOrganisation->getOrganisation()) !== false
                ) {
                    $organisation = $foundOrganisation;
                }
            }

            //If the organisation is still not found, create a new one
            if (!$organisation) {
                $organisation = $this->getOrganisationService()->createOrganisationFromNameCountryTypeAndEmail(
                    $contactOrganisation['organisation'],
                    $country,
                    (int)$contactOrganisation['type'],
                    $contact->getEmail()
                );
            }

            if (!$organisation) {
                throw new \Exception("Update of profile failed, the organisation cannot be found");
            }

            $currentContactOrganisation->setOrganisation($organisation);
            $currentContactOrganisation->setBranch(
                OrganisationService::determineBranch(
                    $contactOrganisation['organisation'],
                    $organisation->getOrganisation()
                )
            );
        }

        //Update the entity
        $this->updateEntity($currentContactOrganisation);
    }


    /**
     * @param int $optInId
     * @param bool $enable
     * @param Contact $contact
     */
    public function updateOptInForContact(
        $optInId,
        $enable,
        Contact $contact
    ) {
        $optIn = $this->findEntityById(OptIn::class, $optInId);
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
        /** @var \Contact\Repository\Contact $repository */
        $repository = $this->getEntityManager()->getRepository(Contact::class);

        return $repository->findContactsByOptIn($optIn, true);
    }

    /**
     * @param int $optInId
     * @param Contact $contact
     *
     * @return bool
     */
    public function hasOptInEnabledByContact($optInId, Contact $contact): bool
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
     * @return Contact[]
     */
    public function searchContacts($searchItem): array
    {
        /** @var \Contact\Repository\Contact $repository */
        $repository = $this->getEntityManager()->getRepository(Contact::class);

        return $repository->searchContacts($searchItem);
    }


    /**
     * Returns which template is to  be used for facebook
     *
     * @return string
     */
    public function getFacebookTemplate(): string
    {
        return $this->getModuleOptions()->getFacebookTemplate();
    }

    /**
     * Create an array with the incomplete items in the profile and the relative weight.
     *
     * @param Contact $contact
     *
     * @return array
     */
    public function getProfileInCompleteness(Contact $contact): array
    {
        $inCompleteness = [];
        $totalWeight = 0;
        $totalWeight += 10;
        if (\is_null($contact->getFirstName())) {
            $inCompleteness['firstName']['message'] = _("txt-first-name-is-missing");
            $inCompleteness['firstName']['weight'] = 10;
        }
        $totalWeight += 10;
        if (\is_null($contact->getLastName())) {
            $inCompleteness['lastName']['message'] = _("txt-last-name-is-missing");
            $inCompleteness['lastName']['weight'] = 10;
        }
        $totalWeight += 10;
        if (\count($contact->getPhone()) === 0) {
            $inCompleteness['phone']['message'] = _("txt-no-telephone-number-known");
            $inCompleteness['phone']['weight'] = 10;
        }
        $totalWeight += 10;
        if (\count($contact->getAddress()) === 0) {
            $inCompleteness['address']['message'] = _("txt-no-address-known");
            $inCompleteness['address']['weight'] = 10;
        }
        $totalWeight += 10;
        if (\count($contact->getPhoto()) === 0) {
            $inCompleteness['photo']['message'] = _("txt-no-profile-photo-given");
            $inCompleteness['photo']['weight'] = 10;
        }
        $totalWeight += 10;
        if (\is_null($contact->getSaltedPassword())) {
            $inCompleteness['password']['message'] = _("txt-no-password-given");
            $inCompleteness['password']['weight'] = 10;
        }
        $totalWeight += 20;
        if (\is_null($contact->getContactOrganisation()) === 0) {
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
    public function findContactsInAffiliation(Affiliation $affiliation): array
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
        if (!\is_null($affiliation->getFinancial())) {
            $contacts[$affiliation->getFinancial()->getContact()->getId()] = $affiliation->getFinancial()
                ->getContact();
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
            if (!\is_null($workpackage->getContact()->getContactOrganisation())
                && $workpackage->getContact()->getContactOrganisation()->getOrganisation()->getId()
                === $affiliation->getOrganisation()->getId()
            ) {
                $contacts[$workpackage->getContact()->getId()] = $workpackage->getContact();
                $contactRole[$workpackage->getContact()->getId()][] = 'Workpackage leader';
            }
        }

        $contactRole = array_map('array_unique', $contactRole);

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
        if (\is_null($calendar->getProjectCalendar())) {
            throw new \Exception("A projectCalendar is required to find the contacts");
        }

        /** @var \Contact\Repository\Contact $repository */
        $repository = $this->getEntityManager()->getRepository(Contact::class);

        return $repository->findPossibleContactByCalendar($calendar);
    }

    /**
     * @param Organisation $organisation
     *
     * @return Contact[]
     */
    public function findContactsInOrganisation(Organisation $organisation)
    {
        /** @var \Contact\Repository\Contact $repository */
        $repository = $this->getEntityManager()->getRepository(Contact::class);

        return $repository->findContactsInOrganisation($organisation);
    }
}
