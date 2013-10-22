<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Contact
 * @package     Config
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */

namespace Contact;

use Zend\Stdlib\ArrayUtils;


$config = array(
    'controllers'     => array(
        'invokables' => array(
            'contact'         => 'Contact\Controller\ContactController',
            'contact-manager' => 'Contact\Controller\ContactManagerController',
        ),
    ),
    'view_manager'    => array(
        'template_path_stack' => array(
            __DIR__ . '/../view'
        ),
    ),
    'service_manager' => array(
        'factories'  => array(
            'contact-assertion' => 'Contact\Acl\Assertion\Contact',
        ),
        'invokables' => array(
            'contact_contact_service' => 'Contact\Service\ContactService',
            'contact_address_service' => 'Contact\Service\AddressService',
            'contact_form_service'    => 'Contact\Service\FormService',

        )
    ),
    'doctrine'        => array(
        'driver'       => array(
            'contact_annotation_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(
                    __DIR__ . '/../src/Contact/Entity/'
                )
            ),
            'orm_default'               => array(
                'class'   => 'Doctrine\ORM\Mapping\Driver\DriverChain',
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => 'contact_annotation_driver',
                )
            ),
        ),
        'eventmanager' => array(
            'orm_default' => array(
                'subscribers' => array(
                    'Gedmo\Timestampable\TimestampableListener',
                    'Gedmo\Sluggable\SluggableListener',
                )
            ),
        ),
    ),
);

$configFiles = array(
    __DIR__ . '/module.config.routes.php',
    __DIR__ . '/module.config.navigation.php',
    __DIR__ . '/module.config.authorize.php',
);

foreach ($configFiles as $configFile) {
    $config = ArrayUtils::merge($config, include $configFile);
}

return $config;
