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
 * Create a link to an sub-area
 *
 * @category    Contact
 * @package     View
 * @subpackage  Helper
 */
class SubAreaLink extends AbstractHelper
{

    /**
     * @param  \Contact\Entity\SubArea $subArea
     * @param                          $action
     * @param                          $show
     * @return string
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function __invoke(Entity\SubArea $subArea = null, $action, $show)
    {
        $translate = $this->view->plugin('translate');
        $url       = $this->view->plugin('url');
        $serverUrl = $this->view->plugin('serverUrl');
        $isAllowed = $this->view->plugin('isAllowed');

        if (!$isAllowed('contact', $action)) {
            if ($action === 'view' && $show === 'name') {
                return $subArea;
            }

            return '';
        }

        switch ($action) {
            case 'new':
                $router  = 'zfcadmin/contact-manager/new';
                $text    = sprintf($translate("txt-new-area"));
                $subArea = new Entity\SubArea();
                break;
            case 'edit':
                $router = 'zfcadmin/contact-manager/edit';
                $text   = sprintf($translate("txt-edit-sub-area-%s"), $subArea);
                break;
            case 'view':
                $router = 'contact/sub-area';
                $text   = sprintf($translate("txt-view-sub-area-%s"), $subArea);
                break;
            default:
                throw new \Exception(sprintf("%s is an incorrect action for %s", $action, __CLASS__));
        }

        if (is_null($subArea)) {
            throw new \RuntimeException(sprintf(
                "Area needs to be an instance of %s, %s given in %s",
                "Contact\Entity\SubArea",
                get_class($subArea),
                __CLASS__
            ));
        }

        $params = array(
            'id'     => $subArea->getId(),
            'entity' => 'sub-area'
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
                $linkContent[] = $subArea->getName();
                break;
            default:
                $linkContent[] = $subArea;
                break;
        }

        $uri = '<a href="%s" title="%s" class="%s">%s</a>';

        return sprintf($uri, $serverUrl->__invoke() . $url($router, $params), $text, implode($classes), implode($linkContent));

    }
}
