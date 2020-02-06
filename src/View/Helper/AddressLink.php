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
use Contact\Entity\Address;
use General\ValueObject\Link\Link;
use General\View\Helper\AbstractLink;

/**
 * Class PasswordLink
 * @package General\View\Helper
 */
final class AddressLink extends AbstractLink
{
    public function __invoke(
        Address $address = null,
        string $action = 'view',
        string $show = 'text',
        Contact $contact = null
    ): string {
        $address ??= new Address();

        if (! $this->hasAccess($address, \Contact\Acl\Assertion\Address::class, $action)) {
            return '';
        }

        $routeParams = [];
        $showOptions = [];

        if (! $address->isEmpty()) {
            $routeParams['id'] = $address->getId();
            $showOptions['name'] = $address->getAddress();
        }

        if (null !== $contact) {
            $routeParams['contact'] = $contact->getId();
        }


        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon' => 'fas fa-plus',
                    'route' => 'zfcadmin/address/new',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-new-address')
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon' => 'far fa-edit',
                    'route' => 'zfcadmin/address/edit',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-address')
                ];
                break;
        }

        $linkParams['action'] = $action;
        $linkParams['show'] = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
