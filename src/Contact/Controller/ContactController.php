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
use Zend\ServiceManager\ServiceLocatorAwareInterface;

use Contact\Service\ContactService;
use Contact\Service\FormServiceAwareInterface;
use Contact\Service\FormService;
use Contact\Entity;

/**
 * @category    Contact
 * @package     Controller
 */
class LocationController extends AbstractActionController
    implements FormServiceAwareInterface, ServiceLocatorAwareInterface
{
    /**
     * @var ContactService
     */
    protected $contactService;
    /**
     * @var FormService
     */
    protected $formService;

    /**
     * Message container
     * @return array|void
     */
    public function indexAction()
    {
        _("txt-lot-status");
        _("txt-special-spc");
        _("txt-remark");
        _("txt-info");
        _("txt-personnel");
        _("txt-priority");
        _("txt-reservations");
        _("txt-txrf");
    }

    /**
     * Give a list of contacts
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function contactsAction()
    {
        $contacts = $this->getContactService()->findAll('contact');

        return new ViewModel(array('contacts' => $contacts));
    }

    /**
     * Show the details of 1 contact
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function contactAction()
    {
        $contact = $this->getContactService()->findEntityById(
            'contact',
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        return new ViewModel(array('contact' => $contact));
    }

    /**
     * Give a list of facilities
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function facilitiesAction()
    {
        $facilities = $this->getContactService()->findAll('facility');

        return new ViewModel(array('facilities' => $facilities));
    }

    /**
     * Show the details of 1 facility
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function facilityAction()
    {
        $facility = $this->getContactService()->findEntityById(
            'facility',
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        return new ViewModel(array('facility' => $facility));
    }

    /**
     * Give a list of areas
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function areasAction()
    {
        $areas = $this->getContactService()->findAll('area');

        return new ViewModel(array('areas' => $areas));
    }

    /**
     * Show the details of 1 area
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function areaAction()
    {
        $area = $this->getContactService()->findEntityById(
            'area',
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        return new ViewModel(array('area' => $area));
    }

    /**
     * Give a list of area2s
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function area2sAction()
    {
        $area2s = $this->getContactService()->findAll('area2');

        return new ViewModel(array('area2s' => $area2s));
    }

    /**
     * Show the details of 1 area
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function area2Action()
    {
        $area2 = $this->getContactService()->findEntityById(
            'area2',
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        return new ViewModel(array('area2' => $area2));
    }

    /**
     * Give a list of areas
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function subAreasAction()
    {
        $subAreas = $this->getContactService()->findAll('subArea');

        return new ViewModel(array('subAreas' => $subAreas));
    }

    /**
     * Show the details of 1 area
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function subAreaAction()
    {
        $subArea = $this->getContactService()->findEntityById(
            'subArea',
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        return new ViewModel(array('subArea' => $subArea));
    }

    /**
     * Give a list of operAreas
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function operAreasAction()
    {
        $operAreas = $this->getContactService()->findAll('operArea');

        return new ViewModel(array('operAreas' => $operAreas));
    }

    /**
     * Show the details of 1 operArea
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function operAreaAction()
    {
        $operArea = $this->getContactService()->findEntityById(
            'operArea',
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        return new ViewModel(array('operArea' => $operArea));
    }

    /**
     * Give a list of operAreas
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function operSubAreasAction()
    {
        $operSubAreas = $this->getContactService()->findAll('operSubArea');

        return new ViewModel(array('operSubAreas' => $operSubAreas));
    }

    /**
     * Show the details of 1 operArea
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function operSubAreaAction()
    {
        $operSubArea = $this->getContactService()->findEntityById(
            'operSubArea',
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        return new ViewModel(array('operSubArea' => $operSubArea));
    }

    /**
     * Edit an entity
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        $this->layout(false);
        $entity = $this->getContactService()->findEntityById(
            $this->getEvent()->getRouteMatch()->getParam('entity'),
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        $form = $this->getFormService()->prepare($entity->get('entity_name'), $entity, $_POST);
        $form->setAttribute('class', 'form-vertical live-form-edit');
        $form->setAttribute('id', 'contact-' . strtolower($entity->get('entity_name')) . '-' . $entity->getId());

        if ($this->getRequest()->isPost() && $form->isValid()) {
            $this->getContactService()->updateEntity($form->getData());

            $view = new ViewModel(array($this->getEvent()->getRouteMatch()->getParam('entity') => $form->getData()));
            $view->setTemplate(
                "contact/partial/" . $this->getEvent()->getRouteMatch()->getParam('entity') . '.twig'
            );

            return $view;
        }

        return new ViewModel(array('form' => $form, 'entity' => $entity));
    }

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
     * @return FormService
     */
    public function getFormService()
    {
        return $this->formService;
    }

    /**
     * @param $formService
     * @return ContactController
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
     * @return ContactController
     */
    public function setContactService($contactService)
    {
        $this->contactService = $contactService;

        return $this;
    }

}
