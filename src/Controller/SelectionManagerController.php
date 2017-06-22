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

use Contact\Entity\Selection;
use Contact\Form\AddContactToSelection;
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
        $contactQuery = $this->getContactService()->findEntitiesFiltered(Selection::class, $filterPlugin->getFilter());

        $paginator
            = new Paginator(new PaginatorAdapter(new ORMPaginator($contactQuery, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new SelectionFilter($this->getSelectionService());

        $form->setData(['filter' => $filterPlugin->getFilter()]);

        return new ViewModel(
            [
                'paginator'        => $paginator,
                'form'             => $form,
                'encodedFilter'    => urlencode($filterPlugin->getHash()),
                'order'            => $filterPlugin->getOrder(),
                'direction'        => $filterPlugin->getDirection(),
                'selectionService' => $this->getSelectionService()
            ]
        );
    }

    /**
     * @return array|ViewModel
     */
    public function viewAction()
    {
        $selection = $this->getSelectionService()->findSelectionById($this->params('id'));

        if (is_null($selection)) {
            return $this->notFoundAction();
        }

        /**
         * If the query is wrong an exception is thrown now
         */

        try {
            $contacts = $this->getContactService()->findContactsInSelectionAsArray($selection);

            $error = false;
        } catch (\Throwable $e) {
            $contacts = [];
            $error = $e->getMessage();
        }

        return new ViewModel(
            [
                'selectionService' => $this->getSelectionService(),
                'selection'        => $selection,
                'contacts'         => $contacts,
                'error'            => $error,
            ]
        );
    }

    /**
     * @return array|\Zend\Http\Response|ViewModel
     */
    public function editContactsAction()
    {
        $selection = $this->getSelectionService()->findSelectionById($this->params('id'));

        if (is_null($selection)) {
            return $this->notFoundAction();
        }

        $data = array_merge(
            [
                'type' => $this->getSelectionService()->isSql($selection) ? Selection::TYPE_SQL : Selection::TYPE_FIXED,
                'sql'  => $this->getSelectionService()->isSql($selection) ? $selection->getSql()->getQuery() : null,
            ],
            $this->getRequest()->getPost()->toArray()
        );


        $form = new SelectionContacts($this->getSelectionService());
        $form->setData($data);


        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/selection/view', ['id' => $selection->getId()]);
            }

            $this->getSelectionService()->updateSelectionContacts($selection, $data);

            /**
             * @var $selection Selection
             */

            return $this->redirect()->toRoute('zfcadmin/selection/view', ['id' => $selection->getId()]);
        }

        return new ViewModel(
            [
                'selectionService' => $this->getSelectionService(),
                'selection'        => $selection,
                'form'             => $form,
            ]
        );
    }

    /**
     * @return array|\Zend\Http\Response|ViewModel
     */
    public function addContactAction()
    {
        $contact = $this->getContactService()->findContactById($this->params('contactId'));

        if (is_null($contact)) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();


        $form = new AddContactToSelection($this->getSelectionService());
        $form->setData($data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/contact-admin/view', ['id' => $contact->getId()]);
            }

            //Find the selection
            $selection = $this->getSelectionService()->findSelectionById($data['selection']);

            $this->getSelectionService()->addContactToSelection($selection, $contact);

            $this->flashMessenger()->setNamespace('success')
                ->addMessage(sprintf(
                    $this->translate("txt-contact-%s-has-been-added-to-selection-%s-successfully"),
                    $contact->parseFullName(),
                    $selection->getSelection()
                ));

            /**
             * @var $selection Selection
             */

            return $this->redirect()->toRoute('zfcadmin/selection/view', ['id' => $selection->getId()]);
        }

        return new ViewModel(
            [
                'contact' => $contact,
                'form'    => $form,
            ]
        );
    }

    /*
     *
     */
    public function editAction()
    {
        $selection = $this->getSelectionService()->findSelectionById($this->params('id'));

        if (is_null($selection)) {
            return $this->notFoundAction();
        }

        $data = array_merge($this->getRequest()->getPost()->toArray());

        $form = $this->getFormService()->prepare($selection, $selection, $data);

        if (!is_null($selection->getContact())) {
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
                $this->getSelectionService()->removeEntity($selection);

                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(
                        sprintf(
                            $this->translate("txt-selection-%s-has-successfully-been-removed"),
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
                $this->getSelectionService()->updateEntity($selection);

                return $this->redirect()->toRoute('zfcadmin/selection/view', ['id' => $selection->getId()]);
            }
        }

        return new ViewModel(
            [
                'form'             => $form,
                'selectionService' => $this->getSelectionService(),
                'selection'        => $selection,
            ]
        );
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function newAction()
    {
        $selection = new Selection();

        $data = array_merge($this->getRequest()->getPost()->toArray());

        $form = $this->getFormService()->prepare($selection, $selection, $data);
        $form->remove('delete');

        $form->get('contact_entity_selection')->get('contact')->injectContact(
            $this->zfcUserAuthentication()
                ->getIdentity()
        );

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
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
                $this->getSelectionService()->updateEntity($selection);

                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(sprintf(
                        $this->translate("txt-selection-%s-has-been-created-successfully"),
                        $selection->getSelection()
                    ));

                return $this->redirect()->toRoute('zfcadmin/selection/view', ['id' => $selection->getId()]);
            }
        }

        return new ViewModel(['form' => $form]);
    }

    /**
     * @return JsonModel|ViewModel
     */
    public function getContactsAction()
    {
        $selection = $this->getSelectionService()->findSelectionById($this->params()->fromPost('id'));

        if (is_null($selection)) {
            return $this->notFoundAction();
        }

        $results = [];
        foreach ($this->getContactService()->findContactsInSelection($selection) as $contact) {
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
                    ?: $contact->getContactOrganisation()->getOrganisation()->getOrganisation(),
            ];
        }

        return new JsonModel($results);
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface|ViewModel
     */
    public function exportCsvAction()
    {
        $selection = $this->getSelectionService()->findSelectionById($this->params('id'));

        if (is_null($selection)) {
            return $this->notFoundAction();
        }

        // Open the output stream
        $fh = fopen('php://output', 'w');

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

        foreach ($this->getContactService()->findContactsInSelection($selection, true) as $contact) {
            fputcsv(
                $fh,
                [
                    $contact['email'],
                    $contact['firstName'],
                    trim(sprintf("%s %s", $contact['middleName'], $contact['lastName'])),
                    $contact['organisation'],
                    $contact['country'],
                ]
            );
        }

        $string = ob_get_clean();

        //To be able to open the file correctly in Excel, we need to convert it to UTF-16LE
        $string = mb_convert_encoding($string, 'UTF-16LE', 'UTF8');

        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'text/csv');
        $headers->addHeaderLine(
            'Content-Disposition',
            "attachment; filename=\"selection_" . $selection->getSelection() . ".csv\""
        );
        $headers->addHeaderLine('Accept-Ranges', 'bytes');
        $headers->addHeaderLine('Content-Length', strlen($string));

        $response->setContent($string);

        return $response;
    }
}
