<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Selection
 * @package     Controller
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Contact\Controller;

use Contact\Form\Search;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Zend\Paginator\Paginator;
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
        $paginator = null;
        $searchForm = new Search();

        $search = $this->getRequest()->getQuery()->get('q');
        $page = $this->getRequest()->getQuery()->get('page');

        $searchForm->setData($_GET);

        if ($this->getRequest()->isGet() && $searchForm->isValid() && !empty($search)) {
            $selectionQuery = $this->getSelectionService()->searchSelection($search);
        } else {
            $selectionQuery = $this->getSelectionService()->searchSelection();
        }

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($selectionQuery)));
        $paginator->setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 15);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator->getDefaultItemCountPerPage()));

        return new ViewModel(
            [
                'paginator' => $paginator,
                'form'      => $searchForm
            ]
        );
    }

    /**
     * @return ViewModel
     */
    public function viewAction()
    {
        $selectionService = $this->getSelectionService()->setSelectionId(
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        return new ViewModel(
            [
                'selectionService' => $selectionService,
                'contacts'         => $this->getContactService()->findContactsInSelection(
                    $selectionService->getSelection()
                ),
            ]
        );
    }

    /**
     * Create a new entity
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function newAction()
    {
        $entity = $this->getEvent()->getRouteMatch()->getParam('entity');
        $form = $this->getFormService()->prepare($this->params('entity'), null, $_POST);
        $form->setAttribute('class', 'form-horizontal');
        if ($this->getRequest()->isPost() && $form->isValid()) {
            $result = $this->getSelectionService()->newEntity($form->getData());

            return $this->redirect()->toRoute(
                'zfcadmin/selection-manager/' . strtolower($this->params('entity')),
                ['id' => $result->getId()]
            );
        }

        return new ViewModel(['form' => $form, 'entity' => $entity, 'fullVersion' => true]);
    }

    /**
     * Edit an entity by finding it and call the corresponding form
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        $entity = $this->getSelectionService()->findEntityById(
            $this->getEvent()->getRouteMatch()->getParam('entity'),
            $this->getEvent()->getRouteMatch()->getParam('id')
        );
        $form = $this->getFormService()->prepare($entity->get('entity_name'), $entity, $_POST);
        $form->setAttribute('class', 'form-horizontal live-form');
        $form->setAttribute('id', 'selection-selection-' . $entity->getId());
        if ($this->getRequest()->isPost() && $form->isValid()) {
            $result = $this->getSelectionService()->updateEntity($form->getData());

            return $this->redirect()->toRoute(
                'zfcadmin/selection/' . strtolower($entity->get('dashed_entity_name')),
                ['id' => $result->getId()]
            );
        }

        return new ViewModel(['form' => $form, 'entity' => $entity, 'fullVersion' => true]);
    }

    /**
     * (soft-delete) an entity
     *
     * @return \Zend\Http\Response
     */
    public function deleteAction()
    {
        $entity = $this->getSelectionService()->findEntityById(
            $this->getEvent()->getRouteMatch()->getParam('entity'),
            $this->getEvent()->getRouteMatch()->getParam('id')
        );
        $this->getSelectionService()->removeEntity($entity);

        return $this->redirect()->toRoute(
            'zfcadmin/selection-manager/' . $entity->get('dashed_entity_name') . 's'
        );
    }
}
