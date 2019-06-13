<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */
declare(strict_types=1);

namespace Contact\Navigation\Invokable;

use Admin\Navigation\Invokable\AbstractNavigationInvokable;
use Contact\Entity\Contact;
use Contact\Entity\Dnd;
use Zend\Navigation\Page\Mvc;

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
