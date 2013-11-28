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

use General\Service\GeneralService;

/**
 * @category    Contact
 * @package     Controller
 */
class ContactController extends AbstractActionController implements
    FormServiceAwareInterface,
    ServiceLocatorAwareInterface
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
    }

    /**
     * Show the details of 1 project
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function photoAction()
    {
        $response = $this->getResponse();

        $contact = $this->getContactService()->findContactByHash(
            $this->getEvent()->getRouteMatch()->getParam('contactHash')
        );

        $response->getHeaders()
            ->addHeaderLine('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 36000))
            ->addHeaderLine("Cache-Control: max-age=36000, must-revalidate")
            ->addHeaderLine("Pragma: public");

        if (!is_null($contact) && !is_null($contact->getPhoto())) {

            $file = stream_get_contents($contact->getPhoto()->getPhoto());

            $response->getHeaders()
                ->addHeaderLine('Content-Type: ' .
                    $contact->getPhoto()->getContentType()->getContentType())
                ->addHeaderLine('Content-Length: ' . (string)strlen($file));

            $response->setContent($file);

            return $response;
        } else {
            $response->getHeaders()
                ->addHeaderLine('Content-Type: image/jpg');
            $response->setStatusCode(404);
        }
    }

    /**
     * @return ViewModel
     */
    public function profileAction()
    {
        $contactService = $this->getContactService()->setContact(
            $this->zfcUserAuthentication()->getIdentity()
        );


        return new ViewModel(array('contactService' => $contactService));
    }

    /**
     * Edit the profile of the person
     * @return ViewModel
     */
    public function profileEditAction()
    {
        $entity = $this->getContactService()->findEntityById(
            'contact',
            $this->zfcUserAuthentication()->getIdentity()->getId()
        );

        $form = $this->getFormService()->prepare($entity->get('entity_name'), $entity, $_POST);

        $form->setAttribute('class', 'form-horizontal');

        if ($this->getRequest()->isPost() && $form->isValid()) {
            $contact = $this->getContactService()->updateEntity($form->getData());

            /**
             * The contact_organisation is different and not a drop-down.
             * we will extract the organisation name from the contact_organisation text-field
             */
            $formData = $this->params()->fromPost('contact');
            $this->getContactService()->updateContactOrganisation($contact, $formData['contact_organisation']);
        } else {
            /**
             * Pre fill some values
             */
            $contactService = $this->getContactService()->setContact($entity);

            /**
             * Populate the form fields, when known
             */
            if (!is_null($contactService->getContact()->getContactOrganisation())) {
                $form->get('contact')->get('contact_organisation')->get('organisation')->setValue(
                    $contactService->findOrganisationService()->parseOrganisationWithBranch(
                        $contactService->getContact()->getContactOrganisation()->getBranch()
                    )
                );

                $form->get('contact')->get('contact_organisation')->get('country')->setValue(
                    is_null($contactService->findOrganisationService()->getOrganisation()->getCountry()) ?
                        $this->getGeneralService()->findLocationByIPAddress() :
                        $contactService->findOrganisationService()->getOrganisation()->getCountry()->getId()
                );
            }
        }

        return new ViewModel(array('form' => $form, 'entity' => $entity, 'fullVersion' => true));
    }

    /**
     * Function to save the password of the user
     */
    public function changePasswordAction()
    {
        $form = $this->getServiceLocator()->get('contact_password_form');
        $form->setInputFilter($this->getServiceLocator()->get('contact_password_form_filter'));

        $form->setAttribute('class', 'form-horizontal');

        $hasAlreadyAPassword =
            !is_null($this->zfcUserAuthentication()->getIdentity()->getSaltedPassword()) ? true : false;

        $form->setData($_POST);


        if ($this->getRequest()->isPost() && $form->isValid()) {
            $formData = $form->getData();

            if ($this->getContactService()->updatePasswordForContact(
                $formData['password'],
                $this->zfcUserAuthentication()->getIdentity(),
                $hasAlreadyAPassword ? $formData['currentPassword'] : null
            )
            ) {
                $this->flashMessenger()->setNamespace('success')->addMessage(
                    _("txt-password-successfully-been-updated")
                );
                $this->redirect()->toRoute('contact/profile');
            } else {
                $form->get('currentPassword')->setMessages(array('The given current password is incorrect'));
            }
        }

        return new ViewModel(array('form' => $form));
    }


    /**
     * Gateway to the Contact Service
     *
     * @return ContactService
     */
    public function getContactService()
    {
        return $this->getServiceLocator()->get('contact_contact_service');
    }

    /**
     * @param $contactService
     *
     * @return ContactController
     */
    public function setContactService($contactService)
    {
        $this->contactService = $contactService;

        return $this;
    }

    /**
     * Gateway to the General Service
     *
     * @return GeneralService
     */
    public function getGeneralService()
    {
        return $this->getServiceLocator()->get('general_general_service');
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
     *
     * @return ContactManagerController
     */
    public function setFormService($formService)
    {
        $this->formService = $formService;

        return $this;
    }
}
