<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */

namespace Contact\View\Helper;

use Contact\Acl\Assertion\Note as NoteAssertion;
use Contact\Entity\Contact;
use Contact\Entity\Note;

/**
 * Create a link to an note.
 *
 * @category    Note
 */
class NoteLink extends LinkAbstract
{
    /**
     * @param Note|null $note
     * @param string $action
     * @param string $show
     * @param Contact|null $contact
     * @return string
     */
    public function __invoke(
        Note $note = null,
        $action = 'view',
        $show = 'name',
        Contact $contact = null
    ) {
        $this->setNote($note);
        $this->setAction($action);
        $this->setShow($show);
        $this->setContact($contact);

        if (!$this->hasAccess(
            $this->getNote(),
            NoteAssertion::class,
            $this->getAction()
        )
        ) {
            return '';
        }

        $this->setShowOptions(
            [
                'name' => $this->getNote()->getNote(),
            ]
        );
        $this->addRouterParam('id', $this->getNote()->getId());
        $this->addRouterParam('contact', $this->getContact()->getId());

        return $this->createLink();
    }

    /**
     * @throws \Exception
     */
    public function parseAction()
    {
        switch ($this->getAction()) {
            case 'new':
                if (is_null($this->getContact())) {
                    throw new \Exception(sprintf("A contact is needed for a new note"));
                }

                $this->setRouter('zfcadmin/note-manager/new');
                $this->setText($this->translate("txt-new-note"));
                break;
            case 'edit':
                $this->setRouter('zfcadmin/note-manager/edit');
                $this->setText(
                    sprintf($this->translate("txt-edit-note-%s"), $this->getNote()->getNote())
                );
                break;
            default:
                throw new \Exception(sprintf("%s is an incorrect action for %s", $this->getAction(), __CLASS__));
        }
    }
}
