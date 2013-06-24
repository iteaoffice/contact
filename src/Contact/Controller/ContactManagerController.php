<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Contact
 * @package     Controller
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
namespace Contact\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

use Contact\Service\FormServiceAwareInterface;
use Contact\Service\ContactService;
use Contact\Service\FormService;

/**
 *
 */
class LocationManagerController
    extends AbstractActionController
    implements FormServiceAwareInterface, ServiceLocatorAwareInterface
{

    /**
     * @var ContactService;
     */
    protected $contactService;
    /**
     * @var FormService
     */
    protected $formService;
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Trigger to switch layout
     *
     * @param $layout
     */
    public function layout($layout)
    {
        if (false === $layout) {
            $this->getEvent()->getViewModel()->setTemplate('layout/nolayout');
        } else {
            $this->getEvent()->getViewModel()->setTemplate('layout/' . $layout);
        }
    }

    /**
     * Give a list of messages
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function messagesAction()
    {
        $messages = $this->getContactService()->findAll('message');

        return new ViewModel(array('messages' => $messages));
    }

    /**
     * Show the details of 1 message
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function messageAction()
    {
        $message = $this->getContactService()->findEntityById(
            'message',
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        return new ViewModel(array('message' => $message));
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
            $this->redirect()->toRoute('zfcadmin/contact-manager/' .
                strtolower($this->params('entity')), array('id' => $result->getId()));
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

        $form = $this->getFormService()->prepare($entity->get('entity_name'), $entity, $_POST);
        $form->setAttribute('class', 'form-horizontal live-form');
        $form->setAttribute('id', 'contact-contact-' . $entity->getId());

        if ($this->getRequest()->isPost() && $form->isValid()) {
            $result = $this->getContactService()->updateEntity($form->getData());
            $this->redirect()->toRoute('zfcadmin/contact/' .
                strtolower($entity->get('dashed_entity_name')), array('id' => $result->getId()));
        }

        return new ViewModel(array('form' => $form, 'entity' => $entity, 'fullVersion' => true));
    }

    /**
     * (soft-delete) an entity
     *
     * @return \Zend\View\Model\ViewModel
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

    /**
     * @return \Contact\Service\FormService
     */
    public function getFormService()
    {
        return $this->formService;
    }

    /**
     * @param $formService
     * @return ContactManagerController
     */
    public function setFormService($formService)
    {
        $this->formService = $formService;

        return $this;
    }

    /**
     * Gateway to the Contact Service
     *
     * @return ContactService
     */
    public function getContactService()
    {
        return $this->getServiceLocator()->get('contact_generic_service');
    }

    /**
     * @param $contactService
     * @return ContactManagerController
     */
    public function setContactService($contactService)
    {
        $this->contactService = $contactService;

        return $this;
    }

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ContactManagerController|void
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

}
