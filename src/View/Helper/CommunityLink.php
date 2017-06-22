<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\View\Helper;

use Contact\Entity\Community;
use Zend\View\Helper\Url;

/**
 * Create a link to an area2.
 *
 * @category    Contact
 */
class CommunityLink extends AbstractViewHelper
{
    /**
     * @param Community $community
     *
     * @return string
     *
     * @throws \Exception
     */
    public function __invoke(Community $community)
    {
        $uri = '<a href="%s" title="%s" class="%s">%s</a>';
        $img = '<img src="%s">';
        $classes = [];
        $link = preg_replace(
            '/^([^\~]+)(\~(.*))?$/',
            '${1}' . $community->getCommunity() . '$3',
            $community->getType()->getLink()
        );

        /**
         * @var $url Url
         */
        $url = $this->getHelperPluginManager()->get('url');

        return sprintf(
            $uri,
            $link,
            sprintf($this->translate("txt-go-to-%s-profile"), $community->getType()->getType()),
            implode($classes),
            sprintf($img, $url('assets/style-image', ['source' => $community->getType()->getImage()]))
        );
    }
}
