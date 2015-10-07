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

use Contact\Acl\Assertion;
use Contact\Form\View\Helper\ContactFormElement;
use Contact\Service;
use Contact\View\Helper;
use Zend\Stdlib\ArrayUtils;

$config = [
    'controllers'     => [
        'initializers' => [
            Controller\ControllerInitializer::class
        ],
        'invokables'   => [
            Controller\ConsoleController::class          => Controller\ConsoleController::class,
            Controller\ContactController::class          => Controller\ContactController::class,
            Controller\ProfileController::class          => Controller\ProfileController::class,
            Controller\SelectionManagerController::class => Controller\SelectionManagerController::class,
            Controller\FacebookManagerController::class  => Controller\FacebookManagerController::class,
            Controller\FacebookController::class         => Controller\FacebookController::class,
            Controller\AddressManagerController::class   => Controller\AddressManagerController::class,
            Controller\PhoneManagerController::class     => Controller\PhoneManagerController::class,
            Controller\NoteManagerController::class      => Controller\NoteManagerController::class,
            Controller\ContactAdminController::class     => Controller\ContactAdminController::class,
        ],
    ],
    'view_manager'    => [
        'template_map' => include __DIR__ . '/../template_map.php',
    ],
    'view_helpers'    => [
        'initializers' => [
            Helper\ViewHelperInitializer::class
        ],
        'invokables'   => [
            'contactformelement'     => ContactFormElement::class,
            'communityLink'          => Helper\CommunityLink::class,
            'createContactFromArray' => Helper\CreateContactFromArray::class,
            'createPhotoFromArray'   => Helper\CreatePhotoFromArray::class,
            'contactHandler'         => Helper\ContactHandler::class,
            'contactServiceProxy'    => Helper\ContactServiceProxy::class,
            'contactLink'            => Helper\ContactLink::class,
            'selectionLink'          => Helper\SelectionLink::class,
            'facebookLink'           => Helper\FacebookLink::class,
            'addressLink'            => Helper\AddressLink::class,
            'noteLink'               => Helper\NoteLink::class,
            'phoneLink'              => Helper\PhoneLink::class,
            'contactPhoto'           => Helper\ContactPhoto::class,
        ]
    ],
    'service_manager' => [
        'initializers' => [
            Service\ServiceInitializer::class
        ],
        'factories'    => [
            'contact_contact_navigation_service'                       => 'Contact\Navigation\Factory\ContactNavigationServiceFactory',
            'contact_module_config'                                    => 'Contact\Factory\ConfigServiceFactory',
            'contact_cache'                                            => 'Contact\Factory\CacheFactory',
            'Contact\Provider\Identity\AuthenticationIdentityProvider' => 'Contact\Factory\AuthenticationIdentityProviderServiceFactory',
        ],
        'invokables'   => [
            Assertion\Contact::class         => Assertion\Contact::class,
            Assertion\Facebook::class        => Assertion\Facebook::class,
            Assertion\Address::class         => Assertion\Address::class,
            Assertion\Note::class            => Assertion\Note::class,
            Assertion\Phone::class           => Assertion\Phone::class,
            Assertion\Selection::class       => Assertion\Selection::class,
            Service\SelectionService::class  => Service\SelectionService::class,
            Service\StatisticsService::class => Service\StatisticsService::class,
            Service\ContactService::class    => Service\ContactService::class,
            Service\AddressService::class    => Service\AddressService::class,
            'contact_contact_service'        => 'Contact\Service\ContactService',
            'contact_address_service'        => 'Contact\Service\AddressService',
            'contact_form_service'           => 'Contact\Service\FormService',
            'contact_contact_form_filter'    => 'Contact\Form\FilterContact',
            'contact_facebook_form_filter'   => 'Contact\Form\FilterCreateObject',
            'contact_address_form_filter'    => 'Contact\Form\FilterCreateObject',
            'contact_note_form_filter'       => 'Contact\Form\FilterCreateObject',
            'contact_phone_form_filter'      => 'Contact\Form\FilterCreateObject',
            'contact_selection_form_filter'  => 'Contact\Form\FilterCreateObject',
            'contact_password_form'          => 'Contact\Form\Password',
            'contact_password_form_filter'   => 'Contact\Form\PasswordFilter',
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
    __DIR__ . '/module.config.routes.console.php',
    __DIR__ . '/module.config.routes.admin.php',
    __DIR__ . '/module.config.navigation.php',
    __DIR__ . '/module.config.authorize.php',
    __DIR__ . '/module.config.community.php',
    __DIR__ . '/module.config.contact.php',
];
foreach ($configFiles as $configFile) {
    $config = ArrayUtils::merge($config, include $configFile);
}

return $config;
