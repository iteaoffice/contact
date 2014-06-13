<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c] 2004-2014 ITEA Office (http://itea3.org]
 */

namespace Contact;

use Contact\Controller\ControllerInitializer;
use Contact\Service\ServiceInitializer;
use Contact\View\Helper\ViewHelperInitializer;
use Zend\Stdlib\ArrayUtils;

$config = [
    'controllers'     => [
        'initializers' => [
            ControllerInitializer::class
        ],
        'invokables'   => [
            'contact-index'   => 'Contact\Controller\ContactController',
            'contact-manager' => 'Contact\Controller\ContactManagerController',
        ],
    ],
    'view_manager'    => [
        'template_map' => include __DIR__ . '/../template_map.php',
    ],
    'view_helpers'    => [
        'initializers' => [
            ViewHelperInitializer::class
        ],
        'invokables'   => [
            'contactServiceProxy' => 'Contact\View\Helper\ContactServiceProxy',
            'contactLink'         => 'Contact\View\Helper\ContactLink',
            'communityLink'       => 'Contact\View\Helper\CommunityLink',
            'contactPhoto'        => 'Contact\View\Helper\ContactPhoto',
        ]
    ],
    'service_manager' => [
        'initializers' => [
            ServiceInitializer::class
        ],
        'factories'    => [
            'contact_contact_navigation_service' => 'Contact\Navigation\Factory\ContactNavigationServiceFactory',
            'contact_module_config'              => 'Contact\Factory\ConfigServiceFactory',
            'contact_cache'                      => 'Contact\Factory\CacheFactory',
            'Contact\Provider\Identity\AuthenticationIdentityProvider'
                                                 => 'Contact\Factory\AuthenticationIdentityProviderServiceFactory',
        ],
        'invokables'   => [
            'contact_impersonate_form'     => 'Contact\Form\Impersonate',
            'contact_contact_service'      => 'Contact\Service\ContactService',
            'contact_address_service'      => 'Contact\Service\AddressService',
            'contact_form_service'         => 'Contact\Service\FormService',
            'contact_contact_form_filter'  => 'Contact\Form\FilterContact',
            'contact_password_form'        => 'Contact\Form\Password',
            'contact_password_form_filter' => 'Contact\Form\PasswordFilter',
        ]
    ],
    'doctrine'        => [
        'driver'       => [
            'contact_annotation_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => [
                    __DIR__ . '/../src/Contact/Entity/'
                ]
            ],
            'orm_default'               => [
                'class'   => 'Doctrine\ORM\Mapping\Driver\DriverChain',
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => 'contact_annotation_driver',
                ]
            ],
        ],
        'eventmanager' => [
            'orm_default' => [
                'subscribers' => [
                    'Gedmo\Timestampable\TimestampableListener',
                    'Gedmo\Sluggable\SluggableListener',
                ]
            ],
        ],
    ],
];

$configFiles = [
    __DIR__ . '/module.config.routes.php',
    __DIR__ . '/module.config.navigation.php',
    __DIR__ . '/module.config.authorize.php',
    __DIR__ . '/module.config.community.php',
    __DIR__ . '/module.config.contact.php',
];

foreach ($configFiles as $configFile) {
    $config = ArrayUtils::merge($config, include $configFile);
}

return $config;
