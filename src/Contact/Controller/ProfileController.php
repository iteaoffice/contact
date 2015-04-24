<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */

namespace Contact\Controller;

use Contact\Entity\Photo;
use Contact\Form\Profile;
use Doctrine\Common\Collections\ArrayCollection;
use General\Service\EmailServiceAwareInterface;
use Search\Service\SearchServiceAwareInterface;
use Zend\Mvc\Controller\Plugin\FlashMessenger;
use Zend\Validator\File\ImageSize;
use Zend\View\Model\ViewModel;
use ZfcUser\Controller\Plugin\ZfcUserAuthentication;

/**
 * @category    Contact
 *
 * @method      ZfcUserAuthentication zfcUserAuthentication()
 * @method      FlashMessenger flashMessenger()
 * @method      bool isAllowed($resource, $action)
 */
class ProfileController extends ContactAbstractController implements
    EmailServiceAwareInterface,
    SearchServiceAwareInterface
{


    /**
     * @return ViewModel
     */
    public function viewAction()
    {
        $contactService = $this->getContactService()->setContact(
            $this->zfcUserAuthentication()->getIdentity()
        );

        return new ViewModel(
            [
                'contactService' => $contactService,
                'hasIdentity'    => $this->zfcUserAuthentication()->hasIdentity(),
                'hasNda'         => $this->getServiceLocator()->get(
                    'program_module_options'
                )->getHasNda(),
            ]
        );
    }

    /**
     * @return ViewModel
     */
    public function contactAction()
    {
        $contactService = $this->getContactService()->setContactId($this->params('id'));

        if ($contactService->isEmpty() || $contactService->getContact()->parseHash() !== $this->params('hash')) {
            return $this->notFoundAction();
        }

        return new ViewModel(
            [
                'contactService' => $contactService,
            ]
        );
    }

    /**
     * Edit the profile of the person.
     *
     * @return ViewModel
     */
    public function editAction()
    {
        $contactService = $this->getContactService()->setContactId(
            $this->zfcUserAuthentication()->getIdentity()->getId()
        );
        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );

        $form = new Profile(
            $this->getServiceLocator(),
            $contactService->getContact()
        );
        $form->bind($contactService->getContact());


        /**
         * When the organisation name is typed, we force the value to zero
         */

        if (!isset($data['contact_organisation']['organisation_id'])) {
            $form->getInputFilter()->get('contact_organisation')->remove('organisation_id');
        }

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
                $photo->setPhoto(
                    file_get_contents($fileData['file']['tmp_name'])
                );
                $photo->setThumb(
                    file_get_contents($fileData['file']['tmp_name'])
                );
                $imageSizeValidator = new ImageSize();
                $imageSizeValidator->isValid($fileData['file']);
                $photo->setWidth($imageSizeValidator->width);
                $photo->setHeight($imageSizeValidator->height);
                $photo->setContentType(
                    $this->getGeneralService()
                        ->findContentTypeByContentTypeName(
                            $fileData['file']['type']
                        )
                );
                $collection = new ArrayCollection();
                $collection->add($photo);
                $contact->addPhoto($collection);
            }
            /*
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
            $this->getContactService()->updateContactOrganisation(
                $contact,
                $data['contact_organisation']
            );
            $this->flashMessenger()->setNamespace('success')->addMessage(
                _("txt-profile-has-successfully-been-updated")
            );

            return $this->redirect()->toRoute('contact/profile');
        }

        return new ViewModel(
            [
                'form'           => $form,
                'contactService' => $contactService,
                'fullVersion'    => true,
            ]
        );
    }
}