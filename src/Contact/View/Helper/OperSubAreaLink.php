<?php

/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Contact
 * @package     View
 * @subpackage  Helper
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 * @version     4.0
 */
namespace Contact\View\Helper;

use Zend\View\Helper\AbstractHelper;

use Contact\Entity;

/**
 * Create a link to an operArea
 *
 * @category    Contact
 * @package     View
 * @subpackage  Helper
 */
class OperSubAreaLink extends AbstractHelper
{

    /**
     * @param  Entity\OperSubArea $operArea
     * @param                     $action
     * @param                     $show
     * @return null|string
     * @throws \Exception
     */
    public function __invoke(Entity\OperSubArea $operSubArea = null, $action, $show)
    {
        $translate = $this->view->plugin('translate');
        $url       = $this->view->plugin('url');
        $serverUrl = $this->view->plugin('serverUrl');
        $isAllowed = $this->view->plugin('isAllowed');

        if (!$isAllowed('contact', $action)) {
            if ($action === 'view' && $show === 'name') {
                return $operSubArea;
            }

            return '';
        }

        switch ($action) {
            case 'new':
                $router      = 'zfcadmin/contact-manager/new';
                $text        = sprintf($translate("txt-new-oper-sub-area"));
                $operSubArea = new Entity\OperSubArea();
                break;
            case 'edit':
                $router = 'zfcadmin/contact-manager/edit';
                $text   = sprintf($translate("txt-edit-oper-sub-area-%s"), $operSubArea);
                break;
            case 'view':
                $router = 'contact/oper-sub-area';
                $text   = sprintf($translate("txt-view-oper-sub-area-%s"), $operSubArea);
                break;
            default:
                throw new \Exception(sprintf("%s is an incorrect action for %s", $action, __CLASS__));
        }

        if (is_null($operSubArea)) {
            throw new \RuntimeException(sprintf(
                "Area needs to be an instance of %s, %s given in %s",
                "Contact\Entity\OperSubArea",
                get_class($operSubArea),
                __CLASS__
            ));
        }

        $params = array(
            'id'     => $operSubArea->getId(),
            'entity' => 'operSubArea'
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
                $linkContent[] = $operSubArea->getName();
                break;
            default:
                $linkContent[] = $operSubArea;
                break;
        }

        $uri = '<a href="%s" title="%s" class="%s">%s</a>';

        return sprintf($uri, $serverUrl->__invoke() . $url($router, $params), $text, implode($classes), implode($linkContent));

    }
}
