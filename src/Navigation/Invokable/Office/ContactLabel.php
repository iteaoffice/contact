<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\Navigation\Invokable\Office;

use General\Navigation\Invokable\AbstractNavigationInvokable;
use Contact\Entity\Office\Contact as OfficeContact;
use Contact\Entity\Office\Leave;
use Laminas\Navigation\Page\Mvc;

/**
 * Class ContactLabel
 * @package Contact\Navigation\Invokable\Office
 */
class ContactLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        if ($this->getEntities()->containsKey(OfficeContact::class)) {
            /** @var OfficeContact $contact */
            $officeContact = $this->getEntities()->get(OfficeContact::class);

            $page->setParams(array_merge($page->getParams(), ['id' => $officeContact->getId()]));
            $label = (string) $officeContact->getContact()->getDisplayName();
        } elseif ($this->getEntities()->containsKey(Leave::class)) {
            /** @var Leave $leave */
            $leave = $this->getEntities()->get(Leave::class);

            $page->setParams(array_merge($page->getParams(), ['id' => $leave->getOfficeContact()->getId()]));
            $label = (string) $leave->getOfficeContact()->getContact()->getDisplayName();
        } else {
            $label = $this->translate('txt-nav-contact');
        }
        $page->set('label', $label);
    }
}
