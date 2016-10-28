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

use Contact\Service\ContactService;
use Contact\Service\SelectionService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class SelectionServiceFactory
 *
 * @package Contact\Factory
 */
final class SelectionServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param                    $requestedName
     * @param array|null         $options
     *
     * @return SelectionService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SelectionService
    {
        /** @var SelectionService $selectionService */
        $selectionService = new $requestedName($options);
        $selectionService->setServiceLocator($container);

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $selectionService->setEntityManager($entityManager);

        /** @var ContactService $contactService */
        $contactService = $container->get(ContactService::class);
        $selectionService->setContactService($contactService);

        return $selectionService;
    }
}
