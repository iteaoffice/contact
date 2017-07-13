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
use Zend\Navigation\Page\Mvc;

/**
 * Class ContactLabel
 *
 * @package Contact\Navigation\Invokable
 */
class ContactLabel extends AbstractNavigationInvokable
{
    /**
     * Parse a Funder Contact label
     *
     * @param Mvc $page
     *
     * @return void
     */
    public function __invoke(Mvc $page): void
    {
        if ($this->getEntities()->containsKey(Contact::class)) {
            /** @var Contact $contact */
            $contact = $this->getEntities()->get(Contact::class);

            $page->setParams(
                array_merge(
                    $page->getParams(), [
                        'id' => $contact->getId(),
                    ]
                )
            );
            $label = (string)$contact->getDisplayName();
        } else {
            $label = $this->translate('txt-nav-contact');
        }
        $page->set('label', $label);
    }
}
