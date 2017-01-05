<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/main for the canonical source repository
 */
namespace Contact\Factory;

use Contact\Service\AddressService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

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
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AddressService
    {
        /** @var AddressService $addressService */
        $addressService = new $requestedName($options);
        $addressService->setServiceLocator($container);

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $addressService->setEntityManager($entityManager);

        return $addressService;
    }
}
