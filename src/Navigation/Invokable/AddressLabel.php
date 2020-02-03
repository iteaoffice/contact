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

use General\Navigation\Invokable\AbstractNavigationInvokable;
use Contact\Entity\Address;
use Contact\Entity\Contact;
use Laminas\Navigation\Page\Mvc;

/**
 * Class AddressLabel
 *
 * @package Address\Navigation\Invokable
 */
class AddressLabel extends AbstractNavigationInvokable
{
    /**
     * Parse a Funder Address label
     *
     * @param Mvc $page
     *
     * @return void
     */
    public function __invoke(Mvc $page): void
    {
        if ($this->getEntities()->containsKey(Address::class)) {
            /** @var Address $address */
            $address = $this->getEntities()->get(Address::class);

            $this->getEntities()->set(Contact::class, $address->getContact());

            $page->setParams(
                array_merge(
                    $page->getParams(),
                    [
                        'id' => $address->getId(),
                    ]
                )
            );
            $label = sprintf(
                "%s %s, %s (%s)",
                $address->getAddress(),
                $address->getZipCode(),
                $address->getCity(),
                $address->getCountry()
            );
        } else {
            $label = $this->translate('txt-nav-address');
        }
        $page->set('label', $label);
    }
}
