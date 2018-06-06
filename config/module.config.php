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
use DoctrineExtensions\Query\Mysql\Replace;
use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Zend\Stdlib;

$config = [
    'controllers'        => [
        'factories' => [
            Controller\AddressManagerController::class   => ConfigAbstractFactory::class,
            Controller\ConsoleController::class          => ConfigAbstractFactory::class,
            Controller\ContactAdminController::class     => ConfigAbstractFactory::class,
            Controller\ContactController::class          => ConfigAbstractFactory::class,
            Controller\FacebookController::class         => ConfigAbstractFactory::class,
            Controller\ImageController::class            => ConfigAbstractFactory::class,
            Controller\FacebookManagerController::class  => ConfigAbstractFactory::class,
            Controller\NoteManagerController::class      => ConfigAbstractFactory::class,
            Controller\PhoneManagerController::class     => ConfigAbstractFactory::class,
            Controller\ProfileController::class          => ConfigAbstractFactory::class,
            Controller\OptInManagerController::class     => ConfigAbstractFactory::class,
            Controller\SelectionManagerController::class => ConfigAbstractFactory::class,
        ],
    ],
    'controller_plugins' => [
        'aliases'   => [
            'handleImport'     => Controller\Plugin\HandleImport::class,
            'selectionExport'  => Controller\Plugin\SelectionExport::class,
            'getContactFilter' => Controller\Plugin\GetFilter::class,
            'mergeContact'     => Controller\Plugin\MergeContact::class,
            'contactActions'   => Controller\Plugin\ContactActions::class
        ],
        'factories' => [
            Controller\Plugin\HandleImport::class    => ConfigAbstractFactory::class,
            Controller\Plugin\SelectionExport::class => ConfigAbstractFactory::class,
            Controller\Plugin\GetFilter::class       => ConfigAbstractFactory::class,
            Controller\Plugin\MergeContact::class    => ConfigAbstractFactory::class,
            Controller\Plugin\ContactActions::class  => ConfigAbstractFactory::class,
        ],
    ],
    'view_manager'       => [
        'template_map' => include __DIR__ . '/../template_map.php',
    ],
    'view_helpers'       => [
        'aliases'    => [
            'communityLink' => View\Helper\CommunityLink::class,
            'contactLink'   => View\Helper\ContactLink::class,
            'profileLink'   => View\Helper\ProfileLink::class,
            'selectionLink' => View\Helper\SelectionLink::class,
            'facebookLink'  => View\Helper\FacebookLink::class,
            'optInLink'     => View\Helper\OptInLink::class,
            'addressLink'   => View\Helper\AddressLink::class,
            'noteLink'      => View\Helper\NoteLink::class,
            'phoneLink'     => View\Helper\PhoneLink::class,
            'contactPhoto'  => View\Helper\ContactPhoto::class,
        ],
        'invokables' => [
            'contactformelement' => Form\View\Helper\ContactFormElement::class,
        ],
        'factories'  => [
            View\Helper\CommunityLink::class   => View\Factory\ViewHelperFactory::class,
            View\Helper\ContactLink::class     => View\Factory\ViewHelperFactory::class,
            View\Helper\ProfileLink::class     => View\Factory\ViewHelperFactory::class,
            View\Helper\SelectionLink::class   => View\Factory\ViewHelperFactory::class,
            View\Helper\FacebookLink::class    => View\Factory\ViewHelperFactory::class,
            View\Helper\OptInLink::class       => View\Factory\ViewHelperFactory::class,
            View\Helper\AddressLink::class     => View\Factory\ViewHelperFactory::class,
            View\Helper\NoteLink::class        => View\Factory\ViewHelperFactory::class,
            View\Helper\PhoneLink::class       => View\Factory\ViewHelperFactory::class,
            View\Helper\ContactPhoto::class    => View\Factory\ViewHelperFactory::class,
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
            Navigation\Invokable\OptInLabel::class                  => Navigation\Factory\NavigationInvokableFactory::class,
            Navigation\Invokable\SelectionLabel::class              => Navigation\Factory\NavigationInvokableFactory::class,
            Navigation\Invokable\AddressLabel::class                => Navigation\Factory\NavigationInvokableFactory::class,
            Navigation\Invokable\NoteLabel::class                   => Navigation\Factory\NavigationInvokableFactory::class,
            Navigation\Invokable\PhoneLabel::class                  => Navigation\Factory\NavigationInvokableFactory::class,
            Provider\Identity\AuthenticationIdentityProvider::class => Factory\AuthenticationIdentityProviderServiceFactory::class,
            Service\SelectionService::class                         => ConfigAbstractFactory::class,
            Service\ContactService::class                           => ConfigAbstractFactory::class,
            Service\SelectionContactService::class                  => ConfigAbstractFactory::class,
            Service\AddressService::class                           => ConfigAbstractFactory::class,
            Service\FormService::class                              => Factory\FormServiceFactory::class,
            InputFilter\FacebookFilter::class                       => Factory\InputFilterFactory::class,
            InputFilter\ContactFilter::class                        => Factory\InputFilterFactory::class,
            InputFilter\OptInFilter::class                          => Factory\InputFilterFactory::class,
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
        'driver'        => [
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
        'eventmanager'  => [
            'orm_default' => [
                'subscribers' => [
                    'Gedmo\Timestampable\TimestampableListener',
                    'Gedmo\Sluggable\SluggableListener',
                    'Gedmo\SoftDeleteable\SoftDeleteableListener',
                ],
            ],
        ],
        'configuration' => [
            'orm_default' => [
                'string_functions' => [
                    'replace' => Replace::class
                ]
            ]
        ],
    ],
];

foreach (Stdlib\Glob::glob(__DIR__ . '/module.config.{,*}.php', Stdlib\Glob::GLOB_BRACE) as $file) {
    $config = Stdlib\ArrayUtils::merge($config, include $file);
}

return $config;
