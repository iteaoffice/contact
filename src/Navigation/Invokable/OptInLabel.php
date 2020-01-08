<?php

/**
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Contact\Navigation\Invokable;

use Admin\Navigation\Invokable\AbstractNavigationInvokable;
use Contact\Entity\OptIn;
use Laminas\Navigation\Page\Mvc;

/**
 * Class OptInLabel
 *
 * @package Contact\Navigation\Invokable
 */
class OptInLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translate('txt-nav-opt-in');

        if ($this->getEntities()->containsKey(OptIn::class)) {
            /** @var OptIn $optIn */
            $optIn = $this->getEntities()->get(OptIn::class);

            $page->setParams(
                array_merge(
                    $page->getParams(),
                    [
                        'id' => $optIn->getId(),
                    ]
                )
            );
            $label = (string)$optIn->getOptIn();
        }
        $page->set('label', $label);
    }
}
