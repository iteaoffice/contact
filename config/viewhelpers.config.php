<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Application
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */

return array(
    'factories'  => array(
        'contactHandler'      => function ($sm) {
            return new \Contact\View\Helper\ContactHandler($sm);
        },
        'contactServiceProxy' => function ($sm) {
            return new \Contact\View\Helper\ContactServiceProxy($sm);
        }
    ),
    'invokables' => array(
        'contactLink'   => 'Contact\View\Helper\ContactLink',
        'communityLink' => 'Contact\View\Helper\CommunityLink',
        'contactPhoto'  => 'Contact\View\Helper\ContactPhoto',
    )
);
