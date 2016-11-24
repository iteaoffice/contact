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

use Contact\Entity\Contact;
use Contact\Entity\OptIn;
use Contact\Entity\Photo;
use Contact\Form\Profile;
use Doctrine\Common\Collections\ArrayCollection;
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
        return new ViewModel(
            [
                'contactService' => $this->getContactService(),
                'contact'        => $this->zfcUserAuthentication()->getIdentity(),
                'optIns'         => $this->getContactService()->findAll(OptIn::class),
                'callService'    => $this->getCallService(),
                'hasIdentity'    => $this->zfcUserAuthentication()->hasIdentity(),
                'hasNda'         => $this->getProgramModuleOptions()->getHasNda(),
            ]
        );
    }

    /**
     * @return ViewModel
     */
    public function contactAction()
    {
        $contact = $this->getContactService()->findContactById($this->params('id'));

        if (is_null($contact) || $contact->parseHash() !== $this->params('hash')) {
            return $this->notFoundAction();
        }

        return new ViewModel(
            [
                'contactService' => $this->getContactService(),
                'contact'        => $contact,
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
        $contact = $this->zfcUserAuthentication()->getIdentity();
        ;

        //Find the amount of possible organisations
        $organisations = $this->getOrganisationService()->findOrganisationForProfileEditByContact($contact);

        $branches = [];
        if ($this->getContactService()->hasOrganisation($contact)) {
            $branches = $this->getOrganisationService()->findBranchesByOrganisation(
                $contact->getContactOrganisation()
                    ->getOrganisation()
            );
        }

        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );

        $form = new Profile($this->getEntityManager(), $this->getContactService(), $contact);
        $form->bind($contact);

        /**
         * When the organisation name is typed, we force the value to zero
         */

        $form->getInputFilter()->get('address')->remove('country');
        if (! isset($data['contact_organisation']['organisation_id'])) {
            $form->getInputFilter()->get('contact_organisation')->remove('organisation_id');
        }

        $form->setData($data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('community/contact/profile/view');
            }

            if ($form->isValid()) {
                /** @var Contact $contact */
                $contact  = $form->getData();
                $fileData = $this->params()->fromFiles();
                if (! empty($fileData['file']['name'])) {
                    /** @var Photo $photo */
                    $photo = $contact->getPhoto()->first();
                    if (! $photo) {
                        //Create a photo element
                        $photo = new Photo();
                    }
                    $photo->setPhoto(file_get_contents($fileData['file']['tmp_name']));
                    $photo->setThumb(file_get_contents($fileData['file']['tmp_name']));
                    $photo->setContact($this->zfcUserAuthentication()->getIdentity());
                    $imageSizeValidator = new ImageSize();
                    $imageSizeValidator->isValid($fileData['file']);
                    $photo->setWidth($imageSizeValidator->width);
                    $photo->setHeight($imageSizeValidator->height);
                    $photo->setContentType(
                        $this->getGeneralService()
                            ->findContentTypeByContentTypeName($fileData['file']['type'])
                    );
                    $this->getContactService()->updateEntity($photo);
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
                    ->addMessage($this->translate("txt-profile-has-successfully-been-updated"));

                return $this->redirect()->toRoute('community/contact/profile/view');
            } else {
                var_dump($form->getInputFilter()->getMessages());
            }
        }

        return new ViewModel(
            [
                'form'             => $form,
                'branches'         => $branches,
                'contactService'   => $this->getContactService(),
                'contact'          => $contact,
                'hasOrganisations' => sizeof($organisations) > 1, ///We need to exclude the none of the above :)
                'fullVersion'      => true,
            ]
        );
    }
}
