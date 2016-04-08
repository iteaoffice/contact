<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category  Publication
 *
 * @author    Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright Copyright (c) 2004-2016 ITEA Office (http://itea3.org)
 */

namespace Contact\Controller\Factory;

use Admin\Service\AdminService;
use Contact\Controller\ContactAbstractController;
use Contact\Search\Service\ContactSearchService;
use Contact\Service\ContactService;
use Contact\Service\FormService;
use Contact\Service\SelectionService;
use Deeplink\Service\DeeplinkService;
use Doctrine\ORM\EntityManager;
use Event\Service\RegistrationService;
use General\Service\EmailService;
use General\Service\GeneralService;
use Organisation\Service\OrganisationService;
use Program\Service\CallService;
use Project\Service\ProjectService;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ControllerInvokableAbstractFactory
 *
 * @package Contact\Controller\Factory
 */
class ControllerInvokableAbstractFactory implements AbstractFactoryInterface
{
    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param                         $name
     * @param                         $requestedName
     *
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return (class_exists($requestedName)
            && in_array(ContactAbstractController::class, class_parents($requestedName)));
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface|ControllerManager $serviceLocator
     * @param string                                    $name
     * @param string                                    $requestedName
     *
     * @return ContactAbstractController
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        /** @var ContactAbstractController $controller */
        $controller = new $requestedName();
        $controller->setServiceLocator($serviceLocator);

        $serviceManager = $serviceLocator->getServiceLocator();

        /** @var ContactService $contactService */
        $contactService = $serviceManager->get(ContactService::class);
        $controller->setContactService($contactService);

        /** @var FormService $formService */
        $formService = $serviceManager->get(FormService::class);
        $controller->setFormService($formService);

        /** @var SelectionService $selectionService */
        $selectionService = $serviceManager->get(SelectionService::class);
        $controller->setSelectionService($selectionService);

        /** @var EntityManager $entityManager */
        $entityManager = $serviceManager->get(EntityManager::class);
        $controller->setEntityManager($entityManager);

        /** @var AdminService $adminService */
        $adminService = $serviceManager->get(AdminService::class);
        $controller->setAdminService($adminService);

        /** @var DeeplinkService $deeplinkService */
        $deeplinkService = $serviceManager->get(DeeplinkService::class);
        $controller->setDeeplinkService($deeplinkService);

        /** @var GeneralService $generalService */
        $generalService = $serviceManager->get(GeneralService::class);
        $controller->setGeneralService($generalService);

        /** @var ContactSearchService $contactSearchService */
        $contactSearchService = $serviceManager->get(ContactSearchService::class);
        $controller->setContactSearchService($contactSearchService);

        /** @var EmailService $emailService */
        $emailService = $serviceManager->get(EmailService::class);
        $controller->setEmailService($emailService);

        /** @var ProjectService $projectService */
        $projectService = $serviceManager->get(ProjectService::class);
        $controller->setProjectService($projectService);

        /** @var OrganisationService $organisationService */
        $organisationService = $serviceManager->get(OrganisationService::class);
        $controller->setOrganisationService($organisationService);

        /** @var CallService $callService */
        $callService = $serviceManager->get(CallService::class);
        $controller->setCallService($callService);

        /** @var RegistrationService $registrationService */
        $registrationService = $serviceManager->get(RegistrationService::class);
        $controller->setRegistrationService($registrationService);

        return $controller;
    }
}
