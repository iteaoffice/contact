<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Contact
 * @package     Config
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
use Contact\Form;
use Contact\Options;

return array(
    'factories' => array(
        'contact_community_options' => function ($sm) {
                $config = $sm->get('Config');

                return new Options\ModuleOptions(isset($config['community']) ? $config['community'] : array());
            },
    ),
);
