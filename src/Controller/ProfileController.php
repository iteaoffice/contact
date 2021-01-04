<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\Controller;

use Contact\Entity\OptIn;
use Contact\Entity\PhoneType;
use Contact\Entity\Photo;
use Contact\Form\ProfileBody;
use Contact\Form\ProfileForm;
use Contact\Form\SendMessage;
use Contact\Service\ContactService;
use DateTime;
use Doctrine\ORM\EntityManager;
use Event\Service\MeetingService;
use General\Service\EmailService;
use General\Service\GeneralService;
use Laminas\Http\Response;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Validator\File\ImageSize;
use Laminas\Validator\File\MimeType;
use Laminas\View\Model\ViewModel;
use Organisation\Service\OrganisationService;
use Program\Options\ModuleOptions;
use Program\Service\CallService;

use function array_merge_recursive;
use function count;
use function sprintf;

/**
 * Class ProfileController
 *
 * @package Contact\Controller
 */
final class ProfileController extends ContactAbstractController
{
    private ContactService $contactService;
    private OrganisationService $organisationService;
    private CallService $callService;
    private ModuleOptions $programModuleOptions;
    private GeneralService $generalService;
    private MeetingService $meetingService;
    private EmailService $emailService;
    private EntityManager $entityManager;
    private TranslatorInterface $translator;

    public function __construct(
        ContactService $contactService,
        OrganisationService $organisationService,
        CallService $callService,
        ModuleOptions $programModuleOptions,
        GeneralService $generalService,
        MeetingService $meetingService,
        EmailService $emailService,
        EntityManager $entityManager,
        TranslatorInterface $translator
    ) {
        $this->contactService       = $contactService;
        $this->organisationService  = $organisationService;
        $this->callService          = $callService;
        $this->programModuleOptions = $programModuleOptions;
        $this->generalService       = $generalService;
        $this->meetingService       = $meetingService;
        $this->emailService         = $emailService;
        $this->entityManager        = $entityManager;
        $this->translator           = $translator;
    }


