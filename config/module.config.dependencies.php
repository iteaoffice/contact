<?php

/**
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Contact;

use Admin\Service\AdminService;
use Contact\Controller\Plugin;
use Contact\Form\View\Helper\ContactFormElement;
use Contact\Form\View\Helper\SelectionFormElement;
use Contact\Service\AddressService;
use Contact\Service\ContactService;
use Contact\Service\SelectionContactService;
use Contact\Service\SelectionService;
use Doctrine\ORM\EntityManager;
use ErrorHeroModule\Handler\Logging;
use General\Service\CountryService;
use General\Service\EmailService;
use General\Service\GeneralService;
use Laminas\Authentication\AuthenticationService;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Organisation\Service\OrganisationService;

return [
    ConfigAbstractFactory::class => [
        Plugin\MergeContact::class                              => [
            EntityManager::class,
            TranslatorInterface::class,
            Logging::class
        ],
        Plugin\ContactActions::class                            => [
            ContactService::class,
            GeneralService::class,
            EmailService::class
        ],
        Plugin\GetFilter::class                                 => [
            'Application'
        ],
        Plugin\HandleImport::class                              => [
            GeneralService::class,
            CountryService::class,
            OrganisationService::class,
            ContactService::class,
            SelectionContactService::class,
            SelectionService::class
        ],
        Plugin\SelectionExport::class                           => [
            ContactService::class,
            SelectionContactService::class,
            AddressService::class,
            TranslatorInterface::class
        ],
        Search\Service\ContactSearchService::class              => [
            'Config'
        ],
        Search\Service\ProfileSearchService::class              => [
            'Config'
        ],
        Form\ContactForm::class                                 => [
            EntityManager::class
        ],
        ContactFormElement::class                               => [
            ContactService::class,
            'ViewHelperManager',
            TranslatorInterface::class
        ],
        SelectionFormElement::class                             => [
            'ViewHelperManager',
            TranslatorInterface::class
        ],
        Provider\Identity\AuthenticationIdentityProvider::class => [
            AuthenticationService::class,
            AdminService::class,
            'BjyAuthorize\Config'
        ]
    ]
];
