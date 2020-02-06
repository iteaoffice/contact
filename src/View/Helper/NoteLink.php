<?php

/**
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/general for the canonical source repository
 */

declare(strict_types=1);

namespace Contact\View\Helper;

use Contact\Entity\Contact;
use Contact\Entity\Note;
use General\ValueObject\Link\Link;
use General\View\Helper\AbstractLink;

/**
 * Class PasswordLink
 * @package General\View\Helper
 */
final class NoteLink extends AbstractLink
{
    public function __invoke(
        Note $note = null,
        string $action = 'view',
        string $show = 'text',
        Contact $contact = null
    ): string {
        $note ??= new Note();

        if (! $this->hasAccess($note, \Contact\Acl\Assertion\Note::class, $action)) {
            return '';
        }

        $routeParams = [];
        $showOptions = [];

        if (! $note->isEmpty()) {
            $routeParams['id'] = $note->getId();
            $showOptions['name'] = $note->getNote();
        }

        if (null !== $contact) {
            $routeParams['contact'] = $contact->getId();
        }


        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon' => 'fas fa-plus',
                    'route' => 'zfcadmin/note/new',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-new-note')
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon' => 'far fa-edit',
                    'route' => 'zfcadmin/note/edit',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-note')
                ];
                break;
        }

        $linkParams['action'] = $action;
        $linkParams['show'] = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
