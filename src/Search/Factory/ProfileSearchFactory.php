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

declare(strict_types=1);

namespace Contact\Search\Factory;

use Contact\Search\Service\ProfileSearchService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class ContactSearchFactory
 *
 * @package Contact\Search\Factory
 */
class ProfileSearchFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null|null    $options
     *
     * @return ProfileSearchService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ProfileSearchService
    {
        /** @var ProfileSearchService $searchService */
        $searchService = new $requestedName($options);
        $searchService->setServiceLocator($container);

        return $searchService;
    }
}
