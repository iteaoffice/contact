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
use Contact\Entity\Contact;
use Contact\Entity\Dnd;
use Laminas\Navigation\Page\Mvc;

/**
 * Class FunderLabel
 *
 * @package Funder\Navigation\Invokable
 */
final class DndLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translate('txt-nav-dnd');

        if ($this->getEntities()->containsKey(Dnd::class)) {
            /** @var Dnd $dnd */
            $dnd = $this->getEntities()->get(Dnd::class);
            $this->getEntities()->set(Contact::class, $dnd->getContact());

            $page->setParams(
                array_merge(
                    $page->getParams(),
                    [
                        'id' => $dnd->getId(),
                    ]
                )
            );
            $label = (string)$dnd->parseFileName();
        }
        $page->set('label', $label);
    }
}
