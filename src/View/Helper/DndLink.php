<?php

/**
 * ITEA Office all rights reserved
 *
 * @category   Program
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */

declare(strict_types=1);

namespace Contact\View\Helper;

use Contact\Entity\Contact;
use Contact\Entity\Dnd;

/**
 * Class DndLink
 *
 * @package Program\View\Helper
 */
class DndLink extends LinkAbstract
{
    public function __invoke(
        Dnd $dnd = null,
        string $action = 'view',
        string $show = 'button',
        ?Contact $contact = null
    ): string {
        $this->setAction($action);
        $this->setShow($show);

        if ($dnd !== null) {
            $this->addRouterParam('id', $dnd->getId());
        }
        if ($contact !== null) {
            $this->addRouterParam('contactId', $contact->getId());
        }

        $this->parseAction();

        return $this->createLink();
    }

    public function parseAction(): void
    {
        switch ($this->getAction()) {
            case 'new':
                $this->setRouter('zfcadmin/contact/dnd/new');
                $this->setText($this->translate('txt-upload-dnd'));
                break;
            case 'edit':
                $this->setRouter('zfcadmin/contact/dnd/edit');
                $this->setText($this->translate('txt-edit-dnd'));
                break;
                break;
            case 'download':
                $this->setRouter('zfcadmin/contact/dnd/download');
                $this->setText($this->translate('txt-download-dnd'));
                break;
        }
    }
}
