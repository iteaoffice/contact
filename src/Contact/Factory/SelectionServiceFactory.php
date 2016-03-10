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
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ContactServiceFactory
 *
 * @package Contact\Factory
 */
class SelectionServiceFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SelectionService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        try {
            $selectionService = new SelectionService();
            $selectionService->setServiceLocator($serviceLocator);

            /** @var EntityManager $entityManager */
            $entityManager = $serviceLocator->get(EntityManager::class);
            $selectionService->setEntityManager($entityManager);

            /** @var ContactService $contactService */
            $contactService = $serviceLocator->get(ContactService::class);
            $selectionService->setContactService($contactService);

            return $selectionService;
        } catch (\Exception $e) {
            var_dump($e);
            die('test');
        }
    }
}


//zfcuser_module_options
