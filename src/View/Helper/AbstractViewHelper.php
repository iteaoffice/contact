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

namespace Contact\View\Helper;

use Interop\Container\ContainerInterface;
use Zend\Router\Http\RouteMatch;
use Zend\View\Helper\AbstractHelper;
use Zend\View\HelperPluginManager;
use ZfcTwig\View\TwigRenderer;

/**
 * Class AbstractViewHelper
 *
 * @package Contact\View\Helper
 */
abstract class AbstractViewHelper extends AbstractHelper
{
    protected ContainerInterface $container;
    protected HelperPluginManager $helperPluginManager;
    protected ?RouteMatch $routeMatch;

    public function getRouteMatch(): ?RouteMatch
    {
        if (null === $this->routeMatch) {
            $this->routeMatch = $this->container->get('application')->getMvcEvent()->getRouteMatch();
        }

        return $this->routeMatch;
    }

    public function getRenderer(): TwigRenderer
    {
        return $this->getContainer()->get(TwigRenderer::class);
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function setContainer($container): AbstractViewHelper
    {
        $this->container = $container;

        return $this;
    }

    public function translate($string): string
    {
        return $this->getHelperPluginManager()->get('translate')->__invoke($string);
    }

    public function getHelperPluginManager(): HelperPluginManager
    {
        return $this->helperPluginManager;
    }

    public function setHelperPluginManager($helperPluginManager): AbstractViewHelper
    {
        $this->helperPluginManager = $helperPluginManager;

        return $this;
    }
}
