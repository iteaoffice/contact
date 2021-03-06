<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\View\Helper;

use Contact\Entity\Contact;
use Contact\Entity\Selection;
use General\ValueObject\Link\Link;
use General\View\Helper\AbstractLink;

/**
 * Class SelectionLink
 * @package Contact\View\Helper
 */
final class SelectionLink extends AbstractLink
{
    public function __invoke(
        Selection $selection = null,
        string $action = 'view',
        string $show = 'name',
        Contact $contact = null
    ): string {
        $selection ??= new Selection();

        $routeParams = [];
        $showOptions = [];
        if (! $selection->isEmpty()) {
            $routeParams['id']   = $selection->getId();
            $showOptions['name'] = $selection->getSelection();
        }

        if (null !== $contact) {
            $routeParams['contactId'] = $contact->getId();
        }

        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon'  => 'fas fa-plus',
                    'route' => 'zfcadmin/selection/new',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-new-selection')
                ];
                break;
            case 'copy':
                $linkParams = [
                    'icon'  => 'far fa-clone',
                    'route' => 'zfcadmin/selection/copy',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-copy-selection')
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon'  => 'far fa-edit',
                    'route' => 'zfcadmin/selection/edit',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-selection')
                ];
                break;
            case 'generate-deeplinks':
                $linkParams = [
                    'icon'  => 'fa-external-link',
                    'route' => 'zfcadmin/selection/generate-deeplinks',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-generate-deeplinks')
                ];
                break;
            case 'edit-contacts':
                $linkParams = [
                    'icon'  => 'fas fa-users',
                    'route' => 'zfcadmin/selection/edit-contacts',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-contacts-selection')
                ];
                break;
            case 'add-contact':
                $linkParams = [
                    'icon'  => 'fas fa-user-plus',
                    'route' => 'zfcadmin/selection/add-contact',
                    'text'  => $showOptions[$show]
                        ?? sprintf($this->translator->translate('txt-add-%s-to-selection'), $contact->parseFullName())
                ];
                break;
            case 'view':
                $linkParams = [
                    'icon'  => 'fas fa-link',
                    'route' => 'zfcadmin/selection/view',
                    'text'  => $showOptions[$show] ?? $selection->getSelection()
                ];
                break;
            case 'export-csv':
                $routeParams['type'] = 'csv';
                $linkParams          = [
                    'icon'  => 'far fa-file-alt',
                    'route' => 'zfcadmin/selection/export',
                    'text'  => $showOptions[$show] ?? $this->translator->translate('txt-export-selection-to-csv')
                ];
                break;
            case 'export-excel':
                $routeParams['type'] = 'excel';
                $linkParams          = [
                    'icon'  => 'far fa-file-excel',
                    'route' => 'zfcadmin/selection/export',
                    'text'  => $showOptions[$show] ?? $this->translator->translate('txt-export-selection-to-excel')
                ];
                break;
        }

        $linkParams['action']      = $action;
        $linkParams['show']        = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
