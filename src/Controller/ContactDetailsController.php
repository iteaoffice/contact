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

use Admin\Service\AdminService;
use Calendar\Entity\Contact;
use Calendar\Service\CalendarService;
use Contact\Entity\OptIn;
use Contact\Entity\Profile;
use Contact\Form\ContactMerge;
use Contact\Service\ContactService;
use Contact\Service\SelectionService;
use DateTime;
use Doctrine\ORM\EntityManager;
use Event\Service\BoothService;
use Event\Service\RegistrationService;
use Laminas\Http\Request;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\View\Model\ViewModel;
use Program\Service\CallService;
use Project\Service\ProjectService;

use function sprintf;

/**
 * Class ContactController
 *
 * @package Contact\Controller
 */
final class ContactDetailsController extends ContactAbstractController
{
    private ContactService $contactService;
    private SelectionService $selectionService;
    private CallService $callService;
    private ProjectService $projectService;
    private CalendarService $calendarService;
    private AdminService $adminService;
    private RegistrationService $registrationService;
    private BoothService $boothService;
    private TranslatorInterface $translator;
    private EntityManager $entityManager;

    public function __construct(
        ContactService $contactService,
        SelectionService $selectionService,
        CallService $callService,
        ProjectService $projectService,
        CalendarService $calendarService,
        AdminService $adminService,
        RegistrationService $registrationService,
        BoothService $boothService,
        TranslatorInterface $translator,
        EntityManager $entityManager
    ) {
        $this->contactService      = $contactService;
        $this->selectionService    = $selectionService;
        $this->callService         = $callService;
        $this->projectService      = $projectService;
        $this->calendarService     = $calendarService;
        $this->adminService        = $adminService;
        $this->registrationService = $registrationService;
        $this->boothService        = $boothService;
        $this->translator          = $translator;
        $this->entityManager       = $entityManager;
    }

    public function generalAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $contact = $this->contactService->findContactById((int)$this->params('id'));

        if ($contact === null) {
            return $this->notFoundAction();
        }

        //Refresh the contact to avoid that a proxy is loaded
        $this->contactService->refresh($contact);

        $selections = $this->selectionService->findSelectionsByContact($contact);
        $optIn      = $this->contactService->findAll(OptIn::class);

        $data = $request->getPost()->toArray();


        if ($request->isPost()) {
            if (isset($data['activate'])) {
                $contact->setDateActivated(new DateTime());

                $this->contactService->save($contact);

                $changelogMessage = sprintf(
                    $this->translator->translate('txt-contact-%s-has-been-activated-successfully'),
                    $contact->getDisplayName()
                );

                $this->flashMessenger()->addSuccessMessage($changelogMessage);

                $this->contactService->addNoteToContact($changelogMessage, 'office', $contact);
            }

            if (isset($data['deactivate'])) {
                $contact->setDateActivated(null);

                $this->contactService->save($contact);

                $changelogMessage = sprintf(
                    $this->translator->translate('txt-contact-%s-has-been-de-activated-successfully'),
                    $contact->getDisplayName()
                );

                $this->flashMessenger()->addSuccessMessage($changelogMessage);

                $this->contactService->addNoteToContact($changelogMessage, 'office', $contact);
            }

            if (isset($data['anonymise'])) {
                $contact->setDateAnonymous(new DateTime());
                $contact->setFirstName(null);
                $contact->setMiddleName(null);
                $contact->setLastName(null);
                $contact->setEmail(null);
                $contact->setEmailAddress(null);
                $contact->setPhoto(null);
                $contact->setPosition(null);
                $contact->setDepartment(null);
                $contact->setOptIn(null);
                foreach ($contact->getNda() as $nda) {
                    $this->callService->delete($nda);
                }
                if ($contact->getProfile()) {
                    $contact->getProfile()->setVisible(Profile::VISIBLE_HIDDEN);
                }

                $this->contactService->save($contact);

                $changelogMessage = sprintf(
                    $this->translator->translate('txt-contact-%s-has-been-anonymised-successfully'),
                    $contact->getDisplayName()
                );

                $this->flashMessenger()->addSuccessMessage($changelogMessage);

                $this->contactService->addNoteToContact($changelogMessage, 'office', $contact);
            }

            if (isset($data['deanonymise'])) {
                $contact->setDateAnonymous(null);

                $this->contactService->save($contact);

                $changelogMessage = sprintf(
                    $this->translator->translate('txt-contact-%s-has-been-de-anonymised-successfully'),
                    $contact->getDisplayName()
                );

                $this->flashMessenger()->addSuccessMessage($changelogMessage);

                $this->contactService->addNoteToContact($changelogMessage, 'office', $contact);
            }

            if (isset($data['flushpermissions'])) {
                $this->adminService->flushPermitsByContact($contact);
                $changelogMessage = sprintf(
                    $this->translator->translate('txt-permissions-of-contact-%s-has-been-de-flushed-successfully'),
                    $contact->getDisplayName()
                );

                $this->flashMessenger()->addSuccessMessage($changelogMessage);

                $this->contactService->addNoteToContact($changelogMessage, 'office', $contact);
            }

            return $this->redirect()->toRoute('zfcadmin/contact/view/general', ['id' => $contact->getId()]);
        }

