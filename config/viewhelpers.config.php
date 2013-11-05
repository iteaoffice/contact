<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Application
 * @package     Config
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
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
