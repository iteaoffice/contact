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
use Laminas\Navigation\Page\Mvc;

/**
 * Class ContactLabel
 *
 * @package Contact\Navigation\Invokable
 */
final class ContactLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translate('txt-nav-contact');

        if ($this->getEntities()->containsKey(Contact::class)) {
            /** @var Contact $contact */
            $contact = $this->getEntities()->get(Contact::class);

            $page->setParams(
                array_merge(
                    $page->getParams(),
                    [
                        'id' => $contact->getId(),
                    ]
                )
            );
            $label = $page->getLabel();

            if (null === $label) {
                $label = (string)$contact->getDisplayName();
            }
        }
        $page->set('label', $label);
    }
}
