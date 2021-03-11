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
use General\View\Helper\AbstractLink;

/**
 * Class PasswordLink
 * @package General\View\Helper
 */
final class ProfileLink extends AbstractLink
{
    public function __invoke(
        Contact $contact,
        string $action = 'contact',
        string $show = 'name'
    ): string {
        if (! $this->hasAccess($contact, \Contact\Acl\Assertion\Profile::class, $action)) {
            return $action === 'contact' ? $contact->parseFullName() : '';
        }

        $routeParams = [];
        $showOptions = [];

        $routeParams['id']   = $contact->getId();
        $routeParams['hash'] = $contact->getHash();

        $showOptions['name'] = $contact->parseFullName();


        switch ($action) {
            case 'view':
                $linkParams = [
                    'icon'  => 'far fa-user',
                    'route' => 'community/contact/profile/view',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-view-profile')
                ];
                break;
            case 'profile': //legacy
            case 'contact':
                $linkParams = [
                    'icon'  => 'far fa-user',
                    'route' => 'community/contact/profile/contact',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-view-profile')
                ];
                break;
            case 'my':
                $linkParams = [
                    'icon'  => 'far fa-user',
                    'route' => 'community/contact/profile/my',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-my-public-profile')
                ];
                break;
            case 'send-message':
                $linkParams = [
                    'icon'  => 'far fa-envelope',
                    'route' => 'community/contact/profile/send-message',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-send-message')
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon'  => 'far fa-edit',
                    'route' => 'community/contact/profile/edit',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-your-profile')
                ];
                break;
        }

        $linkParams['action']      = $action;
        $linkParams['show']        = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
