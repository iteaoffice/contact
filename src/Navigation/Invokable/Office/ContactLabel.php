<?php

declare(strict_types=1);

namespace Contact\Navigation\Invokable\Office;

use Admin\Navigation\Invokable\AbstractNavigationInvokable;
use Contact\Entity\Office\Contact as OfficeContact;
use Zend\Navigation\Page\Mvc;

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
        } else {
            $label = $this->translate('txt-nav-contact');
        }
        $page->set('label', $label);
    }
}
