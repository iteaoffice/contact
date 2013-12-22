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

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\File\ImageSize;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

use Contact\Service\ContactService;
use Contact\Service\FormServiceAwareInterface;
use Contact\Service\FormService;
use Contact\Entity\Photo;

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

            $file = stream_get_contents($contact->getPhoto()->first()->getPhoto());

            $response->getHeaders()
                ->addHeaderLine('Content-Type: ' .
                    $contact->getPhoto()->first()->getContentType()->getContentType())
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
     * Ajax controller to update the OptIn
     *
     * @return \Zend\View\Model\JsonModel
     */
    public function optInUpdateAction()
    {
        $optInId = (int)$this->getEvent()->getRequest()->getPost()->get('optInId');
        $enable  = (int)$this->getEvent()->getRequest()->getPost()->get('enable') === 1;

        $this->getContactService()->updateOptInForContact(
            $optInId,
            $enable,
            $this->zfcUserAuthentication()->getIdentity()
        );

        return new JsonModel(
            array(
                'result' => 'success',
                'enable' => ($enable ? 1 : 0)
            )
        );
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

        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );

        $form = $this->getFormService()->prepare($entity->get('entity_name'), $entity, $data);

        $form->setAttribute('class', 'form-horizontal');

        if ($this->getRequest()->isPost() && $form->isValid()) {

            $contact = $form->getData();

            $fileData = $this->params()->fromFiles();

            if (isset($fileData['contact']['photo'])) {
                foreach ($fileData['contact']['photo'] as $photoElement) {
                    if (!empty($photoElement['file']['name'])) {

                        $photo = $entity->getPhoto()->first();
                        if (!$photo) {
                            //Create a photo element
                            $photo = new Photo();
                            $photo->setContact($entity);
                        }


                        $photo->setPhoto(file_get_contents($photoElement['file']['tmp_name']));
                        $photo->setThumb(file_get_contents($photoElement['file']['tmp_name']));

                        $imageSizeValidator = new ImageSize();
                        $imageSizeValidator->isValid($photoElement['file']);

                        $photo->setWidth($imageSizeValidator->width);
                        $photo->setHeight($imageSizeValidator->height);

                        $photo->setContentType(
                            $this->getGeneralService()->findContentTypeByContentTypeName($photoElement['file']['type'])
                        );

                        $entity->addPhoto($photo);
                    }
                }
            }

            $contact = $this->getContactService()->updateEntity($contact);
            /**
             * The contact_organisation is different and not a drop-down.
             * we will extract the organisation name from the contact_organisation text-field
             */
            $formData = $this->params()->fromPost('contact');
            $this->getContactService()->updateContactOrganisation($contact, $formData['contact_organisation']);
            $this->flashMessenger()->setNamespace('success')->addMessage(
                _("txt-profile-has-successfully-been-updated")
            );
            $this->redirect()->toRoute('contact/profile');
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

        $form->setData($_POST);


        if ($this->getRequest()->isPost() && $form->isValid()) {
            $formData = $form->getData();

            if ($this->getContactService()->updatePasswordForContact(
                $formData['password'],
                $this->zfcUserAuthentication()->getIdentity()
            )
            ) {
                $this->flashMessenger()->setNamespace('success')->addMessage(
                    _("txt-password-successfully-been-updated")
                );
                $this->redirect()->toRoute('contact/profile');
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
