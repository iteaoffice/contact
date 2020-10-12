<?php

/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Service;

use Admin\Entity\Access;
use Admin\Service\AdminService;
use Affiliation\Entity\Affiliation;
use Calendar\Entity\Calendar;
use Contact\Entity\AbstractEntity;
use Contact\Entity\Address;
use Contact\Entity\AddressType;
use Contact\Entity\Contact;
use Contact\Entity\ContactOrganisation;
use Contact\Entity\Email;
use Contact\Entity\Facebook;
use Contact\Entity\Note;
use Contact\Entity\OptIn;
use Contact\Entity\Phone;
use Contact\Entity\PhoneType;
use Contact\Entity\Photo;
use Contact\Entity\Profile;
use Contact\Entity\Selection;
use Contact\Search\Service\ContactSearchService;
use Contact\Search\Service\ProfileSearchService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Event\Entity\Booth\Booth;
use General\Entity\Country;
use General\Entity\Gender;
use General\Entity\Title;
use General\Service\GeneralService;
use InvalidArgumentException;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Mvc\Exception\RuntimeException;
use Laminas\View\Helper\Url;
use Laminas\View\HelperPluginManager;
use Organisation\Entity\Organisation;
use Organisation\Service\OrganisationService;
use Project\Entity\Project;
use Project\View\Helper\Project\ProjectLink;
use Search\Service\SearchUpdateInterface;
use Solarium\Client;
use Solarium\Core\Query\AbstractQuery;
use Solarium\QueryType\Update\Query\Document;
use ZfcUser\Options\ModuleOptions;

use function array_key_exists;
use function count;
use function implode;
use function in_array;
use function is_array;
use function str_replace;
use function strip_tags;
use function strtolower;
use function trim;

/**
 * Class ContactService
 *
 * @package Contact\Service
 */
class ContactService extends AbstractService implements SearchUpdateInterface
{
    public const WHICH_ALL          = 1;
    public const WHICH_ONLY_ACTIVE  = 2;
    public const WHICH_ONLY_EXPIRED = 3;

    protected array $contacts = [];
    private AddressService $addressService;
    private SelectionContactService $selectionContactService;
    private ContactSearchService $contactSearchService;
    private ProfileSearchService $profileSearchService;
    private OrganisationService $organisationService;
    private GeneralService $generalService;
    private AdminService $adminService;
    private HelperPluginManager $viewHelperManager;
    private ModuleOptions $userOptions;

    public function __construct(
        EntityManager $entityManager,
        AddressService $addressService,
        SelectionContactService $selectionContactService,
        ContactSearchService $contactSearchService,
        ProfileSearchService $profileSearchService,
        OrganisationService $organisationService,
        GeneralService $generalService,
        AdminService $adminService,
        HelperPluginManager $viewHelperManager,
        ModuleOptions $userOptions
    ) {
        parent::__construct($entityManager);

        $this->addressService          = $addressService;
        $this->selectionContactService = $selectionContactService;
        $this->contactSearchService    = $contactSearchService;
        $this->profileSearchService    = $profileSearchService;
        $this->organisationService     = $organisationService;
        $this->generalService          = $generalService;
        $this->adminService            = $adminService;
        $this->viewHelperManager       = $viewHelperManager;
        $this->userOptions             = $userOptions;
    }


