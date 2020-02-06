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
        string $action = 'profile',
        string $show = 'name'
    ): string {
        if (! $this->hasAccess($contact, \Contact\Acl\Assertion\Contact::class, $action)) {
            return '';
        }

        $routeParams = [];
        $showOptions = [];

        $routeParams['id'] = $contact->getId();
        $showOptions['name'] = $contact->parseFullName();


        switch ($action) {
            case 'profile':
                $linkParams = [
                    'icon' => 'far fa-user',
                    'route' => 'community/contact/profile/view',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-view-profile')
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon' => 'far fa-edit',
                    'route' => 'community/contact/profile/edit',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-your-profile')
                ];
                break;
        }

        $linkParams['action'] = $action;
        $linkParams['show'] = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
