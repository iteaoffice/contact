<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Controller;

use Contact\Entity\Contact;
use Contact\Entity\Photo;
use Contact\Form\Profile;
use Contact\Service\ContactService;
use Doctrine\ORM\EntityManager;
use Event\Service\MeetingService;
use General\Service\GeneralService;
use Organisation\Service\OrganisationService;
use Program\Options\ModuleOptions;
use Program\Service\CallService;
use Zend\Http\Response;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Validator\File\ImageSize;
use Zend\Validator\File\MimeType;
use Zend\View\Model\ViewModel;

/**
 * Class ProfileController
 *
 * @package Contact\Controller
 */
final class ProfileController extends ContactAbstractController
{
    /**
     * @var ContactService
     */
    private $contactService;
    /**
     * @var OrganisationService
     */
    private $organisationService;
    /**
     * @var CallService
     */
    private $callService;
    /**
     * @var ModuleOptions
     */
    private $programModuleOptions;
    /**
     * @var GeneralService
     */
    private $generalService;
    /**
     * @var MeetingService
     */
    private $meetingService;
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        ContactService $contactService,
        OrganisationService $organisationService,
        CallService $callService,
        ModuleOptions $programModuleOptions,
        GeneralService $generalService,
        MeetingService $meetingService,
        EntityManager $entityManager,
        TranslatorInterface $translator
    ) {
        $this->contactService = $contactService;
        $this->organisationService = $organisationService;
        $this->callService = $callService;
        $this->programModuleOptions = $programModuleOptions;
        $this->generalService = $generalService;
        $this->meetingService = $meetingService;
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }


    public function viewAction()
    {
        if (!$this->identity()->isActivated()) {
            $this->flashMessenger()->addSuccessMessage(
                $this->translator->translate("txt-your-profile-has-not-been-activated-yet-active-your-pofile-first")
            );

            return $this->redirect()->toRoute('community/contact/profile/activate');
        }

        return new ViewModel(
            [
                'contactService' => $this->contactService,
                'contact'        => $this->identity(),
                'optIns'         => $this->contactService->findActiveOptIns(),
                'callService'    => $this->callService,
                'hasIdentity'    => null !== $this->identity(),
                'hasNda'         => $this->programModuleOptions->getHasNda(),
            ]
        );
    }

    public function privacyAction()
    {
        if (!$this->identity()->isActivated()) {
            $this->flashMessenger()->addSuccessMessage(
                $this->translator->translate("txt-your-profile-has-not-been-activated-yet-active-your-pofile-first")
            );

            return $this->redirect()->toRoute(
                'community/contact/profile/activate',
                ['hash' => $this->identity()->getHash()]
            );
        }

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost()->toArray();

            $this->contactService->updateOptInForContact($this->identity(), $data['optIn'] ?? []);

            $changelogMessage = sprintf(
                $this->translator->translate("txt-your-opt-in-settings-been-updated-successfully")
            );

            $this->flashMessenger()->addSuccessMessage($changelogMessage);
            $this->contactService->addNoteToContact($changelogMessage, 'profile', $this->identity());

            return $this->redirect()->toRoute('community/contact/profile/privacy');
        }

        return new ViewModel(
            [
                'contactService' => $this->contactService,
                'contact'        => $this->identity(),
                'optIns'         => $this->contactService->findActiveOptIns(),
            ]
        );
    }

    public function organisationAction()
    {
        if (!$this->identity()->isActivated()) {
            $this->flashMessenger()->addSuccessMessage(
                $this->translator->translate("txt-your-profile-has-not-been-activated-yet-active-your-pofile-first")
            );

            return $this->redirect()->toRoute(
                'community/contact/profile/activate',
                ['hash' => $this->identity()->getHash()]
            );
        }

        return new ViewModel(
            [
                'contactService' => $this->contactService,
                'contact'        => $this->identity(),
            ]
        );
    }

    public function eventsAction()
    {
        if (!$this->identity()->isActivated()) {
            $this->flashMessenger()->addSuccessMessage(
                $this->translator->translate("txt-your-profile-has-not-been-activated-yet-active-your-pofile-first")
            );

            return $this->redirect()->toRoute(
                'community/contact/profile/activate',
                ['hash' => $this->identity()->getHash()]
            );
        }

        return new ViewModel(
            [
                'contactService' => $this->contactService,
                'contact'        => $this->identity(),
                'callService'    => $this->callService,
                'meetingService' => $this->meetingService,
                'hasIdentity'    => null !== $this->identity(),
                'hasNda'         => $this->programModuleOptions->getHasNda(),
            ]
        );
    }

    public function contactAction(): ViewModel
    {
        $contact = $this->contactService->findContactByHash((string)$this->params('hash'));

        if (null === $contact) {
            return $this->notFoundAction();
        }

        if (!$contact->isVisibleInCommunity()) {
            return $this->notFoundAction();
        }

        return new ViewModel(
            [
                'contactService' => $this->contactService,
                'contact'        => $contact,
            ]
        );
    }

    public function createAction()
    {
        $contact = $this->identity();

        $organisations = $this->organisationService->findOrganisationForProfileEditByContact($contact);

        $branches = [];
        if ($this->contactService->hasOrganisation($contact)) {
            $branches = $this->organisationService->findBranchesByOrganisation(
                $contact->getContactOrganisation()
                    ->getOrganisation()
            );
        }

        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );

        $form = new Profile($this->entityManager, $this->contactService, $contact);
        $form->bind($contact);

        //When we have a valid organisation_id, we do not need the country
        if (isset($data['contact_organisation']['organisation_id'])
            && $data['contact_organisation']['organisation_id'] !== '0'
        ) {
            $form->getInputFilter()->get('contact_organisation')->remove('country');
        } else {
            $form->getInputFilter()->get('contact_organisation')->remove('organisation_id');
        }

        $form->setData($data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                $this->flashMessenger()->addInfoMessage(
                    $this->translator->translate("txt-your-account-registration-has-been-cancelled")
                );
                // clear adapters
                $this->zfcUserAuthentication()->getAuthAdapter()->resetAdapters();
                $this->zfcUserAuthentication()->getAuthService()->clearIdentity();


                return $this->redirect()->toRoute('home');
            }

            if ($form->isValid()) {
                $contact = $form->getData();

                if (isset($data['removeFile'])) {
                    foreach ($contact->getPhoto() as $photo) {
                        $this->contactService->delete($photo);
                    }
                }


                /** @var Contact $contact */
                $contact = $form->getData();
                $fileData = $this->params()->fromFiles();
                if (!empty($fileData['file']['name'])) {
                    /** @var Photo $photo */
                    $photo = $contact->getPhoto()->first();
                    if (!$photo) {
                        //Create a photo element
                        $photo = new Photo();
                    }
                    $photo->setPhoto(file_get_contents($fileData['file']['tmp_name']));
                    $photo->setThumb(file_get_contents($fileData['file']['tmp_name']));
                    $photo->setContact($this->identity());
                    $imageSizeValidator = new ImageSize();
                    $imageSizeValidator->isValid($fileData['file']);
                    $photo->setWidth($imageSizeValidator->width);
                    $photo->setHeight($imageSizeValidator->height);

                    $fileTypeValidator = new MimeType();
                    $fileTypeValidator->isValid($fileData['file']);
                    $photo->setContentType(
                        $this->generalService->findContentTypeByContentTypeName($fileTypeValidator->type)
                    );

                    $this->contactService->save($photo);
                }

                //Activate the account
                $contact->setDateActivated(new \DateTime());

                $contact = $this->contactService->save($contact);

                $this->contactService->updateOptInForContact($this->identity(), $data['optIn'] ?? []);

                /**
                 * The contact_organisation is different and not a drop-down.
                 * we will extract the organisation name from the contact_organisation text-field
                 */
                $this->contactService->updateContactOrganisation($contact, $data['contact_organisation']);
                $this->flashMessenger()->addSuccessMessage(
                    $this->translator->translate("txt-your-account-has-been-registered-successfully")
                );

                return $this->redirect()->toRoute('community/contact/profile/view');
            }
        }

        return new ViewModel(
            [
                'form'             => $form,
                'branches'         => $branches,
                'contactService'   => $this->contactService,
                'contact'          => $contact,
                'hasOrganisations' => \count($organisations) > 1, ///We need to exclude the none of the above :)
                'fullVersion'      => true,
            ]
        );
    }

    public function activateAction(): Response
    {
        $contact = $this->identity();


        if (null === $contact) {
            return $this->redirect()->toRoute('zfcuser/login');
        }

        if ($contact->isActivated()) {
            $this->flashMessenger()->addSuccessMessage(
                $this->translator->translate("txt-your-profile-has-already-been-activated-you-will-be-redirected")
            );

            return $this->redirect()->toRoute('community/contact/profile/view');
        }

        $contact->setDateActivated(new \DateTime());
        $this->contactService->save($contact);

        $this->flashMessenger()->addSuccessMessage(
            $this->translator->translate("txt-your-account-has-been-activated-successfully")
        );

        return $this->redirect()->toRoute('community/contact/profile/view');
    }

    public function editAction()
    {
        $contact = $this->identity();

        $organisations = $this->organisationService->findOrganisationForProfileEditByContact($contact);

        $branches = [];
        if ($this->contactService->hasOrganisation($contact)) {
            $branches = $this->organisationService->findBranchesByOrganisation(
                $contact->getContactOrganisation()
                    ->getOrganisation()
            );
        }

        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );

        $form = new Profile($this->entityManager, $this->contactService, $contact);
        $form->bind($contact);

        //When we have a valid organisation_id, we do not need the country
        if (isset($data['contact_organisation']['organisation_id'])
            && $data['contact_organisation']['organisation_id'] !== '0'
        ) {
            $form->getInputFilter()->get('contact_organisation')->remove('country');
        } else {
            $form->getInputFilter()->get('contact_organisation')->remove('organisation_id');
        }

        $form->setData($data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('community/contact/profile/view');
            }

            if ($form->isValid()) {
                if (isset($data['removeFile'])) {
                    foreach ($contact->getPhoto() as $photo) {
                        $this->contactService->delete($photo);
                    }
                }


                /** @var Contact $contact */
                $contact = $form->getData();

                $fileData = $this->params()->fromFiles();
                if (!empty($fileData['file']['name'])) {
                    /** @var Photo $photo */
                    $photo = $contact->getPhoto()->first();
                    if (!$photo) {
                        //Create a photo element
                        $photo = new Photo();
                    }
                    $photo->setPhoto(file_get_contents($fileData['file']['tmp_name']));
                    $photo->setThumb(file_get_contents($fileData['file']['tmp_name']));
                    $photo->setContact($this->identity());
                    $imageSizeValidator = new ImageSize();
                    $imageSizeValidator->isValid($fileData['file']);
                    $photo->setWidth($imageSizeValidator->width);
                    $photo->setHeight($imageSizeValidator->height);

                    $fileTypeValidator = new MimeType();
                    $fileTypeValidator->isValid($fileData['file']);
                    $photo->setContentType(
                        $this->generalService->findContentTypeByContentTypeName($fileTypeValidator->type)
                    );

                    $this->contactService->save($photo);
                }

                $contact = $this->contactService->save($contact);

                $this->contactService->updateOptInForContact($this->identity(), $data['optIn'] ?? []);

                /**
                 * The contact_organisation is different and not a drop-down.
                 * we will extract the organisation name from the contact_organisation text-field
                 */
                $this->contactService->updateContactOrganisation($contact, $data['contact_organisation']);
                $this->flashMessenger()->addSuccessMessage(
                    $this->translator->translate("txt-profile-has-successfully-been-updated")
                );

                return $this->redirect()->toRoute('community/contact/profile/view');
            }
        }

        return new ViewModel(
            [
                'form'             => $form,
                'branches'         => $branches,
                'contactService'   => $this->contactService,
                'contact'          => $contact,
                'hasOrganisations' => \count($organisations) > 1, ///We need to exclude the none of the above :)
                'fullVersion'      => true,
            ]
        );
    }
}
