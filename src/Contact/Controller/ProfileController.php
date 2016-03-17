<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Contact\Controller;

use Contact\Entity\Photo;
use Contact\Form\Profile;
use Doctrine\Common\Collections\ArrayCollection;
use Program\Options\ModuleOptions;
use Zend\Validator\File\ImageSize;
use Zend\View\Model\ViewModel;

/**
 * Class ProfileController
 *
 * @package Contact\Controller
 */
class ProfileController extends ContactAbstractController
{
    /**
     * @return ViewModel
     */
    public function viewAction()
    {
        $contactService = $this->getContactService()->setContact($this->zfcUserAuthentication()->getIdentity());

        return new ViewModel([
            'contactService' => $contactService,
            'hasIdentity'    => $this->zfcUserAuthentication()->hasIdentity(),
            'hasNda'         => $this->getPluginManager()->getServiceLocator()->get(ModuleOptions::class)->getHasNda(),
        ]);
    }

    /**
     * @return ViewModel
     */
    public function contactAction()
    {
        $contactService = $this->getContactService()->setContactId($this->params('id'));

        if ($contactService->isEmpty() || $contactService->getContact()->parseHash() !== $this->params('hash')) {
            //            return $this->notFoundAction();
        }

        return new ViewModel([
            'contactService' => $contactService,
        ]);
    }

    /**
     * Edit the profile of the person.
     *
     * @return ViewModel
     */
    public function editAction()
    {
        $contactService = $this->getContactService()->setContactId($this->zfcUserAuthentication()->getIdentity()
            ->getId());

        //Find the amount of possible organisations
        $organisations = $this->getOrganisationService()
            ->findOrganisationForProfileEditByContact($contactService->getContact());

        $branches = [];
        if ($contactService->hasOrganisation()) {
            $branches = $this->getOrganisationService()->findBranchesByOrganisation($contactService->getContact()
                ->getContactOrganisation()->getOrganisation());
        }

        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );

        $form = new Profile($this->getEntityManager(), $this->getContactService(), $contactService->getContact());
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
                $photo->setPhoto(file_get_contents($fileData['file']['tmp_name']));
                $photo->setThumb(file_get_contents($fileData['file']['tmp_name']));
                $imageSizeValidator = new ImageSize();
                $imageSizeValidator->isValid($fileData['file']);
                $photo->setWidth($imageSizeValidator->width);
                $photo->setHeight($imageSizeValidator->height);
                $photo->setContentType($this->getGeneralService()
                    ->findContentTypeByContentTypeName($fileData['file']['type']));
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
            $this->getContactService()->updateContactOrganisation($contact, $data['contact_organisation']);
            $this->flashMessenger()->setNamespace('success')
                ->addMessage(_("txt-profile-has-successfully-been-updated"));

            return $this->redirect()->toRoute('community/contact/profile/view');
        }

        return new ViewModel([
            'form'             => $form,
            'branches'         => $branches,
            'contactService'   => $contactService,
            'hasOrganisations' => sizeof($organisations) > 1, ///We need to exclude the none of the above :)
            'fullVersion'      => true,
        ]);
    }
}
