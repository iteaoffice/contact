<?php

/**
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\View\Helper;

use Contact\Entity\OptIn;
use General\ValueObject\Link\Link;
use General\View\Helper\AbstractLink;

/**
 * Class OptInLink
 * @package Contact\View\Helper
 */
final class OptInLink extends AbstractLink
{
    public function __invoke(
        OptIn $optIn = null,
        string $action = 'view',
        string $show = 'name'
    ): string {
        $optIn ??= new OptIn();

        $routeParams = [];
        $showOptions = [];
        if (! $optIn->isEmpty()) {
            $routeParams['id'] = $optIn->getId();
            $showOptions['name'] = $optIn->getOptIn();
        }

        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon' => 'fa-plus',
                    'route' => 'zfcadmin/opt-in/new',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-new-opt-in')
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon' => 'fa-pencil-square-o',
                    'route' => 'zfcadmin/opt-in/edit',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-opt-in')
                ];
                break;
            case 'view':
                $linkParams = [
                    'icon' => 'fa-link',
                    'route' => 'zfcadmin/opt-in/view',
                    'text' => $showOptions[$show] ?? $optIn->getOptIn()
                ];
                break;
        }

        $linkParams['action'] = $action;
        $linkParams['show'] = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
