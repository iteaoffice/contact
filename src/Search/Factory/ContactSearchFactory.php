<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Achievement
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
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
     * @param string $requestedName
     * @param array|null|null $options
     *
     * @return ContactSearchService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ContactSearchService
    {
        /** @var ContactSearchService $searchService */
        $searchService = new $requestedName($options);
        $searchService->setServiceLocator($container);

        /** @var ContactService $contactService */
        $contactService = $container->get(ContactService::class);
        $searchService->setContactService($contactService);

        return $searchService;
    }
}
