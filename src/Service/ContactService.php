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
use Contact\Entity\AbstractEntity;
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
use Contact\Search\Service\ContactSearchService;
use Contact\Search\Service\ProfileSearchService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Event\Entity\Booth\Booth;
use General\Entity\Country;
use General\Service\GeneralService;
use Organisation\Entity\Organisation;
use Organisation\Service\OrganisationService;
use Project\Entity\Project;
use Project\View\Helper\ProjectLink;
use Zend\Crypt\Password\Bcrypt;
use Zend\Mvc\Exception\RuntimeException;
use Zend\View\HelperPluginManager;
use ZfcUser\Options\ModuleOptions;

/**
 * Class ContactService
 *
 * @package Contact\Service
 */
class ContactService extends AbstractService
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
     * @var AddressService
     */
    private $addressService;
    /**
     * @var SelectionContactService
     */
    private $selectionContactService;
    /**
     * @var ContactSearchService
     */
    private $contactSearchService;
    /**
     * @var ProfileSearchService
     */
    private $profileSearchService;
    /**
     * @var OrganisationService
     */
    private $organisationService;
    /**
     * @var GeneralService
     */
    private $generalService;
    /**
     * @var HelperPluginManager
     */
    private $viewHelperManager;
    /**
     * @var ModuleOptions
     */
    private $userOptions;

    public function __construct(
        EntityManager $entityManager,
        AddressService $addressService,
        SelectionContactService $selectionContactService,
        ContactSearchService $contactSearchService,
        ProfileSearchService $profileSearchService,
        OrganisationService $organisationService,
        GeneralService $generalService,
        HelperPluginManager $viewHelperManager,
        ModuleOptions $userOptions
    ) {
        parent::__construct($entityManager);

        $this->addressService = $addressService;
        $this->selectionContactService = $selectionContactService;
        $this->contactSearchService = $contactSearchService;
        $this->profileSearchService = $profileSearchService;
        $this->organisationService = $organisationService;
        $this->generalService = $generalService;
        $this->viewHelperManager = $viewHelperManager;
        $this->userOptions = $userOptions;
    }


    public function findContactByHash(string $hash): ?Contact
    {
        $repository = $this->entityManager->getRepository(Contact::class);

        return $repository->findOneBy(['hash' => $hash]);
    }

    public function findContactById(int $id): ?Contact
    {
        return $this->find(Contact::class, $id);
    }

    public function toFormValueOptions(array $contacts): array
    {
        $return = [];
        foreach ($contacts as $contact) {
            $return[$contact->getId()] = $contact->getFormName();
        }
        asort($return);

        return $return;
    }

    public function findAllContacts(): QueryBuilder
    {
        /** @var \Contact\Repository\Contact $repository */
        $repository = $this->entityManager->getRepository(Contact::class);

        return $repository->findContacts();
    }

    /**
     * Find all contacts which are active and have a date of birth.
     *
     * @return Contact[]
     */
    public function findContactsWithDateOfBirth(): array
    {
        /** @var \Contact\Repository\Contact $repository */
        $repository = $this->entityManager->getRepository(Contact::class);

        return $repository->findContactsWithDateOfBirth();
    }

    /**
     * Find all contacts which are active and have a CV.
     *
     * @return Contact[]
     */
    public function findContactsWithCV(): array
    {
        /** @var \Contact\Repository\Contact $repository */
        $repository = $this->entityManager->getRepository(Contact::class);

        return $repository->findContactsWithCV();
    }

    public function findDuplicateContacts(array $filter): QueryBuilder
    {
        /** @var \Contact\Repository\Contact $repository */
        $repository = $this->entityManager->getRepository(Contact::class);

        return $repository->findDuplicateContacts($filter);
    }

    public function findInactiveContacts(array $filter): QueryBuilder
    {
        /** @var \Contact\Repository\Contact $repository */
        $repository = $this->entityManager->getRepository(Contact::class);

        return $repository->findInactiveContacts($filter);
    }

    public function canDeleteContact(Contact $contact): bool
    {
        return \count($this->cannotDeleteContact($contact)) === 0;
    }

    public function cannotDeleteContact(Contact $contact): array
    {
        $cannotDeleteContact = [];

        $repository = $this->entityManager->getRepository(Contact::class);

        if ($repository->contactIsActiveInProject($contact)) {
            $cannotDeleteContact[] = 'Contact is active in a project';
        }

        if ($repository->contactIsReviewer($contact)) {
            $cannotDeleteContact[] = 'Contact is reviewer in a project';
        }

        if ($this->contactInCoreSelection($contact)) {
            $cannotDeleteContact[] = 'Contact is in a core selection';
        }

        if ($repository->contactIsPresentAtEvent($contact)) {
            $cannotDeleteContact[] = 'Contact visited an event less than 2 years ago';
        }

        if ($repository->contactHasIdea($contact)) {
            $cannotDeleteContact[] = 'Contact has created an idea than 2 years ago';
        }

        if ($this->canAnonymiseContact($contact)) {
            $cannotDeleteContact[] = 'Contact is eligible to make anonymous';
        }

        return $cannotDeleteContact;
    }

    public function contactInCoreSelection(Contact $contact): bool
    {
        $coreSelections = $this->entityManager->getRepository(Selection::class)->findBy(['core' => Selection::CORE]);

        return $this->selectionContactService->contactInSelection($contact, $coreSelections);
    }

    public function canAnonymiseContact(Contact $contact): bool
    {
        return \count($this->canAnonymiseContactReasons($contact)) > 0;
    }

    public function canAnonymiseContactReasons(Contact $contact): array
    {
        $repository = $this->entityManager->getRepository(Contact::class);

        $canAnonymiseContact = [];

//        if (!$this->canDeleteContact($contact)) {
//            $canAnonymiseContact[] = 'Contact cannot be deleted, so can also not be anonymised';
//        }

        if ($repository->contactIsActiveInProject($contact, 5, 'older')) {
            $canAnonymiseContact[] = 'Contact is active in a project completed more than 5 years ago';
        }

        if ($repository->contactIsReviewer($contact)) {
            $canAnonymiseContact[] = 'Contact is reviewer in a project';
        }

        if ($repository->contactIsPresentAtEvent($contact, 2, 'older')) {
            $canAnonymiseContact[] = 'Contact visited an event more than 2 years ago';
        }

        if ($repository->contactHasIdea($contact, 2, 'older')) {
            $canAnonymiseContact[] = 'Contact has created an idea older 2 years ago';
        }

        return $canAnonymiseContact;
    }

    public function findContactsWithActiveProfile(bool $onlyPublic = true): array
    {
        /** @var \Contact\Repository\Contact $repository */
        $repository = $this->entityManager->getRepository(Contact::class);

        return $repository->findContactsWithActiveProfile($onlyPublic);
    }

    public function parseLastName(Contact $contact): string
    {
        return \trim(\implode(' ', [$contact->getMiddleName(), $contact->getLastName()]));
    }

    public function parseAttention(Contact $contact): string
    {
        if (null === $contact->getTitle()) {
            return '';
        }

        if (null === $contact->getGender()) {
            return '';
        }

        if (null !== $contact->getTitle()->getAttention()) {
            return $contact->getTitle()->getAttention();
        }

        if ((int)$contact->getGender()->getId() !== 0) {
            return $contact->getGender()->getAttention();
        }

        return '';
    }

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

    public function parseOrganisation(Contact $contact): ?string
    {
        if (!$this->hasOrganisation($contact)) {
            return null;
        }

        return OrganisationService::parseBranch(
            $contact->getContactOrganisation()->getBranch(),
            $contact->getContactOrganisation()->getOrganisation()
        );
    }

    public function hasOrganisation(Contact $contact): bool
    {
        return null !== $contact->getContactOrganisation();
    }

    public function isFunder(Contact $contact): bool
    {
        return null !== $contact->getFunder();
    }

    public function isActive(Contact $contact): bool
    {
        return null === $contact->getDateEnd();
    }

    public function parseCountry(Contact $contact): ?Country
    {
        if (!$this->hasOrganisation($contact)) {
            return null;
        }

        return $contact->getContactOrganisation()->getOrganisation()->getCountry();
    }

    public function getMailAddress(Contact $contact): ?Address
    {
        return $this->getAddressByTypeId($contact, AddressType::ADDRESS_TYPE_MAIL);
    }

    public function getAddressByTypeId(Contact $contact, int $typeId): ?Address
    {
        $addressType = $this->entityManager->find(AddressType::class, $typeId);

        if (null === $addressType) {
            return null;
        }

        return $this->addressService->findAddressByContactAndType($contact, $addressType);
    }

    public function getVisitAddress(Contact $contact): ?Address
    {
        return $this->getAddressByTypeId($contact, AddressType::ADDRESS_TYPE_VISIT);
    }

    public function getFinancialAddress(Contact $contact): ?Address
    {
        return $this->getAddressByTypeId($contact, AddressType::ADDRESS_TYPE_FINANCIAL);
    }

    public function getBoothFinancialAddress(Contact $contact): ?Address
    {
        return $this->getAddressByTypeId($contact, AddressType::ADDRESS_TYPE_BOOTH_FINANCIAL);
    }

    public function getDirectPhone(Contact $contact): ?Phone
    {
        return $this->getPhoneByContactAndType($contact, PhoneType::PHONE_TYPE_DIRECT);
    }

    private function getPhoneByContactAndType(Contact $contact, int $type): ?Phone
    {
        if (!\in_array($type, PhoneType::getPhoneTypes(), true)) {
            throw new \InvalidArgumentException(sprintf('A invalid phone type chosen'));
        }

        return $this->entityManager->getRepository(Phone::class)->findOneBy(
            [
                'contact' => $contact,
                'type'    => $type,
            ]
        );
    }

    public function getMobilePhone(Contact $contact): ?Phone
    {
        return $this->getPhoneByContactAndType($contact, PhoneType::PHONE_TYPE_MOBILE);
    }


    public function addNoteToContact(string $text, string $source, Contact $contact): Note
    {
        $note = new Note();
        $note->setNote($text);
        $note->setContact($contact);
        $note->setSource($source);
        $this->save($note);

        return $note;
    }

    public function save(AbstractEntity $contact): AbstractEntity
    {
        parent::save($contact);

        $this->refresh($contact);

        if ($contact instanceof Contact) {
            $this->contactSearchService->updateDocument($contact);

            if ($contact->isVisibleInCommunity()) {
                $this->profileSearchService->updateDocument($contact);
            } else {
                $this->profileSearchService->deleteDocument($contact);
            }
        }

        return $contact;
    }

    public function delete(AbstractEntity $contact): void
    {
        parent::delete($contact);

        if ($contact instanceof Contact) {
            $this->contactSearchService->deleteDocument($contact);
            $this->profileSearchService->deleteDocument($contact);
        }
    }

    public function findContactByEmail(string $email, bool $onlyMain = false): ?Contact
    {
        /** @var \Contact\Repository\Contact $repository */
        $repository = $this->entityManager->getRepository(Contact::class);

        return $repository->findContactByEmail($email, $onlyMain);
    }

    public function findSelectionByName(string $name)
    {
        return $this->entityManager->getRepository(Selection::class)->findOneBy(
            [
                'selection' => $name,
            ]
        );
    }

    public function getAmountOfContactsInOptIn(OptIn $optIn): int
    {
        return $this->entityManager->getRepository(OptIn::class)->getAmountOfContactsInOptIn($optIn);
    }

    public function isContactInProject(Contact $contact, Project $project): bool
    {
        $projectContacts = $this->findContactsInProject($project);

        return \array_key_exists($contact->getId(), $projectContacts);
    }

    public function findContactsInProject(Project $project): array
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
            $contact = $affiliation->getContact();
            $contacts[$contact->getId()] = $contact;
            foreach ($affiliation->getAssociate() as $associate) {
                $contacts[$associate->getId()] = $associate;
            }
        }
        /*
         * Add the work package leaders
         */
        foreach ($project->getWorkpackage() as $workpackage) {
            $contact = $workpackage->getContact();
            $contacts[$contact->getId()] = $contact;
        }

        /*
         * Add the country coordinators
         */
        foreach ($project->getRationale() as $rationale) {
            $contact = $rationale->getContact();
            $contacts[$contact->getId()] = $contact;
        }

        /*
         * Add the proxy project leaders
         */
        foreach ($project->getProxyContact() as $contact) {
            $contacts[$contact->getId()] = $contact;
        }

        return $contacts;
    }

    public function isContactInBooth(Contact $contact, Booth $booth): bool
    {
        foreach ($booth->getBoothContact() as $boothContact) {
            if ($contact === $boothContact->getContact()) {
                return true;
            }
        }

        return false;
    }

    public function isContactInFacebook(Contact $contact, Facebook $facebook): bool
    {
        /** @var \Contact\Repository\Facebook $repository */
        $repository = $this->entityManager->getRepository(Facebook::class);

        return $repository->isContactInFacebook($contact, $facebook);
    }

    /**
     * @param Contact        $contact
     * @param string         $role
     * @param AbstractEntity $entity
     *
     * @return bool
     */
    public function contactHasPermit(Contact $contact, string $role, $entity): bool
    {
        if (null === $entity) {
            throw new \InvalidArgumentException('Permit can only be determined of an existing entity, null given');
        }

        $entityName = \str_replace(
            'doctrineormmodule_proxy___cg___',
            '',
            strtolower($entity->get('underscore_entity_name'))
        );

        $repository = $this->entityManager->getRepository(\Admin\Entity\Permit\Contact::class);

        return $repository->contactHasPermit($contact, $role, $entityName, (int)$entity->getId());
    }

    public function canDeleteOptIn(OptIn $optIn): bool
    {
        $cannotDeleteOptIn = [];

        if (!$optIn->getContact()->isEmpty()) {
            $cannotDeleteOptIn[] = 'This Opt In still has contacts';
        }

        if (!$optIn->getMailing()->isEmpty()) {
            $cannotDeleteOptIn[] = 'This Opt In still has mailings';
        }

        return \count($cannotDeleteOptIn) === 0;
    }

    /**
     * @return OptIn[]
     */
    public function findActiveOptIns(): array
    {
        $repository = $this->entityManager->getRepository(OptIn::class);

        return $repository->findBy(['active' => OptIn::ACTIVE_ACTIVE]);
    }

    public function findFacebookByContact(Contact $contact): array
    {
        $repository = $this->entityManager->getRepository(Facebook::class);

        return $repository->findFacebookByContact($contact);
    }

    public function findContactsInFacebook(Facebook $facebook): array
    {
        $repository = $this->entityManager->getRepository(Contact::class);

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
            $singleContact['title'] = $this->facebookTitleParser((int)$facebook->getTitle(), $contact);
            $singleContact['subTitle'] = $this->facebookTitleParser((int)$facebook->getSubtitle(), $contact);
            $singleContact['email'] = $contact->getEmail();
            $singleContact['phone'] = $this->getPhoneByContactAndType($contact, PhoneType::PHONE_TYPE_DIRECT);
            $singleContact['mobile'] = $this->getPhoneByContactAndType($contact, PhoneType::PHONE_TYPE_MOBILE);

            $contacts[] = $singleContact;
        }


        return $contacts;
    }

    private function facebookTitleParser(int $titleGetter, Contact $contact): ?string
    {
        switch ($titleGetter) {
            case Facebook::DISPLAY_ORGANISATION:
                if (null === $contact->getContactOrganisation()) {
                    return 'Unknown';
                }

                return (string)$contact->getContactOrganisation()->getOrganisation();
            case Facebook::DISPLAY_COUNTRY:
                if (null === $contact->getContactOrganisation()) {
                    return 'Unknown';
                }

                return (string)$contact->getContactOrganisation()->getOrganisation()->getCountry();
            case Facebook::DISPLAY_PROJECTS:
                $projects = [];

                $projectLink = $this->viewHelperManager->get(ProjectLink::class);
                $repository = $this->entityManager->getRepository(Project::class);

                foreach ($repository->findProjectsByProjectContact($contact) as $project) {
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

    public function updatePasswordForContact(string $password, Contact $contact): void
    {
        $Bcrypt = new Bcrypt();
        $Bcrypt->setCost($this->userOptions->getPasswordCost());
        $pass = $Bcrypt->create(md5($password));

        $contact->setSaltedPassword($pass);
        $this->save($contact);
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
    public function updateContactOrganisation(Contact $contact, array $contactOrganisation): void
    {
        /**
         * Find the current contactOrganisation, or create a new one if this empty (in case of a new contact)
         */
        $currentContactOrganisation = $contact->getContactOrganisation();
        if (null === $currentContactOrganisation) {
            $currentContactOrganisation = new ContactOrganisation();
            $currentContactOrganisation->setContact($contact);
        }

        /**
         * The trigger for this update is the presence of a $contactOrganisation['organisation_id'].
         * If this value != 0, a choice has been made from the dropdown and we will then take the branch as default
         */
        if (isset($contactOrganisation['organisation_id']) && $contactOrganisation['organisation_id'] !== '0') {
            $organisation = $this->organisationService->findOrganisationById(
                (int)$contactOrganisation['organisation_id']
            );
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

            /** @var Country $country */
            $country = $this->generalService->find(Country::class, (int)$contactOrganisation['country']);

            if (null === $country) {
                return;
            }

            /*
             * Look for the organisation based on the name (without branch) and country + email
             */
            $organisations = $this->organisationService->findOrganisationByNameCountryAndEmailAddress(
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
                $organisation = $this->organisationService->createOrganisationFromNameCountryTypeAndEmail(
                    $contactOrganisation['organisation'],
                    $country,
                    (int)$contactOrganisation['type'],
                    $contact->getEmail()
                );
            }

            if (!$organisation) {
                throw new RuntimeException('Update of profile failed, the organisation cannot be found');
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
        $this->save($currentContactOrganisation);
    }

    public function findContactsByOptInAsArray(OptIn $optIn): array
    {
        /** @var \Contact\Repository\Contact $repository */
        $repository = $this->entityManager->getRepository(Contact::class);

        return $repository->findContactsByOptIn($optIn, true);
    }

    public function searchContacts(string $searchItem): array
    {
        $repository = $this->entityManager->getRepository(Contact::class);

        return $repository->searchContacts($searchItem);
    }

    public function getProfileInCompleteness(Contact $contact): array
    {
        $inCompleteness = [];
        $totalWeight = 0;
        $totalWeight += 10;
        if (null === $contact->getFirstName()) {
            $inCompleteness['firstName']['message'] = _("txt-first-name-is-missing");
            $inCompleteness['firstName']['weight'] = 10;
        }
        $totalWeight += 10;
        if (null === $contact->getLastName()) {
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
        if (null === $contact->getSaltedPassword()) {
            $inCompleteness['password']['message'] = _("txt-no-password-given");
            $inCompleteness['password']['weight'] = 10;
        }
        $totalWeight += 20;
        if (null === $contact->getContactOrganisation()) {
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
        if (null !== $affiliation->getFinancial()) {
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
         * Add the project leader
         */
        if ($this->contactIsFromOrganisation(
            $affiliation->getProject()->getContact(),
            $affiliation->getOrganisation()
        )
        ) {
            $contacts[$affiliation->getProject()->getContact()->getId()] = $affiliation->getProject()->getContact();
            $contactRole[$affiliation->getProject()->getContact()->getId()][] = 'Project leader';
        }

        /*
         * Add the project leader (proxy)
         */
        foreach ($affiliation->getProject()->getProxyContact() as $proxyContact) {
            /*
             * Add the work package leaders
             */
            if ($this->contactIsFromOrganisation($proxyContact, $affiliation->getOrganisation())) {
                $contacts[$proxyContact->getId()] = $proxyContact;
                $contactRole[$proxyContact->getId()][] = 'Project leader (proxy)';
            }
        }


        /*
         * Add the work package leaders
         */
        foreach ($affiliation->getProject()->getWorkpackage() as $workpackage) {
            /*
             * Add the work package leaders
             */
            if ($this->contactIsFromOrganisation($workpackage->getContact(), $affiliation->getOrganisation())) {
                $contacts[$workpackage->getContact()->getId()] = $workpackage->getContact();
                $contactRole[$workpackage->getContact()->getId()][] = 'Work package leader';
            }
        }


        $contactRole = array_map('array_unique', $contactRole);

        return ['contacts' => $contacts, 'contactRole' => $contactRole];
    }

    private function contactIsFromOrganisation(Contact $contact, Organisation $organisation): bool
    {
        if (null === $contact->getContactOrganisation()) {
            return false;
        }

        return $contact->getContactOrganisation()->getOrganisation() === $organisation;
    }

    public function updateOptInForContact(Contact $contact, array $optInOptions): void
    {
        /** Remove first the optIns which are not present in the optIn array */
        $contactOpIn = $contact->getOptIn();
        foreach ($contact->getOptIn() as $optIn) {
            if (!isset($data['optIn']) || !\in_array($optIn->getId(), $optInOptions, false)) {
                $contactOpIn->removeElement($optIn);
            }
        }

        /** Inject the new optIns */
        if (!empty($optInOptions)) {
            foreach ($optInOptions as $optInId) {
                $optIn = $this->find(OptIn::class, (int)$optInId);

                if (!$contactOpIn->contains($optIn)) {
                    $contactOpIn->add($optIn);
                }
            }
        } else {
            $contactOpIn->clear();
        }

        $this->save($contact->setOptIn($contactOpIn));
    }

    public function findPossibleContactByCalendar(Calendar $calendar): array
    {
        if (null === $calendar->getProjectCalendar()) {
            throw new RuntimeException('A projectCalendar is required to find the contacts');
        }

        /** @var \Contact\Repository\Contact $repository */
        $repository = $this->entityManager->getRepository(Contact::class);

        return $repository->findPossibleContactByCalendar($calendar);
    }

    /**
     * @param Organisation $organisation
     *
     * @return Contact[]
     */
    public function findContactsInOrganisation(Organisation $organisation): array
    {
        /** @var \Contact\Repository\Contact $repository */
        $repository = $this->entityManager->getRepository(Contact::class);

        return $repository->findContactsInOrganisation($organisation);
    }
}
