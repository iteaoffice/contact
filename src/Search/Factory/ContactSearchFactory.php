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
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class ContactSearchFactory
 *
 * @package Contact\Search\Factory
 */
class ContactSearchFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null|null    $options
     *
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $searchService = new ContactSearchService();
        $searchService->setServiceLocator($container);

        /** @var ContactService $contactService */
        $contactService = $container->get(ContactService::class);
        $searchService->setContactService($contactService);

        return $searchService;
    }
}