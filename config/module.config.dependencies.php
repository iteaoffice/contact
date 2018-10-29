<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Contact;

use Contact\Controller\Plugin;
use Contact\Service\AddressService;
use Contact\Service\ContactService;
use Contact\Service\SelectionContactService;
use Contact\Service\SelectionService;
use Deeplink\Service\DeeplinkService;
use Doctrine\ORM\EntityManager;
use ErrorHeroModule\Handler\Logging;
use General\Service\CountryService;
use General\Service\EmailService;
use General\Service\GeneralService;
use Organisation\Service\OrganisationService;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [
    ConfigAbstractFactory::class => [
        Plugin\MergeContact::class                 => [
            EntityManager::class,
            TranslatorInterface::class,
            Logging::class
        ],
        Plugin\ContactActions::class               => [
            ContactService::class,
            GeneralService::class,
            DeeplinkService::class,
            EmailService::class
        ],
        Plugin\GetFilter::class                    => [
            'Application'
        ],
        Plugin\HandleImport::class                 => [
            GeneralService::class,
            CountryService::class,
            OrganisationService::class,
            ContactService::class,
            SelectionContactService::class,
            SelectionService::class
        ],
        Plugin\SelectionExport::class              => [
            ContactService::class,
            SelectionContactService::class,
            AddressService::class,
            TranslatorInterface::class
        ],
        Search\Service\ContactSearchService::class => [
            'Config'
        ],
        Search\Service\ProfileSearchService::class => [
            'Config'
        ]
    ]
];