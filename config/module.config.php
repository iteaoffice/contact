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
use Contact\Form\View\Helper\ContactFormElement;
use Contact\Search;
use Contact\Service;
use Contact\View\Helper;
use Zend\Stdlib\ArrayUtils;

$config = [
    'controllers'     => [
        'invokables'         => [
            //            Controller\ConsoleController::class,
            //            Controller\ContactController::class,
            //            Controller\ProfileController::class,
            //            Controller\SelectionManagerController::class,
            //            Controller\FacebookManagerController::class,
            //            Controller\FacebookController::class,
            //            Controller\AddressManagerController::class,
            //            Controller\PhoneManagerController::class,
            //            Controller\NoteManagerController::class,
            //            Controller\ContactAdminController::class,
        ],
        'abstract_factories' => [
            Controller\Factory\ControllerInvokableAbstractFactory::class,
        ],
    ],
    'view_manager'    => [
        'template_map' => include __DIR__ . '/../template_map.php',
    ],
    'view_helpers'    => [
        'invokables' => [
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
        ],
    ],
    'service_manager' => [
        'factories'          => [
            'contact_contact_navigation_service'                       => 'Contact\Navigation\Factory\ContactNavigationServiceFactory',
            'Contact\Provider\Identity\AuthenticationIdentityProvider' => 'Contact\Factory\AuthenticationIdentityProviderServiceFactory',
            Service\SelectionService::class                            => Factory\SelectionServiceFactory::class,
            Service\ContactService::class                              => Factory\ContactServiceFactory::class,
            Service\AddressService::class                              => Factory\AddressServiceFactory::class,
            Service\FormService::class                                 => Factory\FormServiceFactory::class,
            Options\ModuleOptions::class                               => Factory\ModuleOptionsFactory::class,
            Search\Service\ContactSearchService::class                 => Search\Factory\ContactSearchFactory::class,
            //            Acl\Assertion\Contact::class,
            //            Acl\Assertion\Facebook::class,
            //            Acl\Assertion\Address::class,
            //            Acl\Assertion\Note::class,
            //            Acl\Assertion\Phone::class,
            //            Acl\Assertion\Selection::class,
        ],
        'abstract_factories' => [
            Acl\Factory\AssertionInvokableAbstractFactory::class,
        ],
        'shared'             => [
            // Usually, you'll only indicate services that should **NOT** be
            // shared -- i.e., ones where you want a different instance
            // every time.
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
                    __DIR__ . '/../src/Contact/Entity/',
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
