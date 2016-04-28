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

use Contact\Service\AddressService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AddressServiceFactory
 *
 * @package Contact\Factory
 */
final class AddressServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return AddressService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var AddressService $addressService */
        $addressService = new $requestedName($options);
        $addressService->setServiceLocator($container);

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $addressService->setEntityManager($entityManager);

        return $addressService;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param string                  $canonicalName
     * @param string                  $requestedName
     *
     * @return AddressService
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $canonicalName = null, $requestedName = null)
    {
        return $this($serviceLocator, $requestedName);
    }
}
