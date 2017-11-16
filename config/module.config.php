<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

namespace Contact;

use Contact\Acl;
use Contact\Factory;
use Contact\Form;
use Contact\InputFilter;
use Contact\Navigation;
use Contact\Provider;
use Contact\Search;
use Contact\Service;
use Contact\View;
use Zend\Stdlib;

$config = [
    'controllers'        => [
        'factories' => [
            Controller\AddressManagerController::class   => Controller\Factory\ControllerFactory::class,
            Controller\ConsoleController::class          => Controller\Factory\ControllerFactory::class,
            Controller\ContactAdminController::class     => Controller\Factory\ControllerFactory::class,
            Controller\ContactController::class          => Controller\Factory\ControllerFactory::class,
            Controller\FacebookController::class         => Controller\Factory\ControllerFactory::class,
            Controller\ImageController::class            => Controller\Factory\ControllerFactory::class,
            Controller\FacebookManagerController::class  => Controller\Factory\ControllerFactory::class,
            Controller\NoteManagerController::class      => Controller\Factory\ControllerFactory::class,
            Controller\PhoneManagerController::class     => Controller\Factory\ControllerFactory::class,
            Controller\ProfileController::class          => Controller\Factory\ControllerFactory::class,
            Controller\SelectionManagerController::class => Controller\Factory\ControllerFactory::class,
        ],
    ],
    'controller_plugins' => [
        'aliases'   => [
            'handleImport'     => Controller\Plugin\HandleImport::class,
            'getContactFilter' => Controller\Plugin\GetFilter::class,
            'mergeContact'     => Controller\Plugin\MergeContact::class,
        ],
        'factories' => [
            Controller\Plugin\HandleImport::class => Controller\Factory\PluginFactory::class,
            Controller\Plugin\GetFilter::class    => Controller\Factory\PluginFactory::class,
            Controller\Plugin\MergeContact::class => Controller\Factory\PluginFactory::class,
        ],
    ],
    'view_manager'       => [
        'template_map' => include __DIR__ . '/../template_map.php',
    ],
    'view_helpers'       => [
        'aliases'    => [
            'communityLink'  => View\Helper\CommunityLink::class,
            'contactHandler' => View\Helper\ContactHandler::class,
            'contactLink'    => View\Helper\ContactLink::class,
            'selectionLink'  => View\Helper\SelectionLink::class,
            'facebookLink'   => View\Helper\FacebookLink::class,
            'addressLink'    => View\Helper\AddressLink::class,
            'noteLink'       => View\Helper\NoteLink::class,
            'phoneLink'      => View\Helper\PhoneLink::class,
            'contactPhoto'   => View\Helper\ContactPhoto::class,
        ],
        'invokables' => [
            'contactformelement' => Form\View\Helper\ContactFormElement::class,
        ],
        'factories'  => [
            View\Helper\CommunityLink::class  => View\Factory\ViewHelperFactory::class,
            View\Helper\ContactHandler::class => View\Factory\ViewHelperFactory::class,
            View\Helper\ContactLink::class    => View\Factory\ViewHelperFactory::class,
            View\Helper\SelectionLink::class  => View\Factory\ViewHelperFactory::class,
            View\Helper\FacebookLink::class   => View\Factory\ViewHelperFactory::class,
            View\Helper\AddressLink::class    => View\Factory\ViewHelperFactory::class,
            View\Helper\NoteLink::class       => View\Factory\ViewHelperFactory::class,
            View\Helper\PhoneLink::class      => View\Factory\ViewHelperFactory::class,
            View\Helper\ContactPhoto::class   => View\Factory\ViewHelperFactory::class,
        ],
    ],
    'form_elements'      => [
        'aliases'   => [
            'Contact' => Form\Element\Contact::class,
        ],
        'factories' => [
            Form\Element\Contact::class => \Zend\Form\ElementFactory::class,
        ],
    ],
    'service_manager'    => [
        'factories' => [
            Navigation\Service\ContactNavigationService::class      => Navigation\Factory\ContactNavigationServiceFactory::class,
            Navigation\Invokable\ContactLabel::class                => Navigation\Factory\NavigationInvokableFactory::class,
            Navigation\Invokable\FacebookLabel::class               => Navigation\Factory\NavigationInvokableFactory::class,
            Navigation\Invokable\SelectionLabel::class              => Navigation\Factory\NavigationInvokableFactory::class,
            Navigation\Invokable\AddressLabel::class                => Navigation\Factory\NavigationInvokableFactory::class,
            Navigation\Invokable\NoteLabel::class                   => Navigation\Factory\NavigationInvokableFactory::class,
            Navigation\Invokable\PhoneLabel::class                  => Navigation\Factory\NavigationInvokableFactory::class,
            Provider\Identity\AuthenticationIdentityProvider::class => Factory\AuthenticationIdentityProviderServiceFactory::class,
            Service\SelectionService::class                         => Factory\SelectionServiceFactory::class,
            Service\ContactService::class                           => Factory\ContactServiceFactory::class,
            Service\AddressService::class                           => Factory\AddressServiceFactory::class,
            Service\FormService::class                              => Factory\FormServiceFactory::class,
            Options\ModuleOptions::class                            => Factory\ModuleOptionsFactory::class,
            InputFilter\FacebookFilter::class                       => Factory\InputFilterFactory::class,
            InputFilter\ContactFilter::class                        => Factory\InputFilterFactory::class,
            InputFilter\SelectionFilter::class                      => Factory\InputFilterFactory::class,
            Search\Service\ContactSearchService::class              => Search\Factory\ContactSearchFactory::class,
            Search\Service\ProfileSearchService::class              => Search\Factory\ProfileSearchFactory::class,
            Acl\Assertion\Address::class                            => Acl\Factory\AssertionFactory::class,
            Acl\Assertion\Contact::class                            => Acl\Factory\AssertionFactory::class,
            Acl\Assertion\Facebook::class                           => Acl\Factory\AssertionFactory::class,
            Acl\Assertion\Note::class                               => Acl\Factory\AssertionFactory::class,
            Acl\Assertion\Phone::class                              => Acl\Factory\AssertionFactory::class,
            Acl\Assertion\Selection::class                          => Acl\Factory\AssertionFactory::class,
        ],
        'shared'    => [
            Service\ContactService::class => false,
        ],
    ],
    'doctrine'           => [
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

foreach (Stdlib\Glob::glob(__DIR__ . '/module.config.{,*}.php', Stdlib\Glob::GLOB_BRACE) as $file) {
    $config = Stdlib\ArrayUtils::merge($config, include $file);
}

return $config;
