<?php
/**
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Contact\View\Factory;

use Contact\View\Helper\AbstractViewHelper;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class LinkInvokableFactory
 *
 * @package Contact\View\Factory
 */
final class ViewHelperFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var AbstractViewHelper $viewHelper */
        $viewHelper = new $requestedName($options);
        $viewHelper->setContainer($container);
        $viewHelper->setHelperPluginManager($container->get('ViewHelperManager'));

        return $viewHelper;
    }
}
