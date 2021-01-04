<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\View\Helper\Selection;

use Contact\Entity\Selection\Type;
use General\ValueObject\Link\Link;
use General\View\Helper\AbstractLink;

/**
 * Class TypeLink
 * @package Contact\View\Helper
 */
final class TypeLink extends AbstractLink
{
    public function __invoke(
        Type $type = null,
        string $action = 'view',
        string $show = 'name'
    ): string {
        $type ??= new Type();

        $routeParams = [];
        $showOptions = [];
        if (! $type->isEmpty()) {
            $routeParams['id']          = $type->getId();
            $showOptions['name']        = $type->getName();
            $showOptions['description'] = $type->getDescription();
        }

        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon'  => 'fas fa-plus',
                    'route' => 'zfcadmin/selection/type/new',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-new-selection-type')
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon'  => 'far fa-edit',
                    'route' => 'zfcadmin/selection/type/edit',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-selection-type')
                ];
                break;
            case 'view':
                $linkParams = [
                    'icon'  => 'fas fa-link',
                    'route' => 'zfcadmin/selection/type/view',
                    'text'  => $showOptions[$show] ?? $type->getName()
                ];
                break;
        }

        $linkParams['action']      = $action;
        $linkParams['show']        = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
