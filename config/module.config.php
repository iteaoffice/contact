<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c] 2004-2015 ITEA Office (https://itea3.org]
 */
namespace Contact;

use Contact\Acl;
use Contact\Factory;
use Contact\Form;
use Contact\Navigation;
use Contact\Search;
use Contact\Service;
use Contact\View;
use Zend\Stdlib\ArrayUtils;

$config = [
    'controllers'     => [
        'abstract_factories' => [
            Controller\Factory\ControllerInvokableAbstractFactory::class,
        ],
    ],
    'view_manager'    => [
        'template_map' => include __DIR__ . '/../template_map.php',
    ],
    'view_helpers'    => [
        'aliases'    => [
            'communityLink'          => View\Helper\CommunityLink::class,
            'createContactFromArray' => View\Helper\CreateContactFromArray::class,
            'createPhotoFromArray'   => View\Helper\CreatePhotoFromArray::class,
            'contactHandler'         => View\Helper\ContactHandler::class,
            'contactServiceProxy'    => View\Helper\ContactServiceProxy::class,
            'contactLink'            => View\Helper\ContactLink::class,
            'selectionLink'          => View\Helper\SelectionLink::class,
            'facebookLink'           => View\Helper\FacebookLink::class,
            'addressLink'            => View\Helper\AddressLink::class,
            'noteLink'               => View\Helper\NoteLink::class,
            'phoneLink'              => View\Helper\PhoneLink::class,
            'contactPhoto'           => View\Helper\ContactPhoto::class,
        ],
        'invokables' => [
            'contactformelement' => Form\View\Helper\ContactFormElement::class,
        ],
        'factories'  => [
            View\Helper\CommunityLink::class          => View\Factory\LinkInvokableFactory::class,
            View\Helper\CreateContactFromArray::class => View\Factory\LinkInvokableFactory::class,
            View\Helper\CreatePhotoFromArray::class   => View\Factory\LinkInvokableFactory::class,
            View\Helper\ContactHandler::class         => View\Factory\LinkInvokableFactory::class,
            View\Helper\ContactServiceProxy::class    => View\Factory\LinkInvokableFactory::class,
            View\Helper\ContactLink::class            => View\Factory\LinkInvokableFactory::class,
            View\Helper\SelectionLink::class          => View\Factory\LinkInvokableFactory::class,
            View\Helper\FacebookLink::class           => View\Factory\LinkInvokableFactory::class,
            View\Helper\AddressLink::class            => View\Factory\LinkInvokableFactory::class,
            View\Helper\NoteLink::class               => View\Factory\LinkInvokableFactory::class,
            View\Helper\PhoneLink::class              => View\Factory\LinkInvokableFactory::class,
            View\Helper\ContactPhoto::class           => View\Factory\LinkInvokableFactory::class,
        ]
    ],
    'form_elements'   => [
        'aliases'   => [
            'Contact' => Form\Element\Contact::class,
        ],
        'factories' => [
            Form\Element\Contact::class => \Zend\Form\ElementFactory::class,
        ],
    ],
    'service_manager' => [
        'factories'          => [
            Navigation\Service\ContactNavigationService::class         => Navigation\Factory\ContactNavigationServiceFactory::class,
            'Contact\Provider\Identity\AuthenticationIdentityProvider' => Factory\AuthenticationIdentityProviderServiceFactory::class,
            Service\SelectionService::class                            => Factory\SelectionServiceFactory::class,
            Service\ContactService::class                              => Factory\ContactServiceFactory::class,
            Service\AddressService::class                              => Factory\AddressServiceFactory::class,
            Service\FormService::class                                 => Factory\FormServiceFactory::class,
            Options\ModuleOptions::class                               => Factory\ModuleOptionsFactory::class,
            Search\Service\ContactSearchService::class                 => Search\Factory\ContactSearchFactory::class,
        ],
        'abstract_factories' => [
            Acl\Factory\AssertionInvokableAbstractFactory::class,
        ],
        'shared'             => [
            Service\ContactService::class => false,
        ],
        'invokables'         => [
            'contact_facebook_form_filter'  => 'Contact\Form\FilterCreateObject',
            'contact_address_form_filter'   => 'Contact\Form\FilterCreateObject',
            'contact_note_form_filter'      => 'Contact\Form\FilterCreateObject',
            'contact_phone_form_filter'     => 'Contact\Form\FilterCreateObject',
            'contact_selection_form_filter' => 'Contact\Form\FilterCreateObject',
        ],
    ],
    'doctrine'        => [
        'driver'       => [
            'contact_annotation_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => [
                    __DIR__ . '/../src/Entity/',
                ],
            ],
            'orm_default'               => [
                'class'   => 'Doctrine\ORM\Mapping\Driver\DriverChain',
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => 'contact_annotation_driver',
                ],
            ],
        ],
        'eventmanager' => [
            'orm_default' => [
                'subscribers' => [
                    'Gedmo\Timestampable\TimestampableListener',
                    'Gedmo\Sluggable\SluggableListener',
                    'Gedmo\SoftDeleteable\SoftDeleteableListener',
                ],
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
    __DIR__ . '/module.config.search.php',
];
foreach ($configFiles as $configFile) {
    $config = ArrayUtils::merge($config, include $configFile);
}

return $config;