    public function findContactByHash(string $hash): ?Contact
    {
        $repository = $this->entityManager->getRepository(Contact::class);

        return $repository->findOneBy(['hash' => $hash]);
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

    public function findContactsWithDateOfBirth(): array
    {
        /** @var \Contact\Repository\Contact $repository */
        $repository = $this->entityManager->getRepository(Contact::class);

        return $repository->findContactsWithDateOfBirth();
    }

    public function findDuplicateContacts(array $filter): QueryBuilder
    {
        /** @var \Contact\Repository\Contact $repository */
        $repository = $this->entityManager->getRepository(Contact::class);

        return $repository->findDuplicateContacts($filter);
    }

    public function canDeleteContact(Contact $contact): bool
    {
        return count($this->cannotDeleteContact($contact)) === 0;
    }

    public function cannotDeleteContact(Contact $contact): array
    {
        $cannotDeleteContact = [];

        $repository = $this->entityManager->getRepository(Contact::class);

        if ($repository->contactIsActiveInProject($contact)) {
            $cannotDeleteContact[] = 'Contact is active in a project';
        }

        if (! $contact->getLoi()->isEmpty()) {
            $cannotDeleteContact[] = 'Contact is has uploaded an LOI';
        }

        if (! $contact->getLoiApprover()->isEmpty()) {
            $cannotDeleteContact[] = 'Contact is has approved an LOI';
        }

        if (! $contact->getAffiliationDoa()->isEmpty()) {
            $cannotDeleteContact[] = 'Contact is has uploaded a DoA';
        }

        if (! $contact->getAffiliationDoaApprover()->isEmpty()) {
            $cannotDeleteContact[] = 'Contact is has approved a DoA';
        }

        if (! $contact->getAffiliationVersion()->isEmpty()) {
            $cannotDeleteContact[] = 'Contact has been a technical contact in an older version';
        }

        if (! $contact->getAffiliationDescription()->isEmpty()) {
            $cannotDeleteContact[] = 'Contact has written a description for a partner';
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
        return count($this->canAnonymiseContactReasons($contact)) > 0;
    }

    public function canAnonymiseContactReasons(Contact $contact): array
    {
        $repository = $this->entityManager->getRepository(Contact::class);

        $canAnonymiseContact = [];

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

    public function contactWillBeAutoDelete(Contact $contact): bool
    {
        /** @var \Contact\Repository\Contact $repository */
        $repository = $this->entityManager->getRepository(Contact::class);

        return $repository->isInactiveContact($contact);
    }

    public function resetAccessRoles(): void
    {
        $contacts = $this->entityManager->getRepository(Contact::class)->findBy(['triggerUpdate' => true]);

        foreach ($contacts as $contact) {
            $contact->setTriggerUpdate(false);
            $this->save($contact);

            $this->adminService->resetCachedAccessRolesByContact($contact);
            $this->adminService->flushPermitsByContact($contact);
        }
    }

    public function save(AbstractEntity $contact): AbstractEntity
    {
        parent::save($contact);

        $this->refresh($contact);

        if ($contact instanceof Contact) {
            $this->updateEntityInSearchEngine($contact);
        }

        if ($contact instanceof ContactOrganisation) {
            $this->refresh($contact->getContact());
            $this->updateEntityInSearchEngine($contact->getContact());
        }

        return $contact;
    }

    /**
     * @param Contact $contact
     */
    public function updateEntityInSearchEngine($contact): void
    {
        $contactDocument = $this->prepareSearchUpdate($contact);
        $profileDocument = $this->prepareProfileSearchUpdate($contact);

        $this->contactSearchService->executeUpdateDocument($contactDocument);

        if ($contact->isVisibleInCommunity()) {
            $this->profileSearchService->executeUpdateDocument($profileDocument);
        } else {
            $this->profileSearchService->deleteDocument($contact);
        }
    }

    /**
     * @param Contact $contact
     *
     * @return AbstractQuery
     */
    public function prepareSearchUpdate($contact): AbstractQuery
    {
        $searchClient = new Client();
        $update       = $searchClient->createUpdate();

        /** @var Document $contactDocument */
        $contactDocument = $update->createDocument();

        $contactDocument->setField('id', $contact->getResourceId());
        $contactDocument->setField('contact_id', $contact->getId());
        $contactDocument->setField('contact_hash', $contact->getHash());

        $contactDocument->setField('fullname', $contact->getDisplayName());
        $contactDocument->setField('fullname_search', $contact->getDisplayName());
        $contactDocument->setField('fullname_sort', $contact->getDisplayName());

        $contactDocument->setField('lastname', $contact->getLastName());
        $contactDocument->setField('lastname_search', $contact->getLastName());
        $contactDocument->setField('lastname_sort', $contact->getLastName());

        $contactDocument->setField('email', $contact->getEmail());
        $contactDocument->setField('email_sort', $contact->getEmail());

        //Create a collection of emails
        $emails = [$contact->getEmail()];
        /** @var Email $emailAddress */
        foreach ($contact->getEmailAddress() as $emailAddress) {
            $emails[] = $emailAddress->getEmail();
        }
        $contactDocument->setField('email_search', $emails);

        $contactDocument->setField('gender', $contact->getGender()->getName());
        $contactDocument->setField('gender_search', $contact->getGender()->getName());
        $contactDocument->setField('gender_sort', $contact->getGender()->getName());

        $contactDocument->setField('title', $contact->getTitle()->getName());
        $contactDocument->setField('title_search', $contact->getTitle()->getName());
        $contactDocument->setField('title_sort', $contact->getTitle()->getName());

        $contactDocument->setField('position', $contact->getPosition());
        $contactDocument->setField('position_search', $contact->getPosition());
        $contactDocument->setField('position_sort', $contact->getPosition());

        $contactDocument->setField('department', $contact->getDepartment());
        $contactDocument->setField('department_search', $contact->getDepartment());
        $contactDocument->setField('department_sort', $contact->getDepartment());


        //Create a collection of opt ins
        $optInList = [];
        /** @var OptIn $optIn */
        foreach ($contact->getOptIn() as $optIn) {
            $optInList[] = $optIn->getOptIn();
        }
        $contactDocument->setField('optin', $optInList);

        //Create a collection of access groups
        $accessList = [];
        /** @var Access $access */
        foreach ($contact->getAccess() as $access) {
            $accessList[] = $access->getAccess();
        }
        $contactDocument->setField('access', $accessList);

        if (null !== $contact->getProfile()) {
            $description = strip_tags((string)$contact->getProfile()->getDescription());

            //Add the organisation description
            if ($contact->hasOrganisation() && $contact->getContactOrganisation()->getOrganisation()->hasDescription()) {
                $description = $contact->getContactOrganisation()->getOrganisation()->getDescription();
                $description .= ' ' . strip_tags($description->getDescription());
            }

            $contactDocument->setField('profile', str_replace(PHP_EOL, '', $description));
            $contactDocument->setField('profile_sort', str_replace(PHP_EOL, '', $description));
            $contactDocument->setField('profile_search', str_replace(PHP_EOL, '', $description));

            if ($contact->getPhoto()->count() > 0) {
                $url = $this->viewHelperManager->get(Url::class);

                /** @var Photo $photo */
                $photo = $contact->getPhoto()->first();

                $contactDocument->setField(
                    'photo_url',
                    $url(
                        'image/contact-photo',
                        [
                            'ext'         => $photo->getContentType()->getExtension(),
                            'last-update' => $photo->getDateUpdated()->getTimestamp(),
                            'id'          => $photo->getId(),
                        ]
                    )
                );
            }
        }

        $contactDocument->setField('has_organisation', $this->hasOrganisation($contact));
        $contactDocument->setField('has_organisation_text', $this->hasOrganisation($contact) ? 'Yes' : 'No');

        if ($this->hasOrganisation($contact)) {
            /** @var Organisation $organisation */
            $organisation = $contact->getContactOrganisation()->getOrganisation();

            $contactDocument->setField('organisation', $organisation->getOrganisation());
            $contactDocument->setField('organisation_id', $organisation->getId());
            $contactDocument->setField('organisation_sort', $organisation->getOrganisation());
            $contactDocument->setField('organisation_search', $organisation->getOrganisation());
            $contactDocument->setField('organisation_type', $organisation->getType());
            $contactDocument->setField('organisation_type_sort', $organisation->getType());
            $contactDocument->setField('organisation_type_search', $organisation->getType());
            $contactDocument->setField('country', $organisation->getCountry()->getCountry());
            $contactDocument->setField('country_id', $organisation->getCountry()->getId());
            $contactDocument->setField('country_iso3', $organisation->getCountry()->getIso3());
            $contactDocument->setField('country_sort', $organisation->getCountry()->getCountry());
            $contactDocument->setField('country_search', $organisation->getCountry()->getCountry());
        }

        $contactDocument->setField('is_active', $contact->isActive());
        $contactDocument->setField('is_active_text', $contact->isActive() ? 'Yes' : 'No');

        $contactDocument->setField('is_activated', $contact->isActivated());
        $contactDocument->setField('is_activated_text', $contact->isActivated() ? 'Yes' : 'No');

        $contactDocument->setField('is_anonymised', $contact->isActivated());
        $contactDocument->setField('is_anonymised_text', $contact->isActivated() ? 'Yes' : 'No');
        $contactDocument->setField('is_office', $contact->isOffice());
        $contactDocument->setField('is_office_text', $contact->isOffice() ? 'Yes' : 'No');


        $contactDocument->setField('is_funder', $contact->isFunder());
        $contactDocument->setField('is_funder_text', $contact->isFunder() ? 'Yes' : 'No');

        if ($contact->isFunder()) {
            $contactDocument->setField('funder_country', $contact->getFunder()->getCountry()->getCountry());
            $contactDocument->setField('funder_country_sort', $contact->getFunder()->getCountry()->getCountry());
            $contactDocument->setField('funder_country_search', $contact->getFunder()->getCountry()->getCountry());
        }

        $projects = [];
        foreach ($contact->getProject() as $project) {
            $projectId            = $project->getId();
            $projects[$projectId] = $projectId;
        }
        foreach ($contact->getAffiliation() as $affiliation) {
            $projectId            = $affiliation->getProject()->getId();
            $projects[$projectId] = $projectId;
        }
        foreach ($contact->getWorkpackage() as $workpackage) {
            $projectId            = $workpackage->getProject()->getId();
            $projects[$projectId] = $projectId;
        }
        foreach ($contact->getRationale() as $rationale) {
            $projectId            = $rationale->getProject()->getId();
            $projects[$projectId] = $projectId;
        }

        $projectCount = count($projects);

        $contactDocument->setField('projects', $projectCount);
        $contactDocument->setField('has_projects', $projectCount > 0 ? 'Yes' : 'No');
        $contactDocument->setField('has_projects_text', $projectCount > 0 ? 'Yes' : 'No');

        $update->addDocument($contactDocument);
        $update->addCommit();

        return $update;
    }

    public function hasOrganisation(Contact $contact): bool
    {
        return $contact->hasOrganisation();
    }

    /**
     * @param Contact $contact
     *
     * @return AbstractQuery
     */
    public function prepareProfileSearchUpdate($contact): AbstractQuery
    {
        $searchClient = new Client();
        $update       = $searchClient->createUpdate();

        /** @var Document $contactDocument */
        $contactDocument = $update->createDocument();

        $contactDocument->setField('id', $contact->getResourceId());
        $contactDocument->setField('contact_id', $contact->getId());
        $contactDocument->setField('contact_hash', $contact->getHash());

        $contactDocument->setField('fullname', $contact->getDisplayName());
        $contactDocument->setField('fullname_search', $contact->getDisplayName());
        $contactDocument->setField('fullname_sort', $contact->getDisplayName());

        $contactDocument->setField('lastname', $contact->getLastName());
        $contactDocument->setField('lastname_search', $contact->getLastName());
        $contactDocument->setField('lastname_sort', $contact->getLastName());

        $contactDocument->setField('position', $contact->getPosition());
        $contactDocument->setField('position_search', $contact->getPosition());
        $contactDocument->setField('position_sort', $contact->getPosition());

        if (null !== $contact->getProfile()) {
            $description = strip_tags((string)$contact->getProfile()->getDescription());

            //Add the organisation description
            if ($contact->hasOrganisation() && $contact->getContactOrganisation()->getOrganisation()->hasDescription()) {
                $description = $contact->getContactOrganisation()->getOrganisation()->getDescription();
                $description .= ' ' . strip_tags($description->getDescription());
            }

            $contactDocument->setField('profile', str_replace(PHP_EOL, '', $description));
            $contactDocument->setField('profile_sort', str_replace(PHP_EOL, '', $description));
            $contactDocument->setField('profile_search', str_replace(PHP_EOL, '', $description));

            if ($contact->getPhoto()->count() > 0) {
                $url = $this->viewHelperManager->get(Url::class);

                /** @var Photo $photo */
                $photo = $contact->getPhoto()->first();

                $contactDocument->setField(
                    'photo_url',
                    $url(
                        'image/contact-photo',
                        [
                            'ext'         => $photo->getContentType()->getExtension(),
                            'last-update' => $photo->getDateUpdated()->getTimestamp(),
                            'id'          => $photo->getId(),
                        ]
                    )
                );
            }
        }

        if (null !== $contact->getContactOrganisation()) {
            /** @var Organisation $organisation */
            $organisation = $contact->getContactOrganisation()->getOrganisation();

            $contactDocument->setField('organisation', $organisation->getOrganisation());
            $contactDocument->setField('organisation_sort', $organisation->getOrganisation());
            $contactDocument->setField('organisation_search', $organisation->getOrganisation());
            $contactDocument->setField('organisation_type', $organisation->getType());
            $contactDocument->setField('organisation_type_sort', $organisation->getType());
            $contactDocument->setField('organisation_type_search', $organisation->getType());
            $contactDocument->setField('country', $organisation->getCountry()->getCountry());
            $contactDocument->setField('country_sort', $organisation->getCountry()->getCountry());
            $contactDocument->setField('country_search', $organisation->getCountry()->getCountry());
        }

        $update->addDocument($contactDocument);
        $update->addCommit();

        return $update;
    }

    public function removeInactiveContacts(): void
    {
        $inactiveContacts = $this->findInactiveContacts();

        foreach ($inactiveContacts as $inactiveContact) {
            $contact = $this->findContactById($inactiveContact['id']);
            if (null !== $contact) {
                $this->delete($contact);
            }
        }
    }

    public function findInactiveContacts(): array
    {
        /** @var \Contact\Repository\Contact $repository */
        $repository = $this->entityManager->getRepository(Contact::class);

        return $repository->findInactiveContacts();
    }

    public function findContactById(int $id): ?Contact
    {
        return $this->find(Contact::class, $id);
    }

    public function delete(AbstractEntity $contact): void
    {
        if ($contact instanceof Contact) {
            $this->contactSearchService->deleteDocument($contact);
            $this->profileSearchService->deleteDocument($contact);
        }

        parent::delete($contact);
    }

    public function parseLastName(Contact $contact): string
    {
        return trim(implode(' ', [$contact->getMiddleName(), $contact->getLastName()]));
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
        $signature = [];

        if (null !== $contact->getPosition()) {
            $signature[] = $contact->getPosition();
        }

        /*
         * Go over the notes and find the signature of the contact
         */
        foreach ($contact->getNote() as $note) {
            if ($note->getSource() === Note::SOURCE_SIGNATURE) {
                $signature[] = sprintf('<em>%s</em>', $note->getNote());
            }
        }

        if (count($signature) === 0) {
            return null;
        }


        return implode('<br>', $signature);
    }

    public function parseOrganisation(Contact $contact): ?string
    {
        if (! $this->hasOrganisation($contact)) {
            return null;
        }

        return OrganisationService::parseBranch(
            $contact->getContactOrganisation()->getBranch(),
            $contact->getContactOrganisation()->getOrganisation()
        );
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
        if (! $this->hasOrganisation($contact)) {
            return null;
        }

        return $contact->getContactOrganisation()->getOrganisation()->getCountry();
    }

    public function getVisitAddress(Contact $contact): ?Address
    {
        return $this->getAddressByTypeId($contact, AddressType::ADDRESS_TYPE_VISIT);
    }

    public function getAddressByTypeId(Contact $contact, int $typeId): ?Address
    {
        $addressType = $this->entityManager->find(AddressType::class, $typeId);

        if (null === $addressType) {
            return null;
        }

        return $this->addressService->findAddressByContactAndType($contact, $addressType);
    }

    public function getFinancialAddress(Contact $contact): ?Address
    {
        return $this->getAddressByTypeId($contact, AddressType::ADDRESS_TYPE_FINANCIAL);
    }

    public function getBoothFinancialAddress(Contact $contact): ?Address
    {
        return $this->getAddressByTypeId($contact, AddressType::ADDRESS_TYPE_BOOTH_FINANCIAL);
    }

    public function getMobilePhone(Contact $contact): ?Phone
    {
        return $this->getPhoneByContactAndType($contact, PhoneType::PHONE_TYPE_MOBILE);
    }

    private function getPhoneByContactAndType(Contact $contact, int $type): ?Phone
    {
        if (! in_array($type, PhoneType::getPhoneTypes(), true)) {
            throw new InvalidArgumentException(sprintf('A invalid phone type chosen'));
        }

        return $this->entityManager->getRepository(Phone::class)->findOneBy(
            [
                'contact' => $contact,
                'type'    => $type,
            ]
        );
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

        return array_key_exists($contact->getId(), $projectContacts);
    }

    /**
     * @param Project $project
     *
     * @return Contact[]
     */
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
            $contact                     = $affiliation->getContact();
            $contacts[$contact->getId()] = $contact;
            foreach ($affiliation->getAssociate() as $associate) {
                $contacts[$associate->getId()] = $associate;
            }
        }
        /*
         * Add the work package leaders
         */
        foreach ($project->getWorkpackage() as $workpackage) {
            $contact                     = $workpackage->getContact();
            $contacts[$contact->getId()] = $contact;
        }

        /*
         * Add the country coordinators
         */
        foreach ($project->getRationale() as $rationale) {
            $contact                     = $rationale->getContact();
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
     * @param Contact $contact
     * @param string|array $role
     * @param              $entity
     *
     * @return bool
     */
    public function contactHasPermit(Contact $contact, $role, $entity): bool
    {
        if (null === $entity) {
            throw new InvalidArgumentException('Permit can only be determined of an existing entity, null given');
        }

        $entityName = str_replace(
            'doctrineormmodule_proxy___cg___',
            '',
            strtolower($entity->get('underscore_entity_name'))
        );

        $repository = $this->entityManager->getRepository(\Admin\Entity\Permit\Contact::class);

        if (is_array($role)) {
            foreach ($role as $singleRole) {
                $hasPermit = $repository->contactHasPermit($contact, $singleRole, $entityName, (int)$entity->getId());
                if ($hasPermit) {
                    return true;
                }
            }

            return false;
        }

        return $repository->contactHasPermit($contact, $role, $entityName, (int)$entity->getId());
    }

    public function canDeleteOptIn(OptIn $optIn): bool
    {
        $cannotDeleteOptIn = [];

        if (! $optIn->getContact()->isEmpty()) {
            $cannotDeleteOptIn[] = 'This Opt In still has contacts';
        }

        if (! $optIn->getMailing()->isEmpty()) {
            $cannotDeleteOptIn[] = 'This Opt In still has mailings';
        }

        return count($cannotDeleteOptIn) === 0;
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

        $roles = $this->adminService->findAccessRolesByContactAsArray($contact);

        return $repository->findFacebookByRoles($roles);
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
        /** @var Contact $facebookContact */
        foreach ($repository->findContactsInFacebook($facebook) as $facebookContact) {
            $singleContact = [];

            $singleContact['contact']  = $facebookContact;
            $singleContact['title']    = $this->facebookTitleParser((int)$facebook->getTitle(), $facebookContact);
            $singleContact['subTitle'] = $this->facebookTitleParser((int)$facebook->getSubtitle(), $facebookContact);
            $singleContact['email']    = $facebookContact->getEmail();
            $singleContact['phone']    = $this->getPhoneByContactAndType($facebookContact, PhoneType::PHONE_TYPE_DIRECT);
            $singleContact['mobile']   = $this->getPhoneByContactAndType($facebookContact, PhoneType::PHONE_TYPE_MOBILE);

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
                $repository  = $this->entityManager->getRepository(Project::class);

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
     * @param array $contactOrganisation
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
            if (! empty($contactOrganisation['branch'])) {
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
            /** @var Organisation $foundOrganisation */
            foreach ($organisations as $foundOrganisation) {
                if (
                    ! $organisation //Continue until the organisation is found
                    && $foundOrganisation->getOrganisation() === $contactOrganisation['organisation']
                ) {
                    $organisation = $foundOrganisation;
                }
            }

            //We have not found an organisation with an exact match so we will now try to see if we find one
            //With a almost perfect match and use that. We want to see if we can find the company name _in_ the given name
            foreach ($organisations as $foundOrganisation) {
                if (
                    ! $organisation //Continue until the organisation is found
                    && strpos($contactOrganisation['organisation'], $foundOrganisation->getOrganisation()) !== false
                ) {
                    $organisation = $foundOrganisation;
                }
            }

            //If the organisation is still not found, create a new one
            if (! $organisation) {
                $organisation = $this->organisationService->createOrganisationFromNameCountryTypeAndEmail(
                    $contactOrganisation['organisation'],
                    $country,
                    (int)$contactOrganisation['type'],
                    $contact->getEmail()
                );
            }

            if (! $organisation) {
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
        $totalWeight    = 0;
        $totalWeight    += 10;
        if (null === $contact->getFirstName()) {
            $inCompleteness['firstName']['message'] = _('txt-first-name-is-missing');
            $inCompleteness['firstName']['weight']  = 10;
        }
        $totalWeight += 10;
        if (null === $contact->getLastName()) {
            $inCompleteness['lastName']['message'] = _('txt-last-name-is-missing');
            $inCompleteness['lastName']['weight']  = 10;
        }
        $totalWeight += 10;
        if (count($contact->getPhone()) === 0) {
            $inCompleteness['phone']['message'] = _('txt-no-telephone-number-known');
            $inCompleteness['phone']['weight']  = 10;
        }
        $totalWeight += 10;
        if (count($contact->getAddress()) === 0) {
            $inCompleteness['address']['message'] = _('txt-no-address-known');
            $inCompleteness['address']['weight']  = 10;
        }
        $totalWeight += 10;
        if (count($contact->getPhoto()) === 0) {
            $inCompleteness['photo']['message'] = _('txt-no-profile-photo-given');
            $inCompleteness['photo']['weight']  = 10;
        }
        $totalWeight += 10;
        if (null === $contact->getSaltedPassword()) {
            $inCompleteness['password']['message'] = _('txt-no-password-given');
            $inCompleteness['password']['weight']  = 10;
        }
        $totalWeight += 20;
        if (null === $contact->getContactOrganisation()) {
            $inCompleteness['organisation']['message'] = _('txt-no-organisation-known');
            $inCompleteness['organisation']['weight']  = 20;
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
            $incompletenessWeight  += $itemPerType['weight'];
        }

        return [
            'items'                => $inCompleteness,
            'incompletenessWeight' => $incompletenessWeight,
        ];
    }

    public function findContactsInAffiliation(Affiliation $affiliation): array
    {
        $contacts    = [];
        $contactRole = [];

        /*
         * Add the technical contact
         */
        $contacts[$affiliation->getContact()->getId()]      = $affiliation->getContact();
        $contactRole[$affiliation->getContact()->getId()][] = 'Technical Contact';

        /*
         * Add the financial contact
         */
        if (null !== $affiliation->getFinancial()) {
            $contacts[$affiliation->getFinancial()->getContact()->getId()]      = $affiliation->getFinancial()->getContact();
            $contactRole[$affiliation->getFinancial()->getContact()->getId()][] = 'Financial Contact';
        }

        /*
         * Add the associates
         */
        foreach ($affiliation->getAssociate() as $associate) {
            /*
             * Add the associates
             */
            $contacts[$associate->getId()]      = $associate;
            $contactRole[$associate->getId()][] = 'Associate';
        }

        /*
         * Add the project leader
         */
        if (
            $this->contactIsFromOrganisation(
                $affiliation->getProject()->getContact(),
                $affiliation->getOrganisation()
            )
        ) {
            $contacts[$affiliation->getProject()->getContact()->getId()]      = $affiliation->getProject()->getContact();
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
                $contacts[$proxyContact->getId()]      = $proxyContact;
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
                $contacts[$workpackage->getContact()->getId()]      = $workpackage->getContact();
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
            if (! in_array($optIn->getId(), $optInOptions, false)) {
                $contactOpIn->removeElement($optIn);
            }
        }

        /** Inject the new optIns */
        if (! empty($optInOptions)) {
            foreach ($optInOptions as $optInId) {
                $optIn = $this->find(OptIn::class, (int)$optInId);

                if (! $contactOpIn->contains($optIn)) {
                    $contactOpIn->add($optIn);
                }
            }
        } else {
            $contactOpIn->clear();
        }

        $this->save($contact->setOptIn($contactOpIn));
    }

    public function updateContact(Contact $contact, array $formData): void
    {
        //Handle the contact information
        $gender = $this->generalService->find(Gender::class, (int)$formData['contact']['gender']);
        $title  = $this->generalService->find(Title::class, (int)$formData['contact']['title']);
        $contact->setGender($gender);
        $contact->setTitle($title);
        $contact->setFirstName($formData['contact']['firstName']);
        $contact->setMiddleName(! empty($formData['contact']['middleName']) ? $formData['contact']['middleName'] : null);
        $contact->setLastName($formData['contact']['lastName']);
        $contact->setDepartment(! empty($formData['contact']['department']) ? $formData['contact']['department'] : null);
        $contact->setPosition(! empty($formData['contact']['position']) ? $formData['contact']['position'] : null);

        //Handle the phone data
        foreach ($formData['phone'] as $phoneTypeId => $phoneNumber) {
            /** @var PhoneType $type */
            $type  = $this->find(PhoneType::class, (int)$phoneTypeId);
            $phone = $this->getPhoneByContactAndType($contact, (int)$phoneTypeId);

            if (empty($phoneNumber) && null !== $phone) {
                $this->delete($phone);
            }

            if (! empty($phoneNumber)) {
                if (null === $phone) {
                    $phone = new Phone();
                    $phone->setType($type);
                    $phone->setContact($contact);
                }
                $phone->setPhone($phoneNumber);
                $this->save($phone);
            }
        }

        //Handle the address
        $address = $this->getMailAddress($contact);
        if (! empty($formData['address']['address'])) {
            if (null === $address) {
                /** @var AddressType $mailAddressType */
                $mailAddressType = $this->find(AddressType::class, AddressType::ADDRESS_TYPE_MAIL);
                $address         = new Address();
                $address->setType($mailAddressType);
                $address->setContact($contact);
            }

            $address->setAddress($formData['address']['address']);
            $address->setZipCode($formData['address']['zipCode']);
            $address->setCity($formData['address']['city']);

            /** @var Country $country */
            $country = $this->generalService->find(Country::class, (int)$formData['address']['country']);
            $address->setCountry($country);

            $this->save($address);
        }

        //Delete the address if the form is left empty
        if (
            null !== $address && empty($formData['address']['address']) && empty($formData['address']['zipCode'])
            && empty($data['address']['city'])
            && empty($data['address']['country'])
        ) {
            $this->delete($address);
        }

        //Handle the profile
        $profile = $contact->getProfile();
        if (null === $profile) {
            $profile = new Profile();
            $profile->setContact($contact);
        }

        $profile->setVisible((int)$formData['profile']['visible']);
        $profile->setDescription(
            ! empty($formData['profile']['description']) ? $formData['profile']['description'] : null
        );
        $this->save($profile);
    }

    public function getMailAddress(Contact $contact): ?Address
    {
        return $this->getAddressByTypeId($contact, AddressType::ADDRESS_TYPE_MAIL);
    }

    public function getDirectPhone(Contact $contact): ?Phone
    {
        return $this->getPhoneByContactAndType($contact, PhoneType::PHONE_TYPE_DIRECT);
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

    public function updateCollectionInSearchEngine(bool $clearIndex = false): void
    {
        $contacts = $this->findAll(Contact::class);

        $collection = [];

        /** @var Contact $contact */
        foreach ($contacts as $contact) {
            $collection[] = $this->prepareSearchUpdate($contact);
        }

        $this->contactSearchService->updateIndexWithCollection($collection, $clearIndex);
    }

    public function updateProfileCollectionInSearchEngine(bool $clearIndex = false): void
    {
        $contacts   = $this->findContactsWithActiveProfile();
        $collection = [];

        /** @var Contact $contact */
        foreach ($contacts as $contact) {
            //Skip the inactive, the not activated and the not anonymised
            if (! $contact->isActive() || ! $contact->isActivated() || $contact->isAnonymised()) {
                continue;
            }

            $collection[] = $this->prepareProfileSearchUpdate($contact);
        }

        $this->profileSearchService->updateIndexWithCollection($collection, $clearIndex);
    }

    public function findContactsWithActiveProfile(bool $onlyPublic = true): array
    {
        /** @var \Contact\Repository\Contact $repository */
        $repository = $this->entityManager->getRepository(Contact::class);

        return $repository->findContactsWithActiveProfile($onlyPublic);
    }
}
