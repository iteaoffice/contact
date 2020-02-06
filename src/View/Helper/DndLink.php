<?php

/**
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\View\Helper;

use Contact\Entity\Contact;
use Contact\Entity\Dnd;
use General\ValueObject\Link\Link;
use General\View\Helper\AbstractLink;

/**
 * Class DndLink
 * @package Contact\View\Helper
 */
final class DndLink extends AbstractLink
{
    public function __invoke(
        Dnd $dnd = null,
        string $action = 'view',
        string $show = 'name',
        Contact $contact = null
    ): string {
        $dnd ??= new Dnd();

        $routeParams = [];
        $showOptions = [];
        if (! $dnd->isEmpty()) {
            $routeParams['id'] = $dnd->getId();
        }

        if (null !== $contact) {
            $routeParams['contactId'] = $contact->getId();
        }

        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon' => 'fas fa-upload',
                    'route' => 'zfcadmin/contact/dnd/new',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-upload-dnd')
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon' => 'far fa-edit',
                    'route' => 'zfcadmin/contact/dnd/edit',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-dnd')
                ];
                break;
            case 'download':
                $linkParams = [
                    'icon' => 'fas fa-download',
                    'route' => 'zfcadmin/contact/dnd/download',
                    'text' => $showOptions[$show] ?? $this->translator->translate('txt-download-dnd')
                ];
                break;
        }

        $linkParams['action'] = $action;
        $linkParams['show'] = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
