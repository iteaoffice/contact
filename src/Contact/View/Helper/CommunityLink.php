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

use Contact\Entity\Community;

/**
 * Create a link to an area2
 *
 * @category    Contact
 * @package     View
 * @subpackage  Helper
 */
class CommunityLink extends HelperAbstract
{
    /**
     * @param Community $community
     *
     * @return string
     * @throws \Exception
     */
    public function __invoke(Community $community)
    {
        $uri     = '<a href="%s" title="%s" class="%s">%s</a>';
        $img     = '<img src="%s">';
        $classes = [];
        $link    = preg_replace(
            '/^([^\~]+)(\~(.*))?$/',
            '${1}' . $community->getCommunity() . '$3',
            $community->getType()->getLink()
        );

        return sprintf(
            $uri,
            $link,
            sprintf($this->translate("txt-go-to-%s-profile"), $community->getType()->getType()),
            implode($classes),
            sprintf($img, $this->getUrl('assets/style-image', array('source' => $community->getType()->getImage())))
        );
    }
}
