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

namespace Contact\Controller;

use Admin\Service\AdminService;
use Contact\Controller\Plugin\MergeContact;
use Contact\Entity\Contact;
use Contact\Entity\ContactOrganisation;
use Contact\Entity\OptIn;
use Contact\Entity\Photo;
use Contact\Entity\Profile;
use Contact\Form\AddProject;
use Contact\Form\ContactFilter;
use Contact\Form\ContactMerge;
use Contact\Form\Impersonate;
use Contact\Form\Import;
use Contact\Service\ContactService;
use Contact\Service\FormService;
use Contact\Service\SelectionService;
use Deeplink\Entity\Target;
use Deeplink\Service\DeeplinkService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Event\Service\RegistrationService;
use General\Service\GeneralService;
use Organisation\Service\OrganisationService;
use Program\Service\CallService;
use Project\Service\IdeaService;
use Project\Service\ProjectService;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\Paginator\Paginator;
use Zend\Session\Container;
use Zend\Validator\File\ImageSize;
use Zend\Validator\File\MimeType;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Class ContactManagerController.
 */
/*final*/ class ContactAdminController extends ContactAbstractController
{
    /**
     * @var ContactService
     */
    private $contactService;
    /**
     * @var SelectionService
     */
    private $selectionService;
    /**
     * @var OrganisationService
     */
    private $organisationService;
    /**
     * @var CallService
     */
    private $callService;
    /**
     * @var ProjectService
     */
    private $projectService;
    /**
     * @var IdeaService
     */
    private $ideaService;
    /**
     * @var AdminService
     */
    private $adminService;
    /**
     * @var RegistrationService
     */
    private $registrationService;
    /**
     * @var DeeplinkService
     */
    private $deeplinkService;
    /**
     * @var GeneralService
     */
    private $generalService;
    /**
     * @var FormService
     */
    private $formService;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(
        ContactService      $contactService,
        SelectionService    $selectionService,
        OrganisationService $organisationService,
        CallService         $callService,
        ProjectService      $projectService,
        IdeaService         $ideaService,
        AdminService        $adminService,
        RegistrationService $registrationService,
        DeeplinkService     $deeplinkService,
        GeneralService      $generalService,
        FormService         $formService,
        TranslatorInterface $translator,
        EntityManager       $entityManager
    ) {
        $this->contactService      = $contactService;
        $this->selectionService    = $selectionService;
        $this->organisationService = $organisationService;
        $this->callService         = $callService;
        $this->projectService      = $projectService;
        $this->ideaService         = $ideaService;
        $this->adminService        = $adminService;
        $this->registrationService = $registrationService;
        $this->deeplinkService     = $deeplinkService;
        $this->generalService      = $generalService;
        $this->formService         = $formService;
        $this->translator          = $translator;
        $this->entityManager       = $entityManager;
    }

    public function listAction(): ViewModel
    {
        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getContactFilter();
        $contactQuery = $this->contactService->findFiltered(Contact::class, $filterPlugin->getFilter());

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($contactQuery, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new ContactFilter($this->entityManager);

        $form->setData(['filter' => $filterPlugin->getFilter()]);

        return new ViewModel(
            [
                'paginator'      => $paginator,
                'form'           => $form,
                'encodedFilter'  => urlencode($filterPlugin->getHash()),
                'order'          => $filterPlugin->getOrder(),
                'direction'      => $filterPlugin->getDirection(),
                'projectService' => $this->projectService,
            ]
        );
    }

    public function listDuplicateAction(): ViewModel
    {
        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getContactFilter();
        $organisationQuery = $this->contactService
            ->findDuplicateContacts($filterPlugin->getFilter());

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($organisationQuery, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new ContactFilter($this->entityManager);

        $form->setData(['filter' => $filterPlugin->getFilter()]);

        return new ViewModel(
            [
                'paginator'           => $paginator,
                'form'                => $form,
                'encodedFilter'       => urlencode($filterPlugin->getHash()),
                'organisationService' => $this->organisationService,
                'order'               => $filterPlugin->getOrder(),
                'direction'           => $filterPlugin->getDirection(),
            ]
        );
    }

    public function listInactiveAction(): ViewModel
    {
        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getContactFilter();
        $query = $this->contactService->findInactiveContacts($filterPlugin->getFilter());

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($query, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new ContactFilter($this->entityManager);
        $form->setData(['filter' => $filterPlugin->getFilter()]);

        return new ViewModel(
            [
                'paginator'           => $paginator,
                'form'                => $form,
                'encodedFilter'       => urlencode($filterPlugin->getHash()),
                'organisationService' => $this->organisationService,
                'order'               => $filterPlugin->getOrder(),
                'direction'           => $filterPlugin->getDirection(),
            ]
        );
    }

    public function exportAction(): Response
    {
        $filterPlugin = $this->getContactFilter();

        $contactQuery = $this->contactService->findFiltered(Contact::class, $filterPlugin->getFilter());

        /** @var Contact[] $contacts */
        $contacts = $contactQuery->getQuery()->getResult();

        // Open the output stream
        $fh = fopen('php://output', 'wb');

        ob_start();

        fputcsv(
            $fh,
            [
                'Email',
                'Firstname',
                'Lastname',
                'Organisation',
                'Country',
            ]
        );

        foreach ($contacts as $contact) {
            fputcsv(
                $fh,
                [
                    $contact->getEmail(),
                    $contact->getFirstName(),
                    trim(sprintf("%s %s", $contact->getMiddleName(), $contact->getLastName())),
                    null !== $contact->getContactOrganisation() ? $contact->getContactOrganisation()
                        ->getOrganisation() : '',
                    null !== $contact->getContactOrganisation() ? $contact->getContactOrganisation()
                        ->getOrganisation()->getCountry() : '',
                ]
            );
        }

        $string = ob_get_clean();

        // Convert to UTF-16LE
        $string = mb_convert_encoding($string, 'UTF-16LE', 'UTF-8');

        // Prepend BOM
        $string = "\xFF\xFE" . $string;

        /** @var Response $response */
        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'text/csv');
        $headers->addHeaderLine(
            'Content-Disposition',
            "attachment; filename=\"contact.csv\""
        );
        $headers->addHeaderLine('Accept-Ranges', 'bytes');
        $headers->addHeaderLine('Content-Length', strlen($string));

        $response->setContent($string);

        return $response;
    }

    public function viewAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $contact = $this->contactService->findContactById((int)$this->params('id'));

        if ($contact === null) {
            return $this->notFoundAction();
        }

        $selections = $this->selectionService->findSelectionsByContact($contact);
        $optIn = $this->contactService->findAll(OptIn::class);

        $data = $request->getPost()->toArray();
        if ($request->isPost() && !empty($data['selection'])) {
            foreach ((array)$data['selection'] as $selectionId) {
                $selection = $this->selectionService->findSelectionById((int) $selectionId);
                if (null === $selection) {
                    continue;
                }
                foreach ($selection->getSelectionContact() as $selectionContact) {
                    if ($selectionContact->getContact() === $contact) {
                        $this->selectionService->delete($selectionContact);

                        $this->flashMessenger()->addSuccessMessage(
                            sprintf(
                                $this->translator->translate(
                                    "txt-contact-%s-has-removed-form-selection-%s-successfully"
                                ),
                                $contact->getDisplayName(),
                                $selection->getSelection()
                            )
                        );
                    }
                }
            }

            return $this->redirect()->toRoute('zfcadmin/contact-admin/view', ['id' => $contact->getId()]);
        }

        //Handle the optIn
        if ($request->isPost()) {
            if (isset($data['updateOptIn'])) {
                $this->contactService->updateOptInForContact($contact, $data['optIn'] ?? []);

                $changelogMessage = sprintf(
                    $this->translator->translate("txt-opt-in-of-contact-%s-has-been-updated-successfully"),
                    $contact->getDisplayName()
                );

                $this->flashMessenger()->addSuccessMessage($changelogMessage);
                $this->contactService->addNoteToContact($changelogMessage, 'office', $this->identity());
            }

            if (isset($data['activate'])) {
                $contact->setDateActivated(new \DateTime());

                $this->contactService->save($contact);

                $changelogMessage = sprintf(
                    $this->translator->translate("txt-contact-%s-has-been-activated-successfully"),
                    $contact->getDisplayName()
                );

                $this->flashMessenger()->addSuccessMessage($changelogMessage);

                $this->contactService->addNoteToContact($changelogMessage, 'office', $contact);
            }

            if (isset($data['deactivate'])) {
                $contact->setDateActivated(null);

                $this->contactService->save($contact);

                $changelogMessage = sprintf(
                    $this->translator->translate("txt-contact-%s-has-been-de-activated-successfully"),
                    $contact->getDisplayName()
                );

                $this->flashMessenger()->addSuccessMessage($changelogMessage);

                $this->contactService->addNoteToContact($changelogMessage, 'office', $contact);
            }

            if (isset($data['anonymise'])) {
                $contact->setDateAnonymous(new \DateTime());
                $contact->setFirstName(null);
                $contact->setMiddleName(null);
                $contact->setLastName(null);
                $contact->setEmail(null);
                $contact->setEmailAddress(null);
                $contact->setPhoto(null);
                $contact->setPosition(null);
                $contact->setDepartment(null);
                $contact->setOptIn(null);
                foreach ($contact->getNda() as $nda) {
                    $this->callService->delete($nda);
                }
                if ($contact->getProfile()) {
                    $contact->getProfile()->setVisible(Profile::VISIBLE_HIDDEN);
                }

                $this->contactService->save($contact);

                $changelogMessage = sprintf(
                    $this->translator->translate("txt-contact-%s-has-been-anonymised-successfully"),
                    $contact->getDisplayName()
                );

                $this->flashMessenger()->addSuccessMessage($changelogMessage);

                $this->contactService->addNoteToContact($changelogMessage, 'office', $contact);
            }

            if (isset($data['deanonymise'])) {
                $contact->setDateAnonymous(null);

                $this->contactService->save($contact);

                $changelogMessage = sprintf(
                    $this->translator->translate("txt-contact-%s-has-been-de-anonymised-successfully"),
                    $contact->getDisplayName()
                );

                $this->flashMessenger()->addSuccessMessage($changelogMessage);

                $this->contactService->addNoteToContact($changelogMessage, 'office', $contact);
            }

            return $this->redirect()->toRoute('zfcadmin/contact-admin/view', ['id' => $contact]);
        }

        $mergeForm = new ContactMerge($this->entityManager, $contact);

        return new ViewModel(
            [
                'contact'             => $contact,
                'contactService'      => $this->contactService,
                'selections'          => $selections,
                'selectionService'    => $this->selectionService,
                'projects'            => $this->projectService->findProjectParticipationByContact($contact),
                'projectService'      => $this->projectService,
                'optIn'               => $optIn,
                'callService'         => $this->callService,
                'registrationService' => $this->registrationService,
                'ideaService'         => $this->ideaService,
                'mergeForm'           => $mergeForm,
            ]
        );
    }

    public function permitAction(): ViewModel
    {
        $contact = $this->contactService->findContactById((int)$this->params('id'));

        if (null === $contact) {
            return $this->notFoundAction();
        }

        $this->adminService->findPermitContactByContact($contact);

        return new ViewModel(
            [
                'contact' => $contact,
            ]
        );
    }

    public function impersonateAction(): ViewModel
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $contact = $this->contactService->findContactById((int)$this->params('id'));
        $form = new Impersonate($this->entityManager);

        $data = $request->getPost()->toArray();

        $form->setData($data);
        $deeplink = false;
        if ($request->isPost() && $form->isValid()) {
            $data = $form->getData();

            /** @var Target $target */
            $target = $this->deeplinkService->find(Target::class, (int)$data['target']);
            $key = (!empty($data['key']) ? $data['key'] : null);
            //Create a deeplink for the user which redirects to the profile-page
            $deeplink = $this->deeplinkService->createDeeplink($target, $contact, null, $key);
        }

        return new ViewModel(
            [
                'deeplink'       => $deeplink,
                'contact'        => $contact,
                'contactService' => $this->contactService,
                'form'           => $form,
            ]
        );
    }

    public function editAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $contact = $this->contactService->findContactById((int)$this->params('id'));

        if ($contact === null) {
            return $this->notFoundAction();
        }

        //Get contacts in an organisation
        if ($this->contactService->hasOrganisation($contact)) {
            $data = array_merge(
                [
                    'contact' => [
                        'organisation' => $contact->getContactOrganisation()->getOrganisation()->getId(),
                        'branch'       => $contact->getContactOrganisation()->getBranch(),
                    ],
                ],
                $request->getPost()->toArray()
            );

            $form = $this->formService->prepare($contact, $data);
            $form->get('contact_entity_contact')->get("organisation")
                ->injectOrganisation($contact->getContactOrganisation()->getOrganisation());
        } else {
            $data = $request->getPost()->toArray();
            $form = $this->formService->prepare($contact, $data);
        }

        /** Show or hide buttons based on the status of a contact */
        if ($this->contactService->isActive($contact)) {
            $form->remove('reactivate');
        } else {
            $form->remove('deactivate');
        }


        if ($request->isPost()) {

            /** Deactivate a contact */
            if (isset($data['deactivate'])) {
                $changelogMessage = \sprintf($this->translator->translate("txt-contact-%s-has-been-deleted"), $contact->parseFullName());
                $this->flashMessenger()->addSuccessMessage($changelogMessage);

                $this->contactService->addNoteToContact($changelogMessage, 'office', $contact);

                $contact->setDateEnd(new \DateTime());
                $this->contactService->save($contact);

                return $this->redirect()->toRoute('zfcadmin/contact-admin/view', ['id' => $contact->getId()]);
            }

            /** Reactivate a contact */
            if (isset($data['reactivate'])) {
                $changelogMessage = \sprintf($this->translator->translate("txt-contact-%s-has-been-undeleted"), $contact->parseFullName());
                $this->flashMessenger()->addSuccessMessage($changelogMessage);

                $this->contactService->addNoteToContact($changelogMessage, 'office', $contact);

                $contact->setDateEnd(null);
                $this->contactService->save($contact);

                return $this->redirect()->toRoute('zfcadmin/contact-admin/view', ['id' => $contact->getId()]);
            }

            /** Cancel the form */
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/contact-admin/view', ['id' => $contact->getId()]);
            }

            /** Handle the form */
            if ($form->isValid()) {
                /**
                 * @var $contact Contact
                 */
                $contact = $form->getData();

                //Reset the access when the array is empty
                if (!isset($data['contact_entity_contact']['access'])) {
                    $contact->setAccess(new ArrayCollection());
                }

                $contact = $this->contactService->save($contact);

                //Reset the roles of this contact
                $this->adminService->refreshAccessRolesByContact($contact);
                $this->adminService->resetCachedAccessRolesByContact($contact);

                /** Update the organisation if there is any */
                if (isset($data['contact_entity_contact']['organisation'])) {
                    //Update the contactOrganisation (if set)
                    if (null === $contact->getContactOrganisation()) {
                        $contactOrganisation = new ContactOrganisation();
                        $contactOrganisation->setContact($contact);
                    } else {
                        $contactOrganisation = $contact->getContactOrganisation();
                    }

                    $contactOrganisation->setBranch(
                        empty($data['contact_entity_contact']['branch']) ? null
                            : $data['contact_entity_contact']['branch']
                    );
                    $contactOrganisation->setOrganisation(
                        $this->organisationService->findOrganisationById(
                            (int)$data['contact_entity_contact']['organisation']
                        )
                    );

                    $this->contactService->save($contactOrganisation);
                }

                //Handle the file upload
                $fileData = $this->params()->fromFiles();

                if (!empty($fileData['contact_entity_contact']['file']['name'])
                    && $fileData['contact_entity_contact']['file']['error'] === 0
                ) {
                    /** @var Photo $photo */
                    $photo = $contact->getPhoto()->first();
                    if (!$photo) {
                        //Create a photo element
                        $photo = new Photo();
                    }
                    $photo->setPhoto(file_get_contents($fileData['contact_entity_contact']['file']['tmp_name']));
                    $photo->setThumb(file_get_contents($fileData['contact_entity_contact']['file']['tmp_name']));
                    $photo->setContact($contact);
                    $imageSizeValidator = new ImageSize();
                    $imageSizeValidator->isValid($fileData['contact_entity_contact']['file']);
                    $photo->setWidth($imageSizeValidator->width);
                    $photo->setHeight($imageSizeValidator->height);

                    $fileTypeValidator = new MimeType();
                    $fileTypeValidator->isValid($fileData['contact_entity_contact']['file']);
                    $photo->setContentType(
                        $this->generalService->findContentTypeByContentTypeName($fileTypeValidator->type)
                    );

                    $this->contactService->save($photo);
                }

                return $this->redirect()->toRoute('zfcadmin/contact-admin/view', ['id' => $contact->getId()]);
            }
        }

        return new ViewModel(
            [
                'contactService' => $this->contactService,
                'contact'        => $contact,
                'form'           => $form,
            ]
        );
    }

    public function newAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $contact = new Contact();
        $data = $request->getPost()->toArray();
        $form = $this->formService->prepare($contact, $data);

        // Disable the inarray validator for organisations
        $form->get('contact_entity_contact')->get('organisation')->setDisableInArrayValidator(true);

        // Show or hide buttons based on the status of a contact
        $form->remove('reactivate');
        $form->remove('deactivate');

        if ($request->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/contact-admin/list');
            }

            if ($form->isValid()) {
                /** @var Contact $contact */
                $contact = $form->getData();
                $contact = $this->contactService->save($contact);

                if (isset($data['contact_entity_contact']['organisation'])) {
                    $contactOrganisation = new ContactOrganisation();
                    $contactOrganisation->setContact($contact);

                    $contactOrganisation->setBranch(
                        \strlen($data['contact_entity_contact']['branch']) === 0 ? null
                            : $data['contact_entity_contact']['branch']
                    );
                    $contactOrganisation->setOrganisation(
                        $this->organisationService->findOrganisationById(
                            (int)$data['contact_entity_contact']['organisation']
                        )
                    );

                    $this->contactService->save($contactOrganisation);
                }

                return $this->redirect()->toRoute('zfcadmin/contact-admin/view', ['id' => $contact->getId()]);
            }
        }

        return new ViewModel(
            [
                'form' => $form,
            ]
        );
    }

    public function importAction(): ViewModel
    {
        \set_time_limit(0);

        /** @var Request $request */
        $request = $this->getRequest();
        $data = \array_merge_recursive(
            $request->getPost()->toArray(),
            $request->getFiles()->toArray()
        );
        $form = new Import($this->contactService, $this->selectionService);
        $form->setData($data);

        /** store the data in the session, so we can use it when we really handle the import */
        $importSession = new Container('import');

        $handleImport = null;
        if ($request->isPost()) {
            if (isset($data['upload']) && $form->isValid()) {
                $fileData = file_get_contents($data['file']['tmp_name']);

                $importSession->active = true;
                $importSession->fileData = $fileData;

                $handleImport = $this->handleImport(
                    $this->identity(),
                    $fileData,
                    null,
                    $data['optIn'] ?? [],
                    null !== $data['selection_id'] ? (int)$data['selection_id'] : null,
                    $data['selection']
                );
            }

            if (isset($data['import'], $data['key']) && $importSession->active) {
                $handleImport = $this->handleImport(
                    $this->identity(),
                    $importSession->fileData,
                    $data['key'],
                    $data['optIn'] ?? [],
                    null !== $data['selection_id'] ? (int)$data['selection_id'] : null,
                    $data['selection']
                );
            }
        }

        return new ViewModel(['form' => $form, 'handleImport' => $handleImport]);
    }

    public function searchAction(): JsonModel
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $search = $request->getPost()->get('search');

        $results = [];
        foreach ($this->contactService->searchContacts($search) as $result) {
            $text = \trim(
                \sprintf(
                    '%s, %s (%s)',
                    \trim(\sprintf('%s %s', $result['middleName'], $result['lastName'])),
                    $result['firstName'],
                    $result['email']
                )
            );

            // Do a fall-back to the email when the name is empty
            if (empty($text)) {
                $text = $result['email'];
            }

            $results[] = [
                'value'        => $result['id'],
                'text'         => $text,
                'name'         => \sprintf(
                    '%s, %s',
                    \trim(\sprintf('%s %s', $result['middleName'], $result['lastName'])),
                    $result['firstName']
                ),
                'id'           => $result['id'],
                'email'        => $result['email'],
                'organisation' => $result['organisation'],
            ];
        }

        return new JsonModel($results);
    }

    public function mergeAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        /** @var Contact $source */
        $source  = $this->contactService->findContactById((int)$this->params('sourceId'));
        /** @var Contact $target */
        $target  = $this->contactService->findContactById((int)$this->params('targetId'));

        if (($source === null) || ($target === null)) {
            return $this->notFoundAction();
        }

        if ($request->isPost()) {
            $data = $request->getPost()->toArray();

            // Cancel the merge
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/contact-admin/view',
                    ['id' => $target->getId()],
                    ['fragment' => 'merge']
                );
            }

            // Swap source and destination
            if (isset($data['swap'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/contact-admin/merge',
                    ['sourceId' => $target->getId(), 'targetId' => $source->getId()]
                );
            }

            // Perform the merge
            if (isset($data['merge'])) {
                $result = $this->mergeContact()->merge($source, $target);
                $tab    = 'general';
                if ($result['success']) {
                    $this->flashMessenger()->addSuccessMessage(
                        $this->translator->translate('txt-contacts-have-been-successfully-merged')
                    );
                } else {
                    $tab = 'merge';
                    $this->flashMessenger()->addErrorMessage(
                        $this->translator->translate('txt-contact-merge-failed')
                    );
                }

                return $this->redirect()->toRoute(
                    'zfcadmin/contact-admin/view',
                    ['id' => $target->getId()],
                    ['fragment' => $tab]
                );
            }
        }

        return new ViewModel(
            [
                'errors'         => $this->mergeContact()->checkMerge($source, $target),
                'source'         => $source,
                'target'         => $target,
                'mergeForm'      => new ContactMerge(),
                'contactService' => $this->contactService,
            ]
        );
    }

    public function addProjectAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $contact = $this->contactService->findContactById((int)$this->params('id'));

        if ($contact === null) {
            return $this->notFoundAction();
        }

        $form = new AddProject($this->projectService);

        if ($request->isPost()) {
            $data = $request->getPost()->toArray();

            // Cancel the merge
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/contact-admin/view',
                    ['id' => $contact->getId()],
                    ['fragment' => 'project']
                );
            }
        }

        // Add a contact to a project as technical contact/reviewer/associate

        return new ViewModel([
            'contact' => $contact,
            'form'    => $form
        ]);
    }
}