    public function viewAction()
    {
        if (! $this->identity()->isActivated()) {
            $this->flashMessenger()->addSuccessMessage(
                $this->translator->translate('txt-your-profile-has-not-been-activated-yet-active-your-pofile-first')
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
        if (! $this->identity()->isActivated()) {
            $this->flashMessenger()->addSuccessMessage(
                $this->translator->translate('txt-your-profile-has-not-been-activated-yet-active-your-pofile-first')
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
                $this->translator->translate('txt-your-opt-in-settings-been-updated-successfully')
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
        if (! $this->identity()->isActivated()) {
            $this->flashMessenger()->addSuccessMessage(
                $this->translator->translate('txt-your-profile-has-not-been-activated-yet-active-your-pofile-first')
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
        if (! $this->identity()->isActivated()) {
            $this->flashMessenger()->addSuccessMessage(
                $this->translator->translate('txt-your-profile-has-not-been-activated-yet-active-your-pofile-first')
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

        return new ViewModel(
            [
                'contactService' => $this->contactService,
                'contact'        => $contact,
            ]
        );
    }

    public function sendMessageAction()
    {
        $contact = $this->contactService->findContactByHash((string)$this->params('hash'));

        if (null === $contact) {
            return $this->notFoundAction();
        }


        $data = $this->getRequest()->getPost()->toArray();

        $form = new SendMessage();
        $form->setData($data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                if (! $contact->isVisibleInCommunity()) {
                    return $this->redirect()->toRoute('community/contact/search');
                }

                return $this->redirect()->toRoute('community/contact/profile/contact', ['hash' => $this->params('hash')]);
            }

            if ($form->isValid()) {
                $emailBuilder = $this->emailService->createNewWebInfoEmailBuilder('/contact/profile/send-message');

                $emailBuilder->addContactTo($contact);
                $emailBuilder->setReplyTo($this->identity()->getEmail());

                $variables = [
                    'message'        => $form->getData()['message'],
                    'recipient_name' => $contact->parseFullName(),
                    'sender_name'    => $this->identity()->parseFullName()
                ];

                $emailBuilder->setTemplateVariables($variables);

                $this->emailService->sendBuilder($emailBuilder);

                $this->flashMessenger()
                    ->addSuccessMessage(
                        sprintf(
                            $this->translator->translate('txt-message-to-%s-has-been-sent-successfully'),
                            $contact->parseFullName()
                        )
                    );

                if (! $contact->isVisibleInCommunity()) {
                    return $this->redirect()->toRoute('community/contact/search');
                }

                return $this->redirect()->toRoute('community/contact/profile/contact', ['hash' => $this->params('hash')]);
            }
        }

        return new ViewModel(
            [
                'contactService' => $this->contactService,
                'contact'        => $contact,
                'form'           => $form
            ]
        );
    }


    public function activateAction(): Response
    {
        $contact = $this->identity();

        if (null === $contact) {
            return $this->redirect()->toRoute('user/login');
        }

        if ($contact->isActivated()) {
            $this->flashMessenger()->addSuccessMessage(
                $this->translator->translate('txt-your-profile-has-already-been-activated-you-will-be-redirected')
            );

            return $this->redirect()->toRoute('community/contact/profile/view');
        }

        $contact->setDateActivated(new DateTime());
        $this->contactService->save($contact);

        $this->flashMessenger()->addSuccessMessage(
            $this->translator->translate('txt-your-account-has-been-activated-successfully')
        );

        return $this->redirect()->toRoute('community/contact/profile/view');
    }

    public function activateOptInAction(): Response
    {
        $contact = $this->identity();

        if (null === $contact) {
            return $this->redirect()->toRoute('user/login');
        }

        if ($contact->isActivated()) {
            $this->flashMessenger()->addSuccessMessage(
                $this->translator->translate('txt-your-profile-has-already-been-activated-you-will-be-redirected')
            );

            return $this->redirect()->toRoute('community/contact/profile/view');
        }

        $contact->setDateActivated(new DateTime());
        $this->contactService->save($contact);

        $this->flashMessenger()->addSuccessMessage(
            $this->translator->translate('txt-your-newsletter-subscription-has-has-been-activated-successfully')
        );

        return $this->redirect()->toRoute('community/contact/profile/view');
    }

    public function manageHlgAction()
    {
        $contact = $this->identity();
        $contact->setDateActivated(new DateTime());
        $this->contactService->save($contact);

        $form = new ProfileBody($this->entityManager);
        $form->getInputFilter()->get('password')->setRequired(false);
        $form->getInputFilter()->get('passwordVerify')->setRequired(false);

        $form->get('optIn')->setValue(
            $contact->getOptIn()->map(
                static function (OptIn $optIn) {
                    return $optIn->getId();
                }
            )->toArray()
        );

        $data = $this->getRequest()->getPost()->toArray();

        $form->setData($data);
        if ($this->getRequest()->isPost() && $form->isValid()) {
            $this->contactService->updateOptInForContact($contact, $data['optIn'] ?? []);

            $this->flashMessenger()->addSuccessMessage(
                $this->translator->translate('txt-newsletter-subscription-have-been-updated-successfully')
            );

            return $this->redirect()->toRoute('community/contact/profile/manage-hlg');
        }

        return new ViewModel(['form' => $form]);
    }

    public function manageExternalAction()
    {
        $contact = $this->identity();
        $contact->setDateActivated(new DateTime());
        $this->contactService->save($contact);

        $form = new ProfileBody($this->entityManager);
        $form->getInputFilter()->get('password')->setRequired(false);
        $form->getInputFilter()->get('passwordVerify')->setRequired(false);

        $form->get('optIn')->setValue(
            $contact->getOptIn()->map(
                static function (OptIn $optIn) {
                    return $optIn->getId();
                }
            )->toArray()
        );

        $data = $this->getRequest()->getPost()->toArray();

        $form->setData($data);
        if ($this->getRequest()->isPost() && $form->isValid()) {
            $this->contactService->updateOptInForContact($contact, $data['optIn'] ?? []);

            $this->flashMessenger()->addSuccessMessage(
                $this->translator->translate('txt-newsletter-subscription-have-been-updated-successfully')
            );

            return $this->redirect()->toRoute('community/contact/profile/manage-external');
        }

        return new ViewModel(['form' => $form]);
    }

    public function manageBodyAction()
    {
        $contact = $this->identity();
        $contact->setDateActivated(new DateTime());
        $this->contactService->save($contact);

        $form = new ProfileBody($this->entityManager);

        $form->get('optIn')->setValue(
            $contact->getOptIn()->map(
                static function (OptIn $optIn) {
                    return $optIn->getId();
                }
            )->toArray()
        );

        $data = $this->getRequest()->getPost()->toArray();

        $form->setData($data);
        if ($this->getRequest()->isPost() && $form->isValid()) {
            $this->contactService->updatePasswordForContact($data['password'], $contact);

            $this->flashMessenger()->addSuccessMessage(
                $this->translator->translate('txt-password-successfully-been-updated')
            );

            $this->contactService->updateOptInForContact($contact, $data['optIn'] ?? []);

            $this->flashMessenger()->addSuccessMessage(
                $this->translator->translate('txt-newsletter-subscription-have-been-updated-successfully')
            );


            return $this->redirect()->toRoute('community/contact/profile/view');
        }

        return new ViewModel(['form' => $form]);
    }

    public function createAction()
    {
        $contact       = $this->identity();
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

        $mailAddress         = $this->contactService->getMailAddress($contact);
        $contactOrganisation = false;
        if ($contact->hasOrganisation()) {
            $contactOrganisation = $contact->getContactOrganisation();
        }
        $profile = false;
        if ($contact->hasProfile()) {
            $profile = $contact->getProfile();
        }

        $data = array_merge(
            [
                'contact'              => [
                    'gender'     => $contact->getGender()->getId(),
                    'title'      => $contact->getTitle()->getId(),
                    'firstName'  => $contact->getFirstName(),
                    'middleName' => $contact->getMiddleName(),
                    'lastName'   => $contact->getLastName(),
                    'department' => $contact->getDepartment(),
                    'position'   => $contact->getPosition()
                ],
                'phone'                => [
                    PhoneType::PHONE_TYPE_DIRECT => $this->contactService->getDirectPhone($contact),
                    PhoneType::PHONE_TYPE_MOBILE => $this->contactService->getMobilePhone($contact),
                ],
                'address'              =>
                    null !== $mailAddress ? $mailAddress->toArray() : [],
                'contact_organisation' =>
                    ! $contactOrganisation ?: $contactOrganisation->toArray(),
                'profile'              =>
                    ! $profile ?: $profile->toArray(),
                'optIn'                => $contact->getOptIn()->map(
                    static function (OptIn $optIn) {
                        return $optIn->getId();
                    }
                )
            ],
            $data
        );

        $form = new ProfileForm($this->entityManager, $contact);
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
                $this->contactService->updateContact($this->identity(), $form->getData());

                $this->contactService->updateOptInForContact(
                    $this->identity(),
                    '' === $data['optIn'] ? [] : $data['optIn']
                );
                $this->contactService->updateContactOrganisation($this->identity(), $data['contact_organisation']);

                $this->flashMessenger()->addSuccessMessage(
                    $this->translator->translate('txt-profile-has-successfully-been-updated')
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
                'data'             => $data,
                'hasOrganisations' => count($organisations) > 1, ///We need to exclude the none of the above :)
            ]
        );
    }

    public function editAction()
    {
        $contact       = $this->identity();
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

        $mailAddress         = $this->contactService->getMailAddress($contact);
        $contactOrganisation = false;
        if ($contact->hasOrganisation()) {
            $contactOrganisation = $contact->getContactOrganisation();
        }
        $profile = false;
        if ($contact->hasProfile()) {
            $profile = $contact->getProfile();
        }

        $data = array_merge(
            [
                'contact'              => [
                    'gender'     => $contact->getGender()->getId(),
                    'title'      => $contact->getTitle()->getId(),
                    'firstName'  => $contact->getFirstName(),
                    'middleName' => $contact->getMiddleName(),
                    'lastName'   => $contact->getLastName(),
                    'department' => $contact->getDepartment(),
                    'position'   => $contact->getPosition()
                ],
                'phone'                => [
                    PhoneType::PHONE_TYPE_DIRECT => $this->contactService->getDirectPhone($contact),
                    PhoneType::PHONE_TYPE_MOBILE => $this->contactService->getMobilePhone($contact),
                ],
                'address'              =>
                    null !== $mailAddress ? $mailAddress->toArray() : [],
                'contact_organisation' =>
                    ! $contactOrganisation ?: $contactOrganisation->toArray(),
                'profile'              =>
                    ! $profile ?: $profile->toArray(),
                'optIn'                => $contact->getOptIn()->map(
                    static function (OptIn $optIn) {
                        return $optIn->getId();
                    }
                )
            ],
            $data
        );

        $form = new ProfileForm($this->entityManager, $contact);
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

                //Activate the account
                $contact->setDateActivated(new DateTime());

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
                $this->contactService->updateContact($this->identity(), $form->getData());

                $this->contactService->updateOptInForContact(
                    $this->identity(),
                    '' === $data['optIn'] ? [] : $data['optIn']
                );
                $this->contactService->updateContactOrganisation($this->identity(), $data['contact_organisation']);

                $this->flashMessenger()->addSuccessMessage(
                    $this->translator->translate('txt-your-account-has-been-registered-successfully')
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
                'data'             => $data,
                'hasOrganisations' => count($organisations) > 1, ///We need to exclude the none of the above :)
            ]
        );
    }
}
