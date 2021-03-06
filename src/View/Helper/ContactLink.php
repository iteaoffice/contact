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
use General\ValueObject\Link\Link;
use General\ValueObject\Link\LinkDecoration;
use General\View\Helper\AbstractLink;

/**
 * Class ContactLink
 * @package Contact\View\Helper
 */
final class ContactLink extends AbstractLink
{
    public function __invoke(
        Contact $contact = null,
        string $action = 'view',
        string $show = 'name'
    ): string {
        $contact ??= new Contact();

        if (! $this->hasAccess($contact, \Contact\Acl\Assertion\Contact::class, $action)) {
            return '';
        }

        $routeParams = [];
        $showOptions = [];
        if (! $contact->isEmpty()) {
            $routeParams['id']        = $contact->getId();
            $showOptions['name']      = $contact->parseFullName();
            $showOptions['email']     = $contact->getEmail();
            $showOptions['firstname'] = $contact->getFirstName();
            $showOptions['initials']  = $contact->parseInitials();
        }


        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon'  => 'fas fa-plus',
                    'route' => 'zfcadmin/contact/new',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-new-contact')
                ];
                break;
            case 'list-old':
                $linkParams = [
                    'icon'  => 'fas fa-users',
                    'route' => 'zfcadmin/contact/list-old',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-list-contacts-legacy')
                ];
                break;
            case 'import':
                $linkParams = [
                    'icon'  => 'fas fa-upload',
                    'route' => 'zfcadmin/contact/import',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-import-contacts')
                ];
                break;
            case 'edit-admin':
                $linkParams = [
                    'icon'  => 'far fa-edit',
                    'route' => 'zfcadmin/contact/edit',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-contact')
                ];
                break;
            case 'change-password':
                $text = $this->translator->translate('txt-update-your-password');

                if (null === $contact->getSaltedPassword()) {
                    $text = $this->translator->translate('txt-set-your-password');
                    $show = LinkDecoration::SHOW_DANGER_BUTTON;
                }

                $linkParams = [
                    'icon'  => 'fas fa-key',
                    'route' => 'community/contact/change-password',
                    'text'  => $text
                ];
                break;
            case 'view-admin':
                $linkParams = [
                    'icon'  => 'far fa-user',
                    'route' => 'zfcadmin/contact/view/general',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-view-contact-in-admin')
                ];
                break;
            case 'view-project-admin':
                $linkParams = [
                    'icon'  => 'far fa-user',
                    'route' => 'zfcadmin/contact/view/project',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-view-projects-of-contact-in-admin')
                ];
                break;
            case 'add-project':
                $linkParams = [
                    'icon'  => 'fas fa-user-plus',
                    'route' => 'zfcadmin/contact/add-project',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-add-contact-to-project')
                ];
                break;
            case 'impersonate':
                $linkParams = [
                    'icon'  => 'fas fa-user-secret',
                    'route' => 'zfcadmin/contact/impersonate',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-impersonate-contact')
                ];
                break;
            case 'permit':
                $linkParams = [
                    'icon'  => 'fas fa-lock',
                    'route' => 'zfcadmin/contact/permit',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-permissions')
                ];
                break;
        }

        $linkParams['action']      = $action;
        $linkParams['show']        = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
