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
use Contact\Entity\Facebook;
use Zend\Navigation\Page\Mvc;

/**
 * Class FacebookLabel
 *
 * @package Facebook\Navigation\Invokable
 */
class FacebookLabel extends AbstractNavigationInvokable
{
    /**
     * Parse a Funder Facebook label
     *
     * @param Mvc $page
     *
     * @return void
     */
    public function __invoke(Mvc $page)
    {
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
        } else {
            $label = $this->translate('txt-nav-facebook');
        }
        $page->set('label', $label);
    }
}