        return new ViewModel(
            [
                'tab'            => 'general',
                'contact'        => $contact,
                'contactService' => $this->contactService,
            ]
        );
    }

    public function addressAction(): ViewModel
    {
        $contact = $this->contactService->findContactById((int)$this->params('id'));

        if ($contact === null) {
            return $this->notFoundAction();
        }

        return new ViewModel(
            [
                'tab'            => 'address',
                'contact'        => $contact,
                'contactService' => $this->contactService,
            ]
        );
    }

    public function notesAction(): ViewModel
    {
        $contact = $this->contactService->findContactById((int)$this->params('id'));

        if ($contact === null) {
            return $this->notFoundAction();
        }

        return new ViewModel(
            [
                'tab'            => 'notes',
                'contact'        => $contact,
                'contactService' => $this->contactService,
            ]
        );
    }

    public function phoneAction(): ViewModel
    {
        $contact = $this->contactService->findContactById((int)$this->params('id'));

        if ($contact === null) {
            return $this->notFoundAction();
        }

        return new ViewModel(
            [
                'tab'            => 'phone',
                'contact'        => $contact,
                'contactService' => $this->contactService,
            ]
        );
    }

    public function selectionAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $contact = $this->contactService->findContactById((int)$this->params('id'));

        if ($contact === null) {
            return $this->notFoundAction();
        }

        //Refresh the contact to avoid that a proxy is loaded
        $this->contactService->refresh($contact);

        $data = $request->getPost()->toArray();
        if ($request->isPost()) {
            foreach ((array)($data['selection'] ?? []) as $selectionId) {
                $selection = $this->selectionService->findSelectionById((int)$selectionId);
                if (null === $selection) {
                    continue;
                }
                foreach ($selection->getSelectionContact() as $selectionContact) {
                    if ($selectionContact->getContact() === $contact) {
                        $this->selectionService->delete($selectionContact);

                        $this->flashMessenger()->addSuccessMessage(
                            sprintf(
                                $this->translator->translate(
                                    'txt-contact-%s-has-removed-form-selection-%s-successfully'
                                ),
                                $contact->getDisplayName(),
                                $selection->getSelection()
                            )
                        );
                    }
                }
            }

            return $this->redirect()->toRoute('zfcadmin/contact/view/selection', ['id' => $contact->getId()]);
        }

        return new ViewModel(
            [
                'tab'              => 'selection',
                'contact'          => $contact,
                'selections'       => $this->selectionService->findSelectionsByContact($contact),
                'contactService'   => $this->contactService,
                'selectionService' => $this->selectionService,
            ]
        );
    }

    public function mailingAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $contact = $this->contactService->findContactById((int)$this->params('id'));

        if ($contact === null) {
            return $this->notFoundAction();
        }

        $data = $request->getPost()->toArray();
        if ($request->isPost()) {
            $this->contactService->updateOptInForContact($contact, $data['optIn'] ?? []);

            $changelogMessage = sprintf(
                $this->translator->translate('txt-opt-in-of-contact-%s-has-been-updated-successfully'),
                $contact->getDisplayName()
            );

            $this->flashMessenger()->addSuccessMessage($changelogMessage);
            $this->contactService->addNoteToContact($changelogMessage, 'office', $this->identity());

            return $this->redirect()->toRoute('zfcadmin/contact/view/mailing', ['id' => $contact->getId()]);
        }


        return new ViewModel(
            [
                'tab'            => 'mailing',
                'contact'        => $contact,
                'optIn'          => $this->contactService->findAll(OptIn::class),
                'contactService' => $this->contactService
            ]
        );
    }

    public function ideaAction(): ViewModel
    {
        $contact = $this->contactService->findContactById((int)$this->params('id'));

        if ($contact === null) {
            return $this->notFoundAction();
        }

        return new ViewModel(
            [
                'tab'            => 'idea',
                'contact'        => $contact,
                'contactService' => $this->contactService,
            ]
        );
    }

    public function projectAction(): ViewModel
    {
        $contact = $this->contactService->findContactById((int)$this->params('id'));

        if ($contact === null) {
            return $this->notFoundAction();
        }

        return new ViewModel(
            [
                'tab'            => 'project',
                'contact'        => $contact,
                'projectService' => $this->projectService,
                'contactService' => $this->contactService,
                'projects'       => $this->projectService->findProjectParticipationByContact($contact),
            ]
        );
    }

    public function legalAction(): ViewModel
    {
        $contact = $this->contactService->findContactById((int)$this->params('id'));

        if ($contact === null) {
            return $this->notFoundAction();
        }

        return new ViewModel(
            [
                'tab'            => 'legal',
                'contact'        => $contact,
                'contactService' => $this->contactService,
            ]
        );
    }

    public function eventAction(): ViewModel
    {
        $contact = $this->contactService->findContactById((int)$this->params('id'));

        if ($contact === null) {
            return $this->notFoundAction();
        }

        return new ViewModel(
            [
                'tab'                 => 'event',
                'contact'             => $contact,
                'registrationService' => $this->registrationService,
                'contactService'      => $this->contactService,
                'callService'         => $this->callService,
                'boothService'        => $this->boothService
            ]
        );
    }

    public function calendarAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $contact = $this->contactService->findContactById((int)$this->params('id'));

        if ($contact === null) {
            return $this->notFoundAction();
        }

        $data = $request->getPost()->toArray();
        if ($request->isPost()) {
            foreach ((array)($data['calendarContact'] ?? []) as $calendarContactId) {
                /** @var Contact $calendarContact */
                $calendarContact = $this->calendarService->find(Contact::class, (int)$calendarContactId);
                if (null !== $calendarContact) {
                    $this->calendarService->delete($calendarContact);
                }
            }

            return $this->redirect()->toRoute('zfcadmin/contact/view/calendar', ['id' => $contact->getId()]);
        }

        return new ViewModel(
            [
                'tab'             => 'calendar',
                'contact'         => $contact,
                'calendarService' => $this->calendarService,
                'contactService'  => $this->contactService,
            ]
        );
    }

    public function mergeAction(): ViewModel
    {
        $contact = $this->contactService->findContactById((int)$this->params('id'));

        if ($contact === null) {
            return $this->notFoundAction();
        }

        $mergeForm = new ContactMerge($this->entityManager, $contact);

        return new ViewModel(
            [
                'tab'            => 'merge',
                'contactService' => $this->contactService,
                'contact'        => $contact,
                'mergeForm'      => $mergeForm

            ]
        );
    }
}
