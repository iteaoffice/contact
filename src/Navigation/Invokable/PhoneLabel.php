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
use Contact\Entity\Phone;
use Laminas\Navigation\Page\Mvc;

/**
 * Class PhoneLabel
 *
 * @package Phone\Navigation\Invokable
 */
class PhoneLabel extends AbstractNavigationInvokable
{
    /**
     * Parse a Funder Phone label
     *
     * @param Mvc $page
     *
     * @return void
     */
    public function __invoke(Mvc $page): void
    {
        if ($this->getEntities()->containsKey(Phone::class)) {
            /** @var Phone $phone */
            $phone = $this->getEntities()->get(Phone::class);

            $this->getEntities()->set(Contact::class, $phone->getContact());

            $page->setParams(
                array_merge(
                    $page->getParams(),
                    [
                        'id' => $phone->getId(),
                    ]
                )
            );
            $label = sprintf(
                $this->translate("txt-%s-phone-of-%s"),
                $phone->getType(),
                $phone->getContact()->getDisplayName()
            );
        } else {
            $label = $this->translate('txt-nav-phone');
        }
        $page->set('label', $label);
    }
}
