<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
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
class ContactManagerController extends ContactAbstractController
{
    /**
     * @return ViewModel
     */
    public function listAction()
    {
        $projectQuery = $this->getContactService()->findAllContacts();
        $page         = $this->getEvent()->getRouteMatch()->getParam('page');
        $paginator    = new Paginator(new PaginatorAdapter(new ORMPaginator($projectQuery)));
        $paginator->setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 15);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator->getDefaultItemCountPerPage()));
        $searchForm = new Search();

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'form'      => $searchForm
            )
        );
    }

    /**
     * @return ViewModel
     */
    public function viewAction()
    {
        $contactService = $this->getContactService()->setContactId($this->getEvent()->getRouteMatch()->getParam('id'));

        return new ViewModel(array('contactService' => $contactService));
    }

    /**
     * @return ViewModel
     */
    public function impersonateAction()
    {
        $contactService = $this->getContactService()->setContactId($this->getEvent()->getRouteMatch()->getParam('id'));
        $form           = $this->getServiceLocator()->get('contact_impersonate_form');
        $form->setData($_POST);
        $deeplink = false;
        if ($this->getRequest()->isPost() && $form->isValid()) {
            $data = $form->getData();
            //Create a target
            $target = $this->getDeeplinkService()->findEntityById('target', $data['target']);
            $key    = (!empty($data['key']) ? $data['key'] : null);
            //Create a deeplink for the user which redirects to the profile-page
            $deeplink = $this->getDeeplinkService()->createDeeplink($target, $contactService->getContact(), null, $key);
        }

        return new ViewModel(
            array(
                'deeplink'       => $deeplink,
                'contactService' => $contactService,
                'form'           => $form
            )
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
        $form   = $this->getFormService()->prepare($this->params('entity'), null, $_POST);
        $form->setAttribute('class', 'form-horizontal');
        if ($this->getRequest()->isPost() && $form->isValid()) {
            $result = $this->getContactService()->newEntity($form->getData());
            $this->redirect()->toRoute(
                'zfcadmin/contact-manager/' . strtolower($this->params('entity')),
                array('id' => $result->getId())
            );
        }

        return new ViewModel(array('form' => $form, 'entity' => $entity, 'fullVersion' => true));
    }

    /**
     * Edit an entity by finding it and call the corresponding form
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        $entity = $this->getContactService()->findEntityById(
            $this->getEvent()->getRouteMatch()->getParam('entity'),
            $this->getEvent()->getRouteMatch()->getParam('id')
        );
        $form   = $this->getFormService()->prepare($entity->get('entity_name'), $entity, $_POST);
        $form->setAttribute('class', 'form-horizontal live-form');
        $form->setAttribute('id', 'contact-contact-' . $entity->getId());
        if ($this->getRequest()->isPost() && $form->isValid()) {
            $result = $this->getContactService()->updateEntity($form->getData());
            $this->redirect()->toRoute(
                'zfcadmin/contact/' . strtolower($entity->get('dashed_entity_name')),
                array('id' => $result->getId())
            );
        }

        return new ViewModel(array('form' => $form, 'entity' => $entity, 'fullVersion' => true));
    }

    /**
     * (soft-delete) an entity
     *
     * @return \Zend\Http\Response
     */
    public function deleteAction()
    {
        $entity = $this->getContactService()->findEntityById(
            $this->getEvent()->getRouteMatch()->getParam('entity'),
            $this->getEvent()->getRouteMatch()->getParam('id')
        );
        $this->getContactService()->removeEntity($entity);

        return $this->redirect()->toRoute(
            'zfcadmin/contact-manager/' . $entity->get('dashed_entity_name') . 's'
        );
    }
}
