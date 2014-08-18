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

use Contact\Entity\Photo;
use Contact\Form\Profile;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\Mvc\Controller\Plugin\FlashMessenger;
use Zend\Validator\File\ImageSize;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use ZfcUser\Controller\Plugin\ZfcUserAuthentication;

/**
 * @category    Contact
 * @package     Controller
 * @method      ZfcUserAuthentication zfcUserAuthentication()
 * @method      FlashMessenger flashMessenger()
 * @method      bool isAllowed($resource, $action)
 */
class ContactController extends ContactAbstractController
{
    /**
     * @return ViewModel
     */
    public function signatureAction()
    {
        $contactService = $this->getContactService()->setContact(
            $this->zfcUserAuthentication()->getIdentity()
        );

        return new ViewModel(['contactService' => $contactService]);
    }

    /**
     * Show the details of 1 project
     *
     * @return \Zend\Stdlib\ResponseInterface|null
     */
    public function photoAction()
    {
        /**
         * @var $photo Photo
         */
        $photo = $this->getContactService()->findEntityById(
            'photo',
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        /**
         * Do a check if the given has is correct to avoid guessing the image
         */
        if (is_null($photo) || $this->getEvent()->getRouteMatch()->getParam('hash') !== $photo->getHash()) {
            return $this->notFoundAction();
        }

        $file = stream_get_contents($photo->getPhoto());

        /**
         * Check if the file is cached and if not, create it
         */
        if (!file_exists($photo->getCacheFileName())) {
            /**
             * The file exists, but is it not updated?
             */
            file_put_contents($photo->getCacheFileName(), $file);

        }

        $response = $this->getResponse();
        $response->getHeaders()
            ->addHeaderLine('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 36000))
            ->addHeaderLine("Cache-Control: max-age=36000, must-revalidate")
            ->addHeaderLine("Pragma: public")
            ->addHeaderLine('Content-Type: ' . $photo->getContentType()->getContentType())
            ->addHeaderLine('Content-Length: ' . (string) strlen($file));
        $response->setContent($file);

        return $response;

    }

    /**
     * @return ViewModel
     */
    public function profileAction()
    {
        $contactService = $this->getContactService()->setContact(
            $this->zfcUserAuthentication()->getIdentity()
        );

        return new ViewModel(['contactService' => $contactService]);
    }

    /**
     * Ajax controller to update the OptIn
     *
     * @return \Zend\View\Model\JsonModel
     */
    public function optInUpdateAction()
    {
        $optInId = (int) $this->getEvent()->getRequest()->getPost()->get('optInId');
        $enable = (int) $this->getEvent()->getRequest()->getPost()->get('enable') === 1;
        $this->getContactService()->updateOptInForContact(
            $optInId,
            $enable,
            $this->zfcUserAuthentication()->getIdentity()
        );

        return new JsonModel(
            [
                'result' => 'success',
                'enable' => ($enable ? 1 : 0)
            ]
        );
    }

    /**
     * Edit the profile of the person
     * @return ViewModel
     */
    public function profileEditAction()
    {
        $contactService = $this->getContactService()->setContactId(
            $this->zfcUserAuthentication()->getIdentity()->getId()
        );
        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );
        $form = new Profile($this->getServiceLocator(), $contactService->getContact());
        $form->bind($contactService->getContact());
        $form->setData($data);
        if ($this->getRequest()->isPost() && $form->isValid()) {
            $contact = $form->getData();
            $fileData = $this->params()->fromFiles();
            if (!empty($fileData['file']['name'])) {
                $photo = $contactService->getContact()->getPhoto()->first();
                if (!$photo) {
                    //Create a photo element
                    $photo = new Photo();
                }
                $photo->setPhoto(file_get_contents($fileData['file']['tmp_name']));
                $photo->setThumb(file_get_contents($fileData['file']['tmp_name']));
                $imageSizeValidator = new ImageSize();
                $imageSizeValidator->isValid($fileData['file']);
                $photo->setWidth($imageSizeValidator->width);
                $photo->setHeight($imageSizeValidator->height);
                $photo->setContentType(
                    $this->getGeneralService()->findContentTypeByContentTypeName($fileData['file']['type'])
                );
                $collection = new ArrayCollection();
                $collection->add($photo);
                $contact->addPhoto($collection);
            }
            /**
             * Remove any unwanted photo's
             */
            foreach ($contact->getPhoto() as $photo) {
                if (is_null($photo->getWidth())) {
                    $collection = new ArrayCollection();
                    $collection->add($photo);
                    $contact->removePhoto($collection);
                }
            };
            $contact = $this->getContactService()->updateEntity($contact);
            /**
             * The contact_organisation is different and not a drop-down.
             * we will extract the organisation name from the contact_organisation text-field
             */
            $this->getContactService()->updateContactOrganisation($contact, $data['contact_organisation']);
            $this->flashMessenger()->setNamespace('success')->addMessage(
                _("txt-profile-has-successfully-been-updated")
            );

            return $this->redirect()->toRoute('contact/profile');
        }

        return new ViewModel(['form' => $form, 'contactService' => $contactService, 'fullVersion' => true]);
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

                return $this->redirect()->toRoute('contact/profile');
            }
        }

        return new ViewModel(['form' => $form]);
    }
}
