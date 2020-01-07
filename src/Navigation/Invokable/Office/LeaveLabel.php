<?php

declare(strict_types=1);

namespace Contact\Navigation\Invokable\Office;

use Admin\Navigation\Invokable\AbstractNavigationInvokable;
use Contact\Entity\Office\Leave;
use Laminas\Navigation\Page\Mvc;

/**
 * Class LeaveLabel
 * @package Contact\Navigation\Invokable\Office
 */
class LeaveLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        if ($this->getEntities()->containsKey(Leave::class)) {
            /** @var Leave $leave */
            $leave = $this->getEntities()->get(Leave::class);

            $page->setParams(array_merge($page->getParams(), ['id' => $leave->getId()]));
            $label = (string) $leave->getDescription();
        } else {
            $label = $this->translate('txt-nav-leave');
        }
        $page->set('label', $label);
    }
}
