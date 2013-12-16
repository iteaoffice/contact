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

use Contact\Entity\Community;
use Content\Entity\Image;

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
     * @return null|string
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


        $link = preg_replace('/^([^\~]+)(\~(.*))?$/', '${1}' . $community->getCommunity() . '$3',
            $community->getType()->getLink());

        return sprintf(
            $uri,
            $link,
            sprintf($translate("txt-go-to-%s-profile"), $community->getType()->getType()),
            implode($classes),
            sprintf($img, $url('assets/style-image', array('source' => $community->getType()->getImage())))
        );
    }
}
