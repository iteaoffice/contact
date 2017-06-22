<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Contact\Navigation\Factory;

use Admin\Navigation\Invokable\AbstractNavigationInvokable;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class NavigationInvokableFactory
 *
 * @package Contact\Navigation\Factory
 */
final class NavigationInvokableFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @return AbstractNavigationInvokable
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ): AbstractNavigationInvokable {
        /** @var $invokable AbstractNavigationInvokable */
        return new $requestedName($container, $options);
    }
}
