<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     View
 * @subpackage  Helper
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Contact\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Contact\Entity\Community;

/**
 * Create a link to an area2
 *
 * @category    Contact
 * @package     View
 * @subpackage  Helper
 */
class CommunityLink extends AbstractHelper
{
    /**
     * @param Community $community
     *
     * @return string
     * @throws \Exception
     */
    public function __invoke(Community $community)
    {
        $isAllowed = $this->view->plugin('isAllowed');
        $translate = $this->view->plugin('translate');
        $serverUrl = $this->view->plugin('serverUrl');
        $image     = $this->view->plugin('image');
        $url       = $this->view->plugin('url');

        $uri = '<a href="%s" title="%s" class="%s">%s</a>';
        $img = '<img src="%s">';

        $classes = array();

        $link = preg_replace(
            '/^([^\~]+)(\~(.*))?$/',
            '${1}' . $community->getCommunity() . '$3',
            $community->getType()->getLink()
        );

        return sprintf(
            $uri,
            $link,
            sprintf($translate("txt-go-to-%s-profile"), $community->getType()->getType()),
            implode($classes),
            sprintf($img, $url('assets/style-image', array('source' => $community->getType()->getImage())))
        );
    }
}
