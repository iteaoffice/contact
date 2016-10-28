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
 * @link        http://github.com/iteaoffice/main for the canonical source repository
 */
namespace Contact\Factory;

use Admin\Service\AdminService;
use Contact\Options\ModuleOptions;
use Contact\Service\AddressService;
use Contact\Service\ContactService;
use Deeplink\Service\DeeplinkService;
use Doctrine\ORM\EntityManager;
use General\Service\GeneralService;
use Interop\Container\ContainerInterface;
use Organisation\Service\OrganisationService;
use Project\Service\ProjectService;
use Zend\ServiceManager\Factory\FactoryInterface;
use ZfcUser\Options\UserServiceOptionsInterface;

/**
 * Class ContactServiceFactory
 *
 * @package Contact\Factory
 */
final class ContactServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return ContactService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ContactService
    {
        /** @var ContactService $contactService */
        $contactService = new $requestedName($options);
        $contactService->setServiceLocator($container);

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $contactService->setEntityManager($entityManager);

        /** @var AdminService $adminService */
        $adminService = $container->get(AdminService::class);
        $contactService->setAdminService($adminService);

        /** @var ProjectService $projectService */
        $projectService = $container->get(ProjectService::class);
        $contactService->setProjectService($projectService);

        /** @var OrganisationService $organisationService */
        $organisationService = $container->get(OrganisationService::class);
        $contactService->setOrganisationService($organisationService);

        /** @var AddressService $addressService */
        $addressService = $container->get(AddressService::class);
        $contactService->setAddressService($addressService);

        /** @var GeneralService $generalService */
        $generalService = $container->get(GeneralService::class);
        $contactService->setGeneralService($generalService);

        /** @var DeeplinkService $deeplinkService */
        $deeplinkService = $container->get(DeeplinkService::class);
        $contactService->setDeeplinkService($deeplinkService);

        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $container->get(ModuleOptions::class);
        $contactService->setModuleOptions($moduleOptions);

        /** @var UserServiceOptionsInterface $zfcModuleOptions */
        $zfcModuleOptions = $container->get('zfcuser_module_options');
        $contactService->setZfcUserOptions($zfcModuleOptions);

        return $contactService;
    }
}
