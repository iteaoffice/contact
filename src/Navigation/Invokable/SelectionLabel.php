<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\Navigation\Invokable;

use General\Navigation\Invokable\AbstractNavigationInvokable;
use Contact\Entity\Selection;
use Laminas\Navigation\Page\Mvc;

/**
 * Class FunderLabel
 *
 * @package Funder\Navigation\Invokable
 */
final class SelectionLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translate('txt-nav-selection');

        if ($this->getEntities()->containsKey(Selection::class)) {
            /** @var Selection $selection */
            $selection = $this->getEntities()->get(Selection::class);

            $page->setParams(
                array_merge(
                    $page->getParams(),
                    [
                        'id' => $selection->getId(),
                    ]
                )
            );
            $label = (string)$selection->getSelection();
        }
        $page->set('label', $label);
    }
}
