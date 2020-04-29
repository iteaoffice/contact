<?php

/**
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\View\Helper;

use Contact\Entity\Facebook;
use General\ValueObject\Link\Link;
use General\View\Helper\AbstractLink;

/**
 * Class FacebookLink
 * @package Contact\View\Helper
 */
final class FacebookLink extends AbstractLink
{
    public function __invoke(
        Facebook $facebook = null,
        string $action = 'view',
        string $show = 'name'
    ): string {
        $facebook ??= new Facebook();

        if (! $this->hasAccess($facebook, \Contact\Acl\Assertion\Facebook::class, $action)) {
            return '';
        }

        $routeParams = [];
        $showOptions = [];
        if (! $facebook->isEmpty()) {
            $routeParams['id']   = $facebook->getId();
            $showOptions['name'] = $facebook->getFacebook();
        }

        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon'  => 'fas fa-plus',
                    'route' => 'zfcadmin/facebook/new',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-new-facebook')
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon'  => 'far fa-edit',
                    'route' => 'zfcadmin/facebook/edit',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-facebook')
                ];
                break;
            case 'view-community':
                $linkParams = [
                    'icon'  => 'fas fa-users',
                    'route' => 'community/contact/facebook/view',
                    'text'  => $showOptions[$show] ?? $facebook->getFacebook()
                ];
                break;
            case 'send-message':
                $linkParams = [
                    'icon'  => 'fas fa-users',
                    'route' => 'community/contact/facebook/send-message',
                    'text'  => $showOptions[$show] ?? $this->translator->translate('txt-send-message')
                ];
                break;
            case 'view-admin':
                $linkParams = [
                    'icon'  => 'fas fa-users',
                    'route' => 'zfcadmin/facebook/view',
                    'text'  => $showOptions[$show] ?? $facebook->getFacebook()
                ];
                break;
        }

        $linkParams['action']      = $action;
        $linkParams['show']        = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
