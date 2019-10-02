<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\View\Helper;

use Contact\Acl\Assertion\Note as NoteAssertion;
use Contact\Entity\Contact;
use Contact\Entity\Note;
use Exception;
use function is_null;

/**
 * Create a link to an note.
 *
 * @category    Note
 */
class NoteLink extends LinkAbstract
{
    /**
     * @param Note|null    $note
     * @param string       $action
     * @param string       $show
     * @param Contact|null $contact
     *
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

        if (!$this->hasAccess($this->getNote(), NoteAssertion::class, $this->getAction())) {
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
     * @throws Exception
     */
    public function parseAction(): void
    {
        switch ($this->getAction()) {
            case 'new':
                if (is_null($this->getContact())) {
                    throw new Exception(sprintf("A contact is needed for a new note"));
                }

                $this->setRouter('zfcadmin/note/new');
                $this->setText($this->translate("txt-new-note"));
                break;
            case 'edit':
                $this->setRouter('zfcadmin/note/edit');
                $this->setText(sprintf($this->translate("txt-edit-note-%s"), $this->getNote()->getNote()));
                break;
            default:
                throw new Exception(sprintf("%s is an incorrect action for %s", $this->getAction(), __CLASS__));
        }
    }
}
