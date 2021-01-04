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

use Contact\Entity\Note;
use Contact\Service\ContactService;
use Contact\Service\FormService;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\View\Model\ViewModel;

/**
 * Class NoteManagerController
 * @package Contact\Controller
 */
final class NoteManagerController extends ContactAbstractController
{
    private ContactService $contactService;
    private FormService $formService;
    private TranslatorInterface $translator;

    public function __construct(
        ContactService $contactService,
        FormService $formService,
        TranslatorInterface $translator
    ) {
        $this->contactService = $contactService;
        $this->formService = $formService;
        $this->translator = $translator;
    }

    public function newAction()
    {
        $contact = $this->contactService->findContactById((int)$this->params('contact'));

        if (null === $contact) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();

        $form = $this->formService->prepare(Note::class, $data);
        $form->remove('delete');

        if ($this->getRequest()->isPost()) {
            if (! isset($data['cancel']) && $form->isValid()) {
                /**
                 * @var $note Note
                 */
                $note = $form->getData();
                $note->setContact($contact);
                $this->contactService->save($note);
            }

            return $this->redirect()
                ->toRoute(
                    'zfcadmin/contact/view/notes',
                    ['id' => $contact->getId()]
                );
        }

        return new ViewModel(
            [
                'form'    => $form,
                'contact' => $contact,

            ]
        );
    }

    public function editAction()
    {
        /**
         * @var $note Note
         */
        $note = $this->contactService->find(Note::class, (int)$this->params('id'));
        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->formService->prepare($note, $data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['delete'])) {
                $this->contactService->delete($note);
                $this->flashMessenger()->addSuccessMessage(
                    sprintf($this->translator->translate('txt-note-has-successfully-been-deleted'))
                );

                return $this->redirect()->toRoute(
                    'zfcadmin/contact/view/notes',
                    ['id' => $note->getContact()->getId()]
                );
            }


            if (! isset($data['cancel']) && $form->isValid()) {
                $note = $form->getData();
                $note = $this->contactService->save($note);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf($this->translator->translate('txt-note-has-successfully-been-updated'))
                );
            }

            return $this->redirect()->toRoute(
                'zfcadmin/contact/view/notes',
                ['id' => $note->getContact()->getId()]
            );
        }

        return new ViewModel(
            [
                'form'    => $form,
                'contact' => $note->getContact(),

            ]
        );
    }
}
