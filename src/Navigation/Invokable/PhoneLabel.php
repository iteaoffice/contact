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
use Contact\Entity\Contact;
use Contact\Entity\Phone;
use Zend\Navigation\Page\Mvc;

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
    public function __invoke(Mvc $page)
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
