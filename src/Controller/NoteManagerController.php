<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Note
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Controller;

use Contact\Entity\Note;
use Contact\Service\ContactService;
use Contact\Service\FormService;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\View\Model\ViewModel;

/**
 *
 */
final class NoteManagerController extends ContactAbstractController
{
    /**
     * @var ContactService
     */
    private $contactService;
    /**
     * @var FormService
     */
    private $formService;
    /**
     * @var TranslatorInterface
     */
    private $translator;

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
            if (!isset($data['cancel']) && $form->isValid()) {
                /**
                 * @var $note Note
                 */
                $note = $form->getData();
                $note->setContact($contact);
                $this->contactService->save($note);
            }

            return $this->redirect()
                ->toRoute(
                    'zfcadmin/contact/view',
                    ['id' => $contact->getId()],
                    ['fragment' => 'general']
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
                    sprintf($this->translator->translate("txt-note-has-successfully-been-deleted"))
                );

                return $this->redirect()->toRoute(
                    'zfcadmin/contact/view',
                    ['id' => $note->getContact()->getId()],
                    ['fragment' => 'general']
                );
            }


            if (!isset($data['cancel']) && $form->isValid()) {
                $note = $form->getData();
                $note = $this->contactService->save($note);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf($this->translator->translate("txt-note-has-successfully-been-updated"))
                );
            }

            return $this->redirect()->toRoute(
                'zfcadmin/contact/view',
                ['id' => $note->getContact()->getId()],
                ['fragment' => 'general']
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
