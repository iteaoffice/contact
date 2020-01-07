<?php
/**
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\View\Helper\Office;

use Contact\Acl\Assertion\Office\LeaveAssertion;
use Contact\Entity\Office\Contact;
use Contact\Entity\Office\Leave;
use General\ValueObject\Link\Link;
use General\View\Helper\AbstractLink;

/**
 * Class LeaveLink
 * @package Contact\View\Helper\Office
 */
final class LeaveLink extends AbstractLink
{
    public function __invoke(
        Leave $leave = null,
        string $action = 'view',
        string $show = 'name',
        Contact $officeContact = null
    ): string {
        $leave ??= new Leave();

        if (! $this->hasAccess($leave, LeaveAssertion::class, $action)) {
            return '';
        }

        $routeParams = [];
        $showOptions = [];

        if (! $leave->isEmpty()) {
            $routeParams['id'] = $leave->getId();
            $showOptions['name'] = $leave->getDescription();
        }

        if (null !== $officeContact) {
            $routeParams['officeContactId'] = $officeContact->getId();
        }

        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon' => 'fa-plus',
                    'route' => 'zfcadmin/contact/office/leave/new',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-new-leave')
                ];
                break;
            case 'new-admin':
                $linkParams = [
                    'icon' => 'fa-plus',
                    'route' => 'zfcadmin/contact/office/new-leave',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-new-leave')
                ];
                break;
            case 'list':
                $linkParams = [
                    'icon' => 'fa-list',
                    'route' => 'zfcadmin/contact/office/leave/list',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-list-leave')
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon' => 'fa-pencil-square-o',
                    'route' => 'zfcadmin/contact/office/leave/edit',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-leave')
                ];
                break;
            case 'edit-admin':
                $linkParams = [
                    'icon' => 'fa-pencil-square-o',
                    'route' => 'zfcadmin/contact/office/edit-leave',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-leave')
                ];
                break;
            case 'view':
                $linkParams = [
                    'icon' => 'fa-sign-out',
                    'route' => 'zfcadmin/contact/office/leave',
                    'text' => $showOptions[$show] ?? $leave->getDescription()
                ];
                break;
        }

        $linkParams['action'] = $action;
        $linkParams['show'] = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
