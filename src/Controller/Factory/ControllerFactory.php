<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * PHP Version 5
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2016 ITEA Office
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/contact for the canonical source repository
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
use Interop\Container\ContainerInterface;
use Organisation\Service\OrganisationService;
use Program\Options\ModuleOptions;
use Program\Service\CallService;
use Project\Service\IdeaService;
use Project\Service\ProjectService;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\View\HelperPluginManager;

/**
 * Class ControllerFactory
 *
 * @package Contact\Controller\Factory
 */
final class ControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface|ControllerManager $container
     * @param string                               $requestedName
     * @param array|null                           $options
     *
     * @return ContactAbstractController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null
    ): ContactAbstractController {
        /** @var ContactAbstractController $controller */
        $controller = new $requestedName($options);

        /** @var ContactService $contactService */
        $contactService = $container->get(ContactService::class);
        $controller->setContactService($contactService);

        /** @var FormService $formService */
        $formService = $container->get(FormService::class);
        $controller->setFormService($formService);

        /** @var SelectionService $selectionService */
        $selectionService = $container->get(SelectionService::class);
        $controller->setSelectionService($selectionService);

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $controller->setEntityManager($entityManager);

        /** @var AdminService $adminService */
        $adminService = $container->get(AdminService::class);
        $controller->setAdminService($adminService);

        /** @var DeeplinkService $deeplinkService */
        $deeplinkService = $container->get(DeeplinkService::class);
        $controller->setDeeplinkService($deeplinkService);

        /** @var GeneralService $generalService */
        $generalService = $container->get(GeneralService::class);
        $controller->setGeneralService($generalService);

        /** @var ContactSearchService $contactSearchService */
        $contactSearchService = $container->get(ContactSearchService::class);
        $controller->setContactSearchService($contactSearchService);

        /** @var EmailService $emailService */
        $emailService = $container->get(EmailService::class);
        $controller->setEmailService($emailService);

        /** @var ProjectService $projectService */
        $projectService = $container->get(ProjectService::class);
        $controller->setProjectService($projectService);

        /** @var OrganisationService $organisationService */
        $organisationService = $container->get(OrganisationService::class);
        $controller->setOrganisationService($organisationService);

        /** @var CallService $callService */
        $callService = $container->get(CallService::class);
        $controller->setCallService($callService);

        /** @var IdeaService $ideaService */
        $ideaService = $container->get(IdeaService::class);
        $controller->setIdeaService($ideaService);

        /** @var RegistrationService $registrationService */
        $registrationService = $container->get(RegistrationService::class);
        $controller->setRegistrationService($registrationService);

        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $container->get(ModuleOptions::class);
        $controller->setProgramModuleOptions($moduleOptions);

        /** @var HelperPluginManager $viewHelperManager */
        $viewHelperManager = $container->get('ViewHelperManager');
        $controller->setViewHelperManager($viewHelperManager);

        return $controller;
    }
}
