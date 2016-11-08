<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Note
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Contact\Controller;

use Contact\Entity\Contact;
use Contact\Entity\Note;
use Zend\View\Model\ViewModel;

/**
 *
 */
class NoteManagerController extends ContactAbstractController
{
    /**
     * Create a new note.
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function newAction()
    {
        /**
         * @var $contact Contact
         */
        $contact = $this->getContactService()->findContactById($this->params('contact'));

        if (is_null($contact)) {
            return $this->notFoundAction();
        }

        $data = array_merge_recursive($this->getRequest()->getPost()->toArray());

        $form = $this->getFormService()->prepare(Note::class, null, $data);
        $form->remove('delete');

        if ($this->getRequest()->isPost()) {
            if (! isset($data['cancel']) && $form->isValid()) {
                /**
                 * @var $note Note
                 */
                $note = $form->getData();
                $note->setContact($contact);
                $this->getContactService()->newEntity($note);
            }

            return $this->redirect()
                ->toRoute('zfcadmin/contact-admin/view', ['id' => $contact->getId()], ['fragment' => 'general']);
        }

        return new ViewModel(
            [
                'form'    => $form,
                'contact' => $contact,

            ]
        );
    }

    /**
     * Edit an note by finding it and call the corresponding form.
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        /**
         * @var $note Note
         */
        $note = $this->getContactService()->findEntityById(Note::class, $this->params('id'));
        $data = array_merge_recursive($this->getRequest()->getPost()->toArray());
        $form = $this->getFormService()->prepare($note, $note, $data);

        if ($this->getRequest()->isPost()) {
            /**
             * Handle the delete request
             */
            if (isset($data['delete'])) {
                $this->getContactService()->removeEntity($note);
                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(sprintf($this->translate("txt-note-has-successfully-been-deleted")));

                return $this->redirect()->toRoute(
                    'zfcadmin/contact-admin/view',
                    ['id' => $note->getContact()->getId()],
                    ['fragment' => 'general']
                );
            }


            if (! isset($data['cancel']) && $form->isValid()) {
                /**
                 * @var Note $note
                 */
                $note = $form->getData();
                $note = $this->getContactService()->updateEntity($note);
            }

            return $this->redirect()->toRoute(
                'zfcadmin/contact-admin/view',
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
