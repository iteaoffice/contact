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
use Organisation\Service\OrganisationService;
use Project\Service\ProjectService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcUser\Options\UserServiceOptionsInterface;

/**
 * Class ContactServiceFactory
 *
 * @package Contact\Factory
 */
class ContactServiceFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ContactService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $contactService = new ContactService();
        $contactService->setServiceLocator($serviceLocator);

        /** @var EntityManager $entityManager */
        $entityManager = $serviceLocator->get(EntityManager::class);
        $contactService->setEntityManager($entityManager);

        /** @var AdminService $adminService */
        $adminService = $serviceLocator->get(AdminService::class);
        $contactService->setAdminService($adminService);

        /** @var ProjectService $projectService */
        $projectService = $serviceLocator->get(ProjectService::class);
        $contactService->setProjectService($projectService);

        /** @var OrganisationService $organisationService */
        $organisationService = $serviceLocator->get(OrganisationService::class);
        $contactService->setOrganisationService($organisationService);

        /** @var AddressService $addressService */
        $addressService = $serviceLocator->get(AddressService::class);
        $contactService->setAddressService($addressService);

        /** @var GeneralService $generalService */
        $generalService = $serviceLocator->get(GeneralService::class);
        $contactService->setGeneralService($generalService);

        /** @var DeeplinkService $deeplinkService */
        $deeplinkService = $serviceLocator->get(DeeplinkService::class);
        $contactService->setDeeplinkService($deeplinkService);

        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $serviceLocator->get(ModuleOptions::class);
        $contactService->setModuleOptions($moduleOptions);

        /** @var UserServiceOptionsInterface $zfcModuleOptions */
        $zfcModuleOptions = $serviceLocator->get('zfcuser_module_options');
        $contactService->setZfcUserOptions($zfcModuleOptions);

        return $contactService;
    }
}
