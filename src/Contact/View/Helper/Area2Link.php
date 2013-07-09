<?php

/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Contact
 * @package     View
 * @subpackage  Helper
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
namespace Contact\View\Helper;

use Zend\View\Helper\AbstractHelper;

use Contact\Entity;

/**
 * Create a link to an area2
 *
 * @category    Contact
 * @package     View
 * @subpackage  Helper
 */
class Area2Link extends AbstractHelper
{

    /**
     * @param  Entity\Area2 $area2
     * @param               $action
     * @param               $show
     *
     * @return null|string
     * @throws \Exception
     */
    public function __invoke(Entity\Area2 $area2 = null, $action, $show)
    {
        $isAllowed = $this->view->plugin('isAllowed');
        $translate = $this->view->plugin('translate');
        $serverUrl = $this->view->plugin('serverUrl');
        $url       = $this->view->plugin('url');

        if (!$isAllowed('contact', $action)) {
            if ($action === 'view' && $show === 'name') {
                return $area2;
            }

            return '';
        }

        switch ($action) {
            case 'new':
                $router = 'zfcadmin/contact-manager/new';
                $text   = sprintf($translate("txt-new-area2"));
                $area2  = new Entity\Area2();
                break;
            case 'edit':
                $router = 'zfcadmin/contact-manager/edit';
                $text   = sprintf($translate("txt-edit-area2-%s"), $area2);
                break;
            case 'view':
                $router = 'contact/area2';
                $text   = sprintf($translate("txt-view-area2-%s"), $area2);
                break;
            default:
                throw new \Exception(sprintf("%s is an incorrect action for %s", $action, __CLASS__));
        }

        if (is_null($area2)) {
            throw new \RuntimeException(sprintf(
                "Area needs to be an instance of %s, %s given in %s",
                "Contact\Entity\Area2",
                get_class($area2),
                __CLASS__
            ));
        }

        $params = array(
            'id'     => $area2->getId(),
            'entity' => 'area2'
        );

        $classes     = array();
        $linkContent = array();

        switch ($show) {
            case 'icon':
                if ($action === 'edit') {
                    $linkContent[] = '<i class="icon-pencil"></i>';
                } elseif ($action === 'delete') {
                    $linkContent[] = '<i class="icon-remove"></i>';
                } else {
                    $linkContent[] = '<i class="icon-info-sign"></i>';
                }
                break;
            case 'button':
                $linkContent[] = '<i class="icon-pencil icon-white"></i> ' . $text;
                $classes[]     = "btn btn-primary";
                break;
            case 'name':
                $linkContent[] = $area2->getName();
                break;
            default:
                $linkContent[] = $area2;
                break;
        }

        $uri = '<a href="%s" title="%s" class="%s">%s</a>';

        return sprintf($uri, $serverUrl->__invoke() . $url($router, $params), $text, implode($classes), implode($linkContent));

    }
}
