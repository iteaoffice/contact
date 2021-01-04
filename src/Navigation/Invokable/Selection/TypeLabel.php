<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\Navigation\Invokable\Selection;

use Contact\Entity\Selection\Type;
use General\Navigation\Invokable\AbstractNavigationInvokable;
use Laminas\Navigation\Page\Mvc;

/**
 * Class TypeLabel
 *
 * @package Type\Navigation\Invokable
 */
final class TypeLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translate('txt-nav-type');

        if ($this->getEntities()->containsKey(Type::class)) {
            /** @var Type $type */
            $type = $this->getEntities()->get(Type::class);

            $page->setParams(
                array_merge(
                    $page->getParams(),
                    [
                        'id' => $type->getId(),
                    ]
                )
            );
            $label = sprintf("%s", $type->getName());
        }

        if (null === $page->getLabel()) {
            $page->set('label', $label);
        }
    }
}
