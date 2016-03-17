<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * PHP Version 5
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2016 ITEA Office
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */
namespace Contact\Navigation\Invokable;

use Admin\Navigation\Invokable\AbstractNavigationInvokable;
use Contact\Entity\Selection;
use Zend\Navigation\Page\Mvc;

/**
 * Class FunderLabel
 *
 * @package Funder\Navigation\Invokable
 */
class SelectionLabel extends AbstractNavigationInvokable
{
    /**
     * @param Mvc $page
     *
     * @return void
     */
    public function __invoke(Mvc $page)
    {
        if ($this->getEntities()->containsKey(Selection::class)) {
            /** @var Selection $selection */
            $selection = $this->getEntities()->get(Selection::class);

            $page->setParams(array_merge($page->getParams(), [
                'id' => $selection->getId(),
            ]));
            $label = (string)$selection->getSelection();
        } else {
            $label = $this->translate('txt-nav-selection');
        }
        $page->set('label', $label);
    }
}