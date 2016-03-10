<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * PHP Version 5
 *
 * @category    Achievement
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2016 ITEA Office
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/main for the canonical source repository
 */
namespace Contact\Search\Factory;

use Contact\Search\Service\ContactSearchService;
use Contact\Service\ContactService;
use Project\Search\Service\DescriptionSearchService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ContactSearchFactory
 *
 * @package Contact\Search\Factory
 */
class ContactSearchFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return DescriptionSearchService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        try {
            $searchService = new ContactSearchService();
            $searchService->setServiceLocator($serviceLocator);

            /** @var ContactService $contactService */
            $contactService = $serviceLocator->get(ContactService::class);
            $searchService->setContactService($contactService);

            return $searchService;
        } catch (\Exception $e) {
            var_dump($e);
            die('test');
        }
    }
}
