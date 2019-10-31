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
use Affiliation\Service\AffiliationService;
use Calendar\Service\CalendarService;
use Contact\Search\Service\ContactSearchService;
use Contact\Service\ContactService;
use Contact\Service\FormService;
use Deeplink\Service\DeeplinkService;
use Doctrine\ORM\EntityManager;
use Event\Service\BoothService;
use Event\Service\MeetingService;
use Event\Service\RegistrationService;
use General\Service\EmailService;
use General\Service\GeneralService;
use Organisation\Service\OrganisationService;
use Program\Options\ModuleOptions;
use Program\Service\CallService;
use Program\Service\ProgramService;
use Project\Service\IdeaService;
use Project\Service\ProjectService;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [
    ConfigAbstractFactory::class => [
        Controller\AddressManagerController::class   => [
            Service\ContactService::class,
            Service\FormService::class,
            TranslatorInterface::class
        ],
        Controller\ConsoleController::class          => [
            ContactService::class
        ],
        Controller\ContactAdminController::class     => [
            Service\ContactService::class,
            ContactSearchService::class,
            Service\SelectionService::class,
            OrganisationService::class,
            CallService::class,
            ProjectService::class,
            IdeaService::class,
            AdminService::class,
            RegistrationService::class,
            DeeplinkService::class,
            GeneralService::class,
            AffiliationService::class,
            BoothService::class,
            FormService::class,
            TranslatorInterface::class,
            EntityManager::class
        ],
        Controller\ContactDetailsController::class   => [
            Service\ContactService::class,
            Service\SelectionService::class,
            CallService::class,
            ProjectService::class,
            CalendarService::class,
            AdminService::class,
            RegistrationService::class,
            BoothService::class,
            TranslatorInterface::class,
            EntityManager::class
        ],
        Controller\ContactController::class          => [
            Service\ContactService::class,
            TranslatorInterface::class,
            Search\Service\ProfileSearchService::class
        ],
        Controller\DndController::class              => [
            Service\ContactService::class,
            ProgramService::class,
            Service\FormService::class,
            GeneralService::class,
            TranslatorInterface::class,

        ],
        Controller\FacebookController::class         => [
            'Config',
            Service\ContactService::class,
            EmailService::class,
            TranslatorInterface::class
        ],
        Controller\FacebookManagerController::class  => [
            Service\ContactService::class,
            Service\FormService::class,
            TranslatorInterface::class
        ],
        Controller\OptInManagerController::class     => [
            Service\ContactService::class,
            Service\FormService::class,
            TranslatorInterface::class
        ],
        Controller\ImageController::class            => [
            Service\ContactService::class,
        ],
        Controller\NoteManagerController::class      => [
            Service\ContactService::class,
            Service\FormService::class,
            TranslatorInterface::class
        ],
        Controller\PhoneManagerController::class     => [
            Service\ContactService::class,
            Service\FormService::class,
            TranslatorInterface::class
        ],
        Controller\ProfileController::class          => [
            Service\ContactService::class,
            Service\AddressService::class,
            OrganisationService::class,
            CallService::class,
            ModuleOptions::class,
            GeneralService::class,
            MeetingService::class,
            EntityManager::class,
            TranslatorInterface::class
        ],
        Controller\SelectionManagerController::class => [
            Service\ContactService::class,
            Service\SelectionContactService::class,
            Service\SelectionService::class,
            DeeplinkService::class,
            Service\FormService::class,
            EntityManager::class,
            TranslatorInterface::class
        ],
        Controller\Office\ContactController::class   => [
            Service\Office\ContactService::class,
            Service\FormService::class,
        ],
        Controller\Office\LeaveController::class     => [
            Service\Office\ContactService::class,
            AdminService::class,
            Service\FormService::class,
        ]
    ]
];
