<?php

/**
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\View\Helper\Office;

use Contact\Acl\Assertion\Office\ContactAssertion;
use Contact\Entity\Office\Contact;
use General\ValueObject\Link\Link;
use General\View\Helper\AbstractLink;

/**
 * Class ContactLink
 * @package Contact\View\Helper\Office
 */
final class ContactLink extends AbstractLink
{
    public function __invoke(
        Contact $officeContact = null,
        string $action = 'view',
        string $show = 'name'
    ): string {
        $officeContact ??= new Contact();

        if (! $this->hasAccess($officeContact, ContactAssertion::class, $action)) {
            return '';
        }

        $routeParams = [];
        $showOptions = [];

        if (! $officeContact->isEmpty()) {
            $routeParams['id'] = $officeContact->getId();
            $showOptions['name'] = $officeContact->getContact()->getDisplayName();
        }

        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon' => 'fas fa-plus',
                    'route' => 'zfcadmin/contact/office/new',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-new-office-member')
                ];
                break;
            case 'list':
                $linkParams = [
                    'icon' => 'fas fa-list',
                    'route' => 'zfcadmin/contact/office/list',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-list-office-members')
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon' => 'far fa-edit',
                    'route' => 'zfcadmin/contact/office/edit',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-office-member')
                ];
                break;
            case 'view':
                $linkParams = [
                    'icon' => 'far fa-user',
                    'route' => 'zfcadmin/contact/office/view',
                    'text' => $showOptions[$show] ?? $this->translator->translate('txt-view-office-member')
                ];
                break;
        }

        $linkParams['action'] = $action;
        $linkParams['show'] = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
