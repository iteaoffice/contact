<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Selection
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (https://itea3.org)
 */

namespace Contact\Controller;

use Contact\Entity\Selection;
use Contact\Form\SelectionContacts;
use Contact\Form\SelectionFilter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Zend\Paginator\Paginator;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 *
 */
class SelectionManagerController extends ContactAbstractController
{
    /**
     * @return ViewModel
     */
    public function listAction()
    {
        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getContactFilter();
        $contactQuery = $this->getContactService()->findEntitiesFiltered('selection', $filterPlugin->getFilter());

        $paginator
            = new Paginator(new PaginatorAdapter(new ORMPaginator($contactQuery, false)));
        $paginator->setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 15);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator->getDefaultItemCountPerPage()));

        $form = new SelectionFilter($this->getSelectionService());

        $form->setData(['filter' => $filterPlugin->getFilter()]);

        return new ViewModel([
            'paginator'     => $paginator,
            'form'          => $form,
            'encodedFilter' => urlencode($filterPlugin->getHash()),
            'order'         => $filterPlugin->getOrder(),
            'direction'     => $filterPlugin->getDirection(),
        ]);
    }

    /**
     * @return ViewModel
     */
    public function viewAction()
    {
        $selectionService = $this->getSelectionService()->setSelectionId($this->getEvent()->getRouteMatch()
            ->getParam('id'));

        /**
         * If the query is wrong an exception is thrown now
         */
        try {
            $contacts = $this->getContactService()->findContactsInSelectionAsArray($selectionService->getSelection());
            $error = false;
        } catch (\Exception $e) {
            $contacts = [];
            $error = $e->getMessage();
        }

        return new ViewModel([
            'selectionService' => $selectionService,
            'contacts'         => $contacts,
            'error'            => $error,
        ]);
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function editContactsAction()
    {
        $selectionService = $this->getSelectionService()->setSelectionId($this->params('id'));
        if ($selectionService->isEmpty()) {
            return $this->notFoundAction();
        }

        $data = array_merge([
            'type' => $selectionService->isSql() ? Selection::TYPE_SQL : Selection::TYPE_FIXED,
            'sql'  => $selectionService->isSql() ? $selectionService->getSelection()->getSql()->getQuery() : null
        ], $this->getRequest()->getPost()->toArray());


        $form = new SelectionContacts($this->getSelectionService());
        $form->setData($data);


        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()
                    ->toRoute('zfcadmin/selection-manager/view', ['id' => $selectionService->getSelection()->getId()]);
            }

            $this->getSelectionService()->updateSelectionContacts($selectionService->getSelection(), $data);

            /**
             * @var $selection Selection
             */

            return $this->redirect()
                ->toRoute('zfcadmin/selection-manager/view', ['id' => $selectionService->getSelection()->getId()]);
        }

        return new ViewModel([
            'selectionService' => $selectionService,
            'form'             => $form
        ]);
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function editAction()
    {
        $selectionService = $this->getSelectionService()->setSelectionId($this->params('id'));
        if ($selectionService->isEmpty()) {
            return $this->notFoundAction();
        }

        $data = array_merge($this->getRequest()->getPost()->toArray());

        $form = $this->getFormService()->prepare(
            $selectionService->getSelection()->get('entity_name'),
            $selectionService->getSelection(),
            $data
        );

        $form->setAttribute('class', 'form-horizontal');

        $form->get('selection')->get('contact')->setValueOptions([
            $selectionService->getSelection()->getContact()->getId() => $selectionService->getSelection()->getContact()
                ->getFormName()
        ])->setDisableInArrayValidator(true);


        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()
                    ->toRoute('zfcadmin/selection-manager/view', ['id' => $selectionService->getSelection()->getId()]);
            }

            if (isset($data['delete'])) {
                $this->getSelectionService()->removeEntity($selectionService->getSelection());

                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(sprintf(
                        $this->translate("txt-selection-%s-has-successfully-been-removed"),
                        $selectionService->getSelection()->getSelection()
                    ));

                return $this->redirect()->toRoute('zfcadmin/selection-manager/list');
            }

            /**
             * Save the form
             */
            if ($form->isValid()) {
                /**
                 * @var $selection Selection
                 */
                $selection = $form->getData();
                $this->getSelectionService()->updateEntity($selection);

                return $this->redirect()
                    ->toRoute('zfcadmin/selection-manager/view', ['id' => $selectionService->getSelection()->getId()]);
            } else {
                var_dump($form->getInputFilter()->getMessages());
            }
        }

        return new ViewModel([
            'form'             => $form,
            'selectionService' => $selectionService
        ]);
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function newAction()
    {
        $selection = new Selection();

        $data = array_merge($this->getRequest()->getPost()->toArray());

        $form = $this->getFormService()->prepare($selection->get('entity_name'), $selection, $data);

        $form->setAttribute('class', 'form-horizontal');

        $form->get('selection')->get('contact')->setValueOptions([
            $this->zfcUserAuthentication()->getIdentity()->getId() => $this->zfcUserAuthentication()->getIdentity()
                ->getFormName()
        ])->setDisableInArrayValidator(true);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/selection-manager/list');
            }

            /**
             * Save the form
             */
            if ($form->isValid()) {
                /**
                 * @var $selection Selection
                 */
                $selection = $form->getData();
                $this->getSelectionService()->updateEntity($selection);

                return $this->redirect()->toRoute('zfcadmin/selection-manager/view', ['id' => $selection->getId()]);
            }
        }

        return new ViewModel(['form' => $form]);
    }

    /**
     * @return JsonModel
     */
    public function getContactsAction()
    {
        $id = $this->getRequest()->getPost()->get('id');

        $selectionService = $this->getSelectionService()->setSelectionId($id);

        if ($selectionService->isEmpty()) {
            return $this->notFoundAction();
        }

        $results = [];
        foreach ($this->getContactService()->findContactsInSelection($selectionService->getSelection()) as $contact) {
            $text = trim(sprintf("%s (%s)", $contact->getFormName(), $contact->getEmail()));

            /*
             * Do a fall-back to the email when the name is empty
             */
            if (strlen($text) === 0) {
                $text = $contact->getEmail();
            }

            $results[] = [
                'value'        => $contact->getId(),
                'text'         => $text,
                'name'         => $contact->getFormName(),
                'id'           => $contact->getId(),
                'email'        => $contact->getEmail(),
                'organisation' => is_null($contact->getContactOrganisation())
                    ?: $contact->getContactOrganisation()->getOrganisation()->getOrganisation()
            ];
        }

        return new JsonModel($results);
    }

    /**
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function exportCsvAction()
    {
        $selectionService = $this->getSelectionService()->setSelectionId($this->params('id'));

        if ($selectionService->isEmpty()) {
            return $this->notFoundAction();
        }

        // Open the output stream
        $fh = fopen('php://output', 'w');

        ob_start();

        fputcsv($fh, [
            'Email',
            'Firstname',
            'Lastname',
            'Organisation',
            'Country'
        ]);

        foreach ($this->getContactService()->findContactsInSelection($selectionService->getSelection(), true) as $contact) {
            fputcsv($fh, [
                $contact['email'],
                $contact['firstName'],
                trim(sprintf("%s %s", $contact['middleName'], $contact['lastName'])),
                $contact['organisation'],
                $contact['country']
            ]);
        }

        $string = ob_get_clean();

        //To be able to open the file correctly in Excel, we need to convert it to UTF-16LE
        $string = mb_convert_encoding($string, 'UTF-16LE', 'UTF8');

        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'text/csv');
        $headers->addHeaderLine(
            'Content-Disposition',
            "attachment; filename=\"selection_" . $selectionService->getSelection()->getSelection() . ".csv\""
        );
        $headers->addHeaderLine('Accept-Ranges', 'bytes');
        $headers->addHeaderLine('Content-Length', strlen($string));

        $response->setContent($string);

        return $response;
    }
}
