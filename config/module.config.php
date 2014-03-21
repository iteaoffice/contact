<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */

namespace Contact;

use Zend\Stdlib\ArrayUtils;

$config = array(
    'controllers'     => array(
        'invokables' => array(
            'contact-index'   => 'Contact\Controller\ContactController',
            'contact-manager' => 'Contact\Controller\ContactManagerController',
        ),
    ),
    'view_manager'    => array(
        'template_map' => include __DIR__ . '/../template_map.php',
    ),
    'service_manager' => array(
        'factories'  => array(
            'contact_module_config' => 'Contact\Service\ConfigServiceFactory',
            'contact_cache'         => 'Contact\Service\CacheFactory',
        ),
        'invokables' => array(
            'contact_contact_service'      => 'Contact\Service\ContactService',
            'contact_address_service'      => 'Contact\Service\AddressService',
            'contact_form_service'         => 'Contact\Service\FormService',
            'contact_contact_form_filter'  => 'Contact\Form\FilterContact',
            'contact_password_form'        => 'Contact\Form\Password',
            'contact_password_form_filter' => 'Contact\Form\PasswordFilter',

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
    __DIR__ . '/module.config.community.php',
    __DIR__ . '/module.config.contact.php',
);

foreach ($configFiles as $configFile) {
    $config = ArrayUtils::merge($config, include $configFile);
}

return $config;
