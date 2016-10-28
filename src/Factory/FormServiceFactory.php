<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * PHP Version 5
 *
 * @category    General
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2016 ITEA Office
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/main for the canonical source repository
 */
namespace Contact\Factory;

use Contact\Service\FormService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class FormServiceFactory
 *
 * @package Contact\Factory
 */
final class FormServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return FormService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FormService
    {
        /** @var FormService $formService */
        $formService = new $requestedName();
        $formService->setServiceLocator($container);
        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $formService->setEntityManager($entityManager);

        return $formService;
    }
}
