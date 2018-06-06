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


use Contact\Service\SelectionContactService;
use Doctrine\ORM\EntityManager;
use General\Service\GeneralService;
use Organisation\Service\OrganisationService;
use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [
    ConfigAbstractFactory::class => [
        Service\AddressService::class          => [
            EntityManager::class
        ],
        Service\ContactService::class          => [
            EntityManager::class,
            Service\AddressService::class,
            SelectionContactService::class,
            Search\Service\ContactSearchService::class,
            Search\Service\ProfileSearchService::class,
            OrganisationService::class,
            GeneralService::class,
            'ViewHelperManager',
            'zfcuser_module_options'
        ],
        Service\SelectionContactService::class => [
            EntityManager::class
        ],
        Service\SelectionService::class        => [
            EntityManager::class,
            Service\ContactService::class,
            Service\SelectionContactService::class
        ],

    ]
];