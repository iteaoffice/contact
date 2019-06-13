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
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Mapping\Driver\DriverChain;
use DoctrineExtensions\Query\Mysql\Replace;
use Gedmo\Sluggable\SluggableListener;
use Gedmo\SoftDeleteable\SoftDeleteableListener;
use Gedmo\Timestampable\TimestampableListener;
use Zend\Form\ElementFactory;
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
            Controller\DndController::class              => ConfigAbstractFactory::class,
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
        'aliases'   => [
            'contactLink'        => View\Helper\ContactLink::class,
            'dndLink'            => View\Helper\DndLink::class,
            'profileLink'        => View\Helper\ProfileLink::class,
            'selectionLink'      => View\Helper\SelectionLink::class,
            'facebookLink'       => View\Helper\FacebookLink::class,
            'optInLink'          => View\Helper\OptInLink::class,
            'addressLink'        => View\Helper\AddressLink::class,
            'noteLink'           => View\Helper\NoteLink::class,
            'phoneLink'          => View\Helper\PhoneLink::class,
            'contactPhoto'       => View\Helper\ContactPhoto::class,
            'contactformelement' => Form\View\Helper\ContactFormElement::class,
        ],
        'factories' => [
            View\Helper\ContactLink::class             => View\Factory\ViewHelperFactory::class,
            View\Helper\DndLink::class                 => View\Factory\ViewHelperFactory::class,
            View\Helper\ProfileLink::class             => View\Factory\ViewHelperFactory::class,
            View\Helper\SelectionLink::class           => View\Factory\ViewHelperFactory::class,
            View\Helper\FacebookLink::class            => View\Factory\ViewHelperFactory::class,
            View\Helper\OptInLink::class               => View\Factory\ViewHelperFactory::class,
            View\Helper\AddressLink::class             => View\Factory\ViewHelperFactory::class,
            View\Helper\NoteLink::class                => View\Factory\ViewHelperFactory::class,
            View\Helper\PhoneLink::class               => View\Factory\ViewHelperFactory::class,
            View\Helper\ContactPhoto::class            => View\Factory\ViewHelperFactory::class,
            Form\View\Helper\ContactFormElement::class => ConfigAbstractFactory::class,
        ],
    ],
    'form_elements'      => [
        'aliases'   => [
            'Contact' => Form\Element\Contact::class,
        ],
        'factories' => [
            Form\Element\Contact::class => ElementFactory::class,
        ],
    ],
    'service_manager'    => [
        'factories' => [
            Navigation\Service\ContactNavigationService::class      => Navigation\Factory\ContactNavigationServiceFactory::class,
            Navigation\Invokable\ContactLabel::class                => Factory\InvokableFactory::class,
            Navigation\Invokable\FacebookLabel::class               => Factory\InvokableFactory::class,
            Navigation\Invokable\OptInLabel::class                  => Factory\InvokableFactory::class,
            Navigation\Invokable\SelectionLabel::class              => Factory\InvokableFactory::class,
            Navigation\Invokable\AddressLabel::class                => Factory\InvokableFactory::class,
            Navigation\Invokable\DndLabel::class                    => Factory\InvokableFactory::class,
            Navigation\Invokable\NoteLabel::class                   => Factory\InvokableFactory::class,
            Navigation\Invokable\PhoneLabel::class                  => Factory\InvokableFactory::class,
            Provider\Identity\AuthenticationIdentityProvider::class => Factory\AuthenticationIdentityProviderServiceFactory::class,
            Service\SelectionService::class                         => ConfigAbstractFactory::class,
            Service\ContactService::class                           => ConfigAbstractFactory::class,
            Service\SelectionContactService::class                  => ConfigAbstractFactory::class,
            Service\AddressService::class                           => ConfigAbstractFactory::class,
            Service\FormService::class                              => Factory\FormServiceFactory::class,
            InputFilter\FacebookFilter::class                       => Factory\InputFilterFactory::class,
            InputFilter\DndFilter::class                            => Factory\InputFilterFactory::class,
            InputFilter\ContactFilter::class                        => Factory\InputFilterFactory::class,
            InputFilter\OptInFilter::class                          => Factory\InputFilterFactory::class,
            InputFilter\SelectionFilter::class                      => Factory\InputFilterFactory::class,
            Search\Service\ContactSearchService::class              => ConfigAbstractFactory::class,
            Search\Service\ProfileSearchService::class              => ConfigAbstractFactory::class,
            Acl\Assertion\Address::class                            => Factory\InvokableFactory::class,
            Acl\Assertion\Contact::class                            => Factory\InvokableFactory::class,
            Acl\Assertion\Facebook::class                           => Factory\InvokableFactory::class,
            Acl\Assertion\Note::class                               => Factory\InvokableFactory::class,
            Acl\Assertion\Phone::class                              => Factory\InvokableFactory::class,
            Acl\Assertion\Selection::class                          => Factory\InvokableFactory::class,
        ],
        'shared'    => [
            Service\ContactService::class => false,
        ],
    ],
    'doctrine'           => [
        'driver'        => [
            'contact_annotation_driver' => [
                'class' => AnnotationDriver::class,
                'paths' => [
                    __DIR__ . '/../src/Entity/',
                ],
            ],
            'orm_default'               => [
                'class'   => DriverChain::class,
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => 'contact_annotation_driver',
                ],
            ],
        ],
        'eventmanager'  => [
            'orm_default' => [
                'subscribers' => [
                    TimestampableListener::class,
                    SluggableListener::class,
                    SoftDeleteableListener::class,
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
