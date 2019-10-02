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
use Contact\Entity\Facebook;
use Zend\Navigation\Page\Mvc;

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
