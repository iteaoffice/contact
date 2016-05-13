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
use Organisation\Controller\OrganisationAbstractController;
use Organisation\Service\OrganisationService;
use Program\Service\CallService;
use Project\Service\IdeaService;
use Project\Service\ProjectService;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var ContactAbstractController $controller */
        $controller = new $requestedName($options);

        $serviceManager = $container->getServiceLocator();

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

        /** @var IdeaService $ideaService */
        $ideaService = $serviceManager->get(IdeaService::class);
        $controller->setIdeaService($ideaService);

        /** @var RegistrationService $registrationService */
        $registrationService = $serviceManager->get(RegistrationService::class);
        $controller->setRegistrationService($registrationService);


        return $controller;
    }

    /**
     * @param ServiceLocatorInterface $container
     * @param string                  $canonicalName
     * @param string                  $requestedName
     *
     * @return OrganisationAbstractController
     */
    public function createService(ServiceLocatorInterface $container, $canonicalName = null, $requestedName = null)
    {
        return $this($container, $requestedName);
    }
}
