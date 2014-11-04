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

use Affiliation\Entity\Affiliation;
use Calendar\Entity\Calendar;
use Contact\Entity\AddressType;
use Contact\Entity\Contact;
use Contact\Entity\ContactOrganisation;
use Contact\Entity\Phone;
use Contact\Entity\PhoneType;
use Contact\Entity\Selection;
use Contact\Options\CommunityOptionsInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\PersistentCollection;
use General\Entity\Country;
use General\Entity\Gender;
use General\Entity\Title;
use Mailing\Entity\Mailing;
use Organisation\Entity\Organisation;
use Organisation\Entity\Web;
use Organisation\Service\OrganisationService;
use Project\Service\ProjectService;
use Zend\Crypt\Password\Bcrypt;
use Zend\Validator\EmailAddress;
use ZfcUser\Options\UserServiceOptionsInterface;

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
     * @return bool
     */
    public function isEmpty()
    {
        return is_null($this->contact) || is_null($this->contact->getId());
    }

    /**
     * This function returns the contact by the hash. The hash has as format contactId-CHECKSUM which needs to be checked
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
     * @return \Doctrine\ORM\Query
     */
    public function findAllContacts()
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName('contact'))->findContacts();
    }

    /**
     * Find all contacts which are active and have a date of birth
     *
     * @return Contact[]
     */
    public function findContactsWithDateOfBirth()
    {
        return $this->getEntityManager()
            ->getRepository($this->getFullEntityName('contact'))
            ->findContactsWithDateOfBirth();
    }

    /**
     * Find all contacts which are active and have a CV
     *
     * @return Contact[]
     */
    public function findContactsWithCV()
    {
        return $this->getEntityManager()
            ->getRepository($this->getFullEntityName('contact'))
            ->findContactsWithCV();
    }

    /**
     * Find a list of upcoming meetings were a user has not registered yet
     *
     * @return \Event\Entity\Meeting\Meeting[]
     */
    public function findUpcomingMeetings()
    {
        return $this->getMeetingService()->findUnregisteredMeetingsByContact($this->getContact());
    }

    /**
     * @return \Contact\Entity\Contact
     */
    public function getContact()
    {
        return $this->contact;
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
     * Get the last name
     *
     * @return string
     */
    public function parseLastName()
    {
        return trim(implode(' ', [$this->getContact()->getMiddleName(), $this->getContact()->getLastName()]));
    }

    /**
     * Create the attention of a contact
     *
     * @return string
     */
    public function parseAttention()
    {
        if (!is_null($this->getContact()->getTitle()->getAttention())) {
            return $this->getContact()->getTitle()->getAttention();
        } elseif ((int)$this->getContact()->getGender()->getId() !== 0) {
            return $this->getContact()->getGender()->getAttention();
        }

        return '';
    }

    /**
     * Dedicated function to have the organisation of a contact (or null)
     *
     * @return null|string
     */
    public function parseOrganisation()
    {
        if (!$this->hasOrganisation()) {
            return null;
        }

        return $this->findOrganisationService()->parseOrganisationWithBranch(
            $this->getContact()->getContactOrganisation()->getBranch()
        );
    }

    /**
     * Boolean value to check if  contact has a contactOrganisation (and is thus linked to an organisation
     *
     * @return bool
     */
    public function hasOrganisation()
    {
        return !is_null($this->getContact()->getContactOrganisation());
    }

    /**
     * @return OrganisationService|null
     */
    public function findOrganisationService()
    {
        /**
         * Return null when the contactOrganisation is not defined
         */
        if (!$this->hasOrganisation()) {
            return null;
        }

        return $this->getOrganisationService()->setOrganisationId(
            $this->getContact()->getContactOrganisation()->getOrganisation()->getId()
        );
    }

    /**
     * Dedicated function to have the organisation of a contact (or null)
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
     * Find the mail address of a contact
     *
     * @throws \RunTimeException
     * @return AddressService
     */
    public function getMailAddress()
    {
        return $this->getAddressByTypeId(AddressType::ADDRESS_TYPE_MAIL);
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
     * @param AddressService $addressService
     */
    public function setAddressService($addressService)
    {
        $this->addressService = $addressService;
    }

    /**
     * Find the visit address of a contact
     *
     * @throws \RunTimeException
     * @return AddressService
     */
    public function getVisitAddress()
    {
        return $this->getAddressByTypeId(AddressType::ADDRESS_TYPE_VISIT);
    }

    /**
     * Find the financial address of a contact
     *
     * @throws \RunTimeException
     * @return AddressService
     */
    public function getFinancialAddress()
    {
        return $this->getAddressByTypeId(AddressType::ADDRESS_TYPE_FINANCIAL);
    }

    /**
     * Find the financial address of a contact
     *
     * @throws \RunTimeException
     * @return AddressService
     */
    public function getBoothFinancialAddress()
    {
        return $this->getAddressByTypeId(AddressType::ADDRESS_TYPE_BOOTH_FINANCIAL);
    }

    /**
     * @param $typeId
     * @return AddressService|null
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function getAddressByTypeId($typeId)
    {
        if (is_null($this->getContact())) {
            throw new \RunTimeException(sprintf("A contact should be set"));
        }

        /**
         * @var $addressType AddressType
         */
        $addressType = $this->getEntityManager()->find($this->getFullEntityName('AddressType'), $typeId);

        if (is_null($this->getContact())) {
            throw new \RunTimeException(sprintf("A invalid AddressType (%s) requested", $addressType));
        }

        return $this->getAddressService()->findAddressByContactAndType(
            $this->getContact(),
            $addressType
        );
    }

    /**
     * Find the direct phone number of a contact
     *
     * @return Phone
     * @throws \RunTimeException
     */
    public function getDirectPhone()
    {
        if (is_null($this->getContact())) {
            throw new \RunTimeException(sprintf("A contact should be set"));
        }

        return $this->getEntityManager()->getRepository($this->getFullEntityName('phone'))->findOneBy(
            [
                'contact' => $this->contact,
                'type'    => PhoneType::PHONE_TYPE_DIRECT
            ]
        );
    }

    /**
     * Find the mobile phone number of a contact
     *
     * @return Phone
     * @throws \RunTimeException
     */
    public function getMobilePhone()
    {
        if (is_null($this->getContact())) {
            throw new \RunTimeException(sprintf("A contact should be set"));
        }

        return $this->getEntityManager()->getRepository($this->getFullEntityName('phone'))->findOneBy(
            [
                'contact' => $this->contact,
                'type'    => PhoneType::PHONE_TYPE_MOBILE
            ]
        );
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
        $contact->setGender($this->getGeneralService()->findEntityById('gender', Gender::GENDER_UNKNOWN));
        $contact->setTitle($this->getGeneralService()->findEntityById('title', Title::TITLE_UNKNOWN));
        /**
         * Include all the optIns
         */
        $contact->setOptIn($this->findAll('optIn'));
        $contact = $this->newEntity($contact);
        //Create a target
        $target = $this->getDeeplinkService()->createTargetFromRoute('contact/profile');
        //Create a deep link for the user which redirects to the profile-page
        $deeplink = $this->getDeeplinkService()->createDeeplink($target, $contact);
        $email = $this->getEmailService()->setTemplate("/auth/register:mail")->create();
        $email->addTo($emailAddress);
        $email->setUrl($this->getDeeplinkService()->parseDeeplinkUrl($deeplink));
        $this->getEmailService()->send($email);

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
        $contact = $this->findContactByEmail($emailAddress, true);
        if (is_null($contact)) {
            throw new \InvalidArgumentException(
                sprintf("The contact with emailAddress %s cannot be found", $emailAddress)
            );
        }
        $contactService = $this->createServiceElement($contact);
        //Create a target
        $target = $this->getDeeplinkService()->createTargetFromRoute('contact/change-password');
        //Create a deeplink for the user which redirects to the profile-page
        $deeplink = $this->getDeeplinkService()->createDeeplink($target, $contact);
        /**
         * Send the email tot he user
         */
        $email = $this->getEmailService()->setTemplate("/auth/forgotpassword:mail")->create();
        $email->addTo($emailAddress, $contactService->parseFullName());
        $email->setFullname($contactService->parseFullName());
        $email->setUrl($this->getDeeplinkService()->parseDeeplinkUrl($deeplink));
        $this->getEmailService()->send($email);

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
        return $this->getEntityManager()
            ->getRepository($this->getFullEntityName('contact'))
            ->findContactByEmail($email, $onlyMain);
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
     * Parse the full name of a project
     *
     * @return string
     */
    public function parseFullName()
    {
        return $this->getContact()->getDisplayName();
    }

    /**
     * Return true or false depending if a user is in the community
     *
     * @return bool
     */
    public function isCommunity()
    {
        return $this->getEntityManager()->getRepository(
            $this->getFullEntityName('contact')
        )->findIsCommunityMember($this->getContact(), $this->getCommunityOptions());
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
            $selections = [$selections];
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
            $contact = $this->getEntityManager()->getRepository(
                $this->getFullEntityName('SelectionContact')
            )->findOneBy(
                [
                    'contact'   => $this->getContact(),
                    'selection' => $selection
                ]
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
     * @param $role
     * @param $entity
     *
     * @throw \InvalidArgumentException
     * @return bool
     */
    public function hasPermit($role, $entity)
    {
        if (is_null($entity)) {
            throw new \InvalidArgumentException("Permit can only be determined of an existing entity, null given");
        }
        /**
         * An empty contact can never have access via the permit-editor
         */
        if ($this->isEmpty()) {
            return false;
        }

        return $this->getAdminService()->contactHasPermit(
            $this->getContact(),
            $role,
            str_replace('doctrineormmodule_proxy___cg___', '', strtolower($entity->get('underscore_full_entity_name'))),
            $entity->getId()
        );
    }

    /**
     * @param Selection $selection
     *
     * @return Contact[]
     */
    public function findContactsInSelection(Selection $selection)
    {
        /**
         * A selection can have 2 methods, either SQL or a contacts. We need to query both
         */
        if (!is_null($selection->getSql())) {
            //We have a dynamic query, check if the contact is in the selection
            return $this->getEntityManager()->getRepository(
                $this->getFullEntityName('Contact')
            )->findContactsBySelectionSQL($selection->getSql());
        } else {
            return $this->getEntityManager()->getRepository(
                $this->getFullEntityName('Contact')
            )->findContactsBySelectionContact($selection);
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
        $Bcrypt = new Bcrypt();
        $Bcrypt->setCost($this->getZfcUserOptions()->getPasswordCost());
        $pass = $Bcrypt->create(md5($password));
        $contact->setPassword(md5($password));
        $contact->setSaltedPassword($pass);
        $this->updateEntity($contact);

        return true;
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

    /**
     * @param UserServiceOptionsInterface $zfcUserOptions
     */
    public function setZfcUserOptions($zfcUserOptions)
    {
        $this->zfcUserOptions = $zfcUserOptions;
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
     *
     * @return void
     */
    public function updateContactOrganisation(Contact $contact, array $contactOrganisation)
    {
        /**
         * Don't do anything when the organisationName = empty
         */
        if (empty($contactOrganisation['organisation'])) {
            return;
        }
        $country = $this->getGeneralService()->findEntityById(
            'country',
            (int)$contactOrganisation['country']
        );
        $currentContactOrganisation = $contact->getContactOrganisation();
        if (is_null($currentContactOrganisation)) {
            $currentContactOrganisation = new ContactOrganisation();
            $currentContactOrganisation->setContact($contact);
        }
        /**
         * Look for the organisation based on the name (without branch) and country + email
         */
        $organisation = $this->getOrganisationService()->findOrganisationByNameCountryAndEmailAddress(
            $contactOrganisation['organisation'],
            $country,
            $contact->getEmail()
        );
        $organisationFound = false;
        /**
         * We did not find an organisation, so we need to create it
         */
        if (sizeof($organisation) === 0) {
            $organisation = new Organisation();
            $organisation->setOrganisation($contactOrganisation['organisation']);
            $organisation->setCountry($country);
            $organisation->setType($this->organisationService->findEntityById('Type', 0)); //Unknown
            /**
             * Add the domain in the saved domains for this new company
             * Use the ZF2 EmailAddress validator to strip the hostname out of the EmailAddress
             */
            $validateEmail = new EmailAddress();
            $validateEmail->isValid($contact->getEmail());
            $organisationWeb = new Web();
            $organisationWeb->setOrganisation($organisation);
            $organisationWeb->setWeb($validateEmail->hostname);
            $organisationWeb->setMain(Web::MAIN);
            $this->getOrganisationService()->newEntity($organisationWeb);
            $currentContactOrganisation->setOrganisation($organisation);
        } else {
            $foundOrganisation = null;

            /**
             * Go over the found organisation to match the branching
             */
            foreach ($organisation as $foundOrganisation) {
                /**
                 * Stop when we have found an exact match and reset the branch if set
                 */
                if ($foundOrganisation->getOrganisation() === $contactOrganisation['organisation'] &&
                    $country->getId() === $foundOrganisation->getCountry()->getId()
                ) {
                    $currentContactOrganisation->setOrganisation($foundOrganisation);
                    $currentContactOrganisation->setBranch(null);
                    break;
                }
                if (!$organisationFound) {
                    //Create only a branch when the name is found and the given names do not match in length
                    if (strlen($foundOrganisation->getOrganisation()) <
                        (strlen($contactOrganisation['organisation']) -
                            strlen($currentContactOrganisation->getBranch()))
                    ) {
                        $currentContactOrganisation->setBranch(
                            str_replace(
                                $contactOrganisation['organisation'],
                                '~',
                                $foundOrganisation->getOrganisation()
                            )
                        );
                    }
                    /**
                     * We have found a match of the organisation in the string and
                     */
                    $organisationFound = true;

                }
            }
            $currentContactOrganisation->setOrganisation($foundOrganisation);
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
     * Search for contacts based on a search-item
     *
     * @param $searchItem
     *
     * @return QueryBuilder;
     */
    public function searchContacts($searchItem)
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName('contact'))
            ->searchContacts($searchItem);
    }

    /**
     * Create an array with the incomplete items in the profile and the relative weight
     */
    public function getProfileInCompleteness()
    {
        $inCompleteness = [];
        $totalWeight = 0;
        $totalWeight += 10;
        if (is_null($this->getContact()->getFirstName())) {
            $inCompleteness['firstName']['message'] = _("txt-first-name-is-missing");
            $inCompleteness['firstName']['weight'] = 10;
        }
        $totalWeight += 10;
        if (is_null($this->getContact()->getLastName())) {
            $inCompleteness['lastName']['message'] = _("txt-last-name-is-missing");
            $inCompleteness['lastName']['weight'] = 10;
        }
        $totalWeight += 10;
        if (sizeof($this->getContact()->getPhone()) === 0) {
            $inCompleteness['phone']['message'] = _("txt-no-telephone-number-known");
            $inCompleteness['phone']['weight'] = 10;
        }
        $totalWeight += 10;
        if (sizeof($this->getContact()->getAddress()) === 0) {
            $inCompleteness['address']['message'] = _("txt-no-address-known");
            $inCompleteness['address']['weight'] = 10;
        }
        $totalWeight += 10;
        if (sizeof($this->getContact()->getPhoto()) === 0) {
            $inCompleteness['photo']['message'] = _("txt-no-profile-photo-given");
            $inCompleteness['photo']['weight'] = 10;
        }
        $totalWeight += 10;
        if (is_null($this->getContact()->getSaltedPassword())) {
            $inCompleteness['password']['message'] = _("txt-no-password-given");
            $inCompleteness['password']['weight'] = 10;
        }
        $totalWeight += 20;
        if (is_null($this->getContact()->getContactOrganisation()) === 0) {
            $inCompleteness['organisation']['message'] = _("txt-no-organisation-known");
            $inCompleteness['organisation']['weight'] = 20;
        }
        /**
         * Determine the total weight
         */
        $incompletenessWeight = 0;
        /**
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
     * @param Contact $contact
     * @return boolean
     */
    public function findIsCommunityMember(Contact $contact){
        return $this->getEntityManager()->getRepository($this->getFullEntityName('contact'))
            ->findIsCommunityMember($contact, $this->getCommunityOptions());
    }

    /**
     * Produce a list of contacts which are active in a project
     *
     * @param ProjectService $projectService
     *
     * @return Contact[]
     * @throws \InvalidArgumentException
     */
    public function findContactsInProject(ProjectService $projectService)
    {
        /**
         * Throw an exception when no project is selected
         */
        if (is_null($projectService->getProject())) {
            throw new \InvalidArgumentException(sprintf("No project selected"));
        }
        $contacts = [];
        /**
         * Add the project leader
         */
        $contacts[$projectService->getProject()->getContact()->getId()] = $projectService->getProject()->getContact();
        /**
         * Add the contacts form the affiliations and the associates
         */
        foreach ($projectService->getProject()->getAffiliation() as $affiliation) {
            $contacts[$affiliation->getContact()->getId()] = $affiliation->getContact();
            foreach ($affiliation->getAssociate() as $associate) {
                $contacts[$associate->getId()] = $associate;
            }
        }
        /**
         * Add the workpackage leaders
         */
        foreach ($projectService->getProject()->getWorkpackage() as $workpackage) {
            $contacts[$workpackage->getContact()->getId()] = $workpackage->getContact();
        }

        return $contacts;
    }

    /**
     * @param  Affiliation $affiliation
     * @return array
     */
    public function findContactsInAffiliation(Affiliation $affiliation)
    {
        $contacts = new ArrayCollection();
        $contactRole = [];

        /**
         * Add the technical contact
         */
        $contacts->add($affiliation->getContact());
        $contactRole[$affiliation->getContact()->getId()][] = 'Technical Contact';

        /**
         * Add the financial contact
         */
        if (!is_null($affiliation->getFinancial())) {
            $contacts->add($affiliation->getFinancial()->getContact());
            $contactRole[$affiliation->getFinancial()->getContact()->getId()][] = 'Financial Contact';
        }

        /**
         * Add the associates
         */
        foreach ($affiliation->getAssociate() as $associate) {
            /**
             * Add the associates
             */
            if (!$contacts->contains($associate)) {
                $contacts->add($associate);
            }
            $contactRole[$associate->getId()][] = 'Associate';
        }

        /**
         * Add the workpackage leaders
         */
        foreach ($affiliation->getProject()->getWorkpackage() as $workpackage) {
            /**
             * Add the associates
             */
            if (is_null($workpackage->getContact()->getContactOrganisation())) {
                continue;
            }
            if ($workpackage->getContact()->getContactOrganisation()->getOrganisation()->getId() ===
                $affiliation->getOrganisation()->getId()
            ) {
                if (!$contacts->contains($workpackage->getContact())) {
                    $contacts->add($workpackage->getContact());
                }
                $contactRole[$workpackage->getContact()->getId()][] = 'Workpackage leader';
            }
        }

        $contactRole = array_map('array_unique', $contactRole);

        return ['contacts' => $contacts, 'contactRole' => $contactRole];
    }

    /**
     * @param  Calendar $calendar
     * @return Contact[]
     * @throws \Exception
     */
    public function findPossibleContactByCalendar(Calendar $calendar)
    {
        if (is_null($calendar->getProjectCalendar())) {
            throw new \Exception("A projectCalendar is required to find the contacts");
        }

        return $this->getEntityManager()
            ->getRepository($this->getFullEntityName('Contact'))
            ->findPossibleContactByCalendar($calendar);
    }

    /**
     * @param Organisation $organisation
     *
     * @return Contact[]
     */
    public function findContactsInOrganisation(Organisation $organisation)
    {
        return $this->getEntityManager()->getRepository(
            $this->getFullEntityName('contact')
        )->findContactsInOrganisation($organisation);
    }
}
