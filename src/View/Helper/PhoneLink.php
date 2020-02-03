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
use Contact\Entity\Phone;
use General\ValueObject\Link\Link;
use General\View\Helper\AbstractLink;

/**
 * Class PasswordLink
 * @package General\View\Helper
 */
final class PhoneLink extends AbstractLink
{
    public function __invoke(
        Phone $phone = null,
        string $action = 'view',
        string $show = 'text',
        Contact $contact = null
    ): string {
        $phone ??= new Phone();

        if (!$this->hasAccess($phone, \Contact\Acl\Assertion\Phone::class, $action)) {
            return '';
        }

        $routeParams = [];
        $showOptions = [];

        if (!$phone->isEmpty()) {
            $routeParams['id'] = $phone->getId();
            $showOptions['name'] = $phone->getPhone();
        }

        if (null !== $contact) {
            $routeParams['contact'] = $contact->getId();
        }


        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon' => 'fas fa-plus',
                    'route' => 'zfcadmin/phone/new',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-new-phone')
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon' => 'far fa-edit',
                    'route' => 'zfcadmin/phone/edit',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-phone')
                ];
                break;
        }

        $linkParams['action'] = $action;
        $linkParams['show'] = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
