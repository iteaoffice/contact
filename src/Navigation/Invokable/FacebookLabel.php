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
use Contact\Entity\Facebook;
use Laminas\Navigation\Page\Mvc;

/**
 * Class FacebookLabel
 *
 * @package Facebook\Navigation\Invokable
 */
class FacebookLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translate('txt-nav-facebook');

        if ($this->getEntities()->containsKey(Facebook::class)) {
            /** @var Facebook $facebook */
            $facebook = $this->getEntities()->get(Facebook::class);

            $page->setParams(
                array_merge(
                    $page->getParams(),
                    [
                        'id' => $facebook->getId(),
                    ]
                )
            );
            $label = (string)$facebook->getFacebook();
        }
        $page->set('label', $label);
    }
}
