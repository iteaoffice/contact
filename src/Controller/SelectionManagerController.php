<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Selection
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Controller;

use Contact\Controller\Plugin\SelectionExport;
use Contact\Entity\Selection;
use Contact\Form\AddContactToSelection;
use Contact\Form\Impersonate;
use Contact\Form\SelectionContacts;
use Contact\Form\SelectionFilter;
use Contact\Service\ContactService;
use Contact\Service\FormService;
use Contact\Service\SelectionContactService;
use Contact\Service\SelectionService;
use Deeplink\Entity\Target;
use Deeplink\Service\DeeplinkService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Zend\Http\Response;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Paginator\Paginator;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Class SelectionManagerController
 *
 * @package Contact\Controller
 */
final class SelectionManagerController extends ContactAbstractController
{
    /**
     * @var ContactService
     */
    private $contactService;
    /**
     * @var SelectionContactService
     */
    private $selectionContactService;
    /**
     * @var SelectionService
     */
    private $selectionService;
    /**
     * @var DeeplinkService
     */
    private $deeplinkService;
    /**
     * @var FormService
     */
    private $formService;
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        ContactService $contactService,
        SelectionContactService $selectionContactService,
        SelectionService $selectionService,
        DeeplinkService $deeplinkService,
        FormService $formService,
        EntityManager $entityManager,
        TranslatorInterface $translator
    ) {
        $this->contactService = $contactService;
        $this->selectionContactService = $selectionContactService;
        $this->selectionService = $selectionService;
        $this->deeplinkService = $deeplinkService;
        $this->formService = $formService;
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }


    public function listAction(): ViewModel
    {
        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getContactFilter();
        $contactQuery = $this->contactService->findFiltered(Selection::class, $filterPlugin->getFilter());

        $paginator
            = new Paginator(new PaginatorAdapter(new ORMPaginator($contactQuery, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new SelectionFilter($this->selectionService);

        $form->setData(['filter' => $filterPlugin->getFilter()]);

        return new ViewModel(
            [
                'paginator'        => $paginator,
                'form'             => $form,
                'encodedFilter'    => urlencode($filterPlugin->getHash()),
                'order'            => $filterPlugin->getOrder(),
                'direction'        => $filterPlugin->getDirection(),
                'selectionService' => $this->selectionService
            ]
        );
    }

    public function viewAction(): ViewModel
    {
        $selection = $this->selectionService->findSelectionById((int)$this->params('id'));

        if (null === $selection) {
            return $this->notFoundAction();
        }

        try {
            $contacts = $this->selectionContactService->findContactsInSelection($selection, true);

            $error = false;
        } catch (\Throwable $e) {
            $contacts = [];
            $error = $e->getMessage();
        }

        return new ViewModel(
            [
                'selectionService' => $this->selectionService,
                'selection'        => $selection,
                'contacts'         => $contacts,
                'error'            => $error,
            ]
        );
    }

    public function generateDeeplinksAction(): ViewModel
    {
        \set_time_limit(0);

        $selection = $this->selectionService->findSelectionById((int)$this->params('id'));

        if (null === $selection) {
            return $this->notFoundAction();
        }

        $form = new Impersonate($this->entityManager);

        $request = $this->getRequest();
        $data = $request->getPost()->toArray();

        $form->setData($data);

        $deeplinks = [];

        if ($request->isPost() && $form->isValid()) {
            $data = $form->getData();

            /** @var Target $target */
            $target = $this->deeplinkService->find(Target::class, (int)$data['target']);
            $key = (!empty($data['key']) ? $data['key'] : null);

            //Create a deeplink for the user which redirects to the profile-page
            foreach ($this->selectionContactService->findContactsInSelection($selection) as $contact) {
                $deeplinks[] = [
                    'contact'  => $contact,
                    'deeplink' => $this->deeplinkService->createDeeplink($target, $contact, null, $key)
                ];
            }
        }


        return new ViewModel(
            [
                'selection' => $selection,
                'form'      => $form,
                'deeplinks' => $deeplinks
            ]
        );
    }

    public function editContactsAction()
    {
        $selection = $this->selectionService->findSelectionById((int)$this->params('id'));

        if (null === $selection) {
            return $this->notFoundAction();
        }

        $data = array_merge(
            [
                'type' => $this->selectionService->isSql($selection) ? Selection::TYPE_SQL : Selection::TYPE_FIXED,
                'sql'  => $this->selectionService->isSql($selection) ? $selection->getSql()->getQuery() : null,
            ],
            $this->getRequest()->getPost()->toArray()
        );


        $form = new SelectionContacts($this->selectionService);
        $form->setData($data);


        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/selection/view', ['id' => $selection->getId()]);
            }

            $this->selectionService->updateSelectionContacts($selection, $data);

            return $this->redirect()->toRoute('zfcadmin/selection/view', ['id' => $selection->getId()]);
        }

        return new ViewModel(
            [
                'selectionService' => $this->selectionService,
                'selection'        => $selection,
                'form'             => $form,
            ]
        );
    }

    public function addContactAction()
    {
        $contact = $this->contactService->findContactById((int)$this->params('contactId'));

        if (null === $contact) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();


        $form = new AddContactToSelection($this->selectionService);
        $form->setData($data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/contact-admin/view', ['id' => $contact->getId()]);
            }

            //Find the selection
            $selection = $this->selectionService->findSelectionById((int)$data['selection']);

            if (null === $selection) {
                throw new \InvalidArgumentException('Selection cannot be found');
            }

            $this->selectionService->addContactToSelection($selection, $contact);

            $this->flashMessenger()->addSuccessMessage(
                sprintf(
                    $this->translator->translate("txt-contact-%s-has-been-added-to-selection-%s-successfully"),
                    $contact->parseFullName(),
                    $selection->getSelection()
                )
            );

            return $this->redirect()->toRoute('zfcadmin/selection/view', ['id' => $selection->getId()]);
        }

        return new ViewModel(
            [
                'contact' => $contact,
                'form'    => $form,
            ]
        );
    }

    public function editAction()
    {
        $selection = $this->selectionService->findSelectionById((int)$this->params('id'));

        if (null === $selection) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();

        $form = $this->formService->prepare($selection, $data);

        if (null !== $selection->getContact()) {
            $form->get($selection->get('underscore_entity_name'))->get('contact')->injectContact(
                $selection->getContact()
            );
        }

        if (!$selection->getMailing()->isEmpty()) {
            $form->remove('delete');
        }
        if (!$selection->getAccess()->isEmpty()) {
            $form->remove('delete');
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/selection/view', ['id' => $selection->getId()]);
            }

            if (isset($data['delete'])) {
                $this->selectionService->delete($selection);

                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(
                        sprintf(
                            $this->translator->translate("txt-selection-%s-has-successfully-been-removed"),
                            $selection->getSelection()
                        )
                    );

                return $this->redirect()->toRoute('zfcadmin/selection/list');
            }

            /**
             * Save the form
             */
            if ($form->isValid()) {
                /**
                 * @var $selection Selection
                 */
                $selection = $form->getData();
                $this->selectionService->save($selection);

                return $this->redirect()->toRoute('zfcadmin/selection/view', ['id' => $selection->getId()]);
            }
        }

        return new ViewModel(
            [
                'form'             => $form,
                'selectionService' => $this->selectionService,
                'selection'        => $selection,
            ]
        );
    }

    public function newAction()
    {
        $data = $this->getRequest()->getPost()->toArray();

        $form = $this->formService->prepare(Selection::class, $data);
        $form->remove('delete');

        $form->get('contact_entity_selection')->get('contact')->injectContact($this->identity());

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/selection/list');
            }

            if ($form->isValid()) {
                /** @var Selection $selection */
                $selection = $form->getData();
                $this->selectionService->save($selection);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate("txt-selection-%s-has-been-created-successfully"),
                        $selection->getSelection()
                    )
                );

                return $this->redirect()->toRoute('zfcadmin/selection/view', ['id' => $selection->getId()]);
            }
        }

        return new ViewModel(['form' => $form]);
    }

    public function getContactsAction(): JsonModel
    {
        $selection = $this->selectionService->findSelectionById((int)$this->params()->fromPost('id'));

        if (null === $selection) {
            return new JsonModel([]);
        }

        $results = [];
        foreach ($this->selectionContactService->findContactsInSelection($selection) as $contact) {
            $text = trim(sprintf('%s (%s)', $contact->getFormName(), $contact->getEmail()));

            /*
             * Do a fall-back to the email when the name is empty
             */
            if ('' === \strlen($text)) {
                $text = $contact->getEmail();
            }

            $results[] = [
                'value'        => $contact->getId(),
                'text'         => $text,
                'name'         => $contact->getFormName(),
                'id'           => $contact->getId(),
                'email'        => $contact->getEmail(),
                'organisation' => null === $contact->getContactOrganisation()
                    ?: $contact->getContactOrganisation()->getOrganisation()->getOrganisation(),
            ];
        }

        return new JsonModel($results);
    }


    public function exportAction(): Response
    {
        $selection = $this->selectionService->findSelectionById((int)$this->params('id'));

        if (null === $selection) {
            $response = new Response();
            return $response->setStatusCode(Response::STATUS_CODE_404);
        }


        $type = $this->params('type') === 'csv' ? SelectionExport::EXPORT_CSV : SelectionExport::EXPORT_EXCEL;

        return $this->selectionExport($selection, $type)->parseResponse();
    }
}
