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
    /**
     * @var ContainerInterface
     */
    protected $serviceManager;
    /**
     * @var HelperPluginManager
     */
    protected $helperPluginManager;
    /**
     * @var RouteMatch
     */
    protected $routeMatch = null;

    public function getRouteMatch(): ?RouteMatch
    {
        if (null === $this->routeMatch) {
            $this->routeMatch = $this->getServiceManager()->get('application')->getMvcEvent()->getRouteMatch();
        }

        return $this->routeMatch;
    }

    /**
     * @return ContainerInterface
     */
    public function getServiceManager(): ContainerInterface
    {
        return $this->serviceManager;
    }

    /**
     * @param ContainerInterface $serviceManager
     *
     * @return AbstractViewHelper
     */
    public function setServiceManager($serviceManager): AbstractViewHelper
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }

    /**
     * @return TwigRenderer
     */
    public function getRenderer()
    {
        return $this->getServiceManager()->get('ZfcTwigRenderer');
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function translate($string): string
    {
        return $this->getHelperPluginManager()->get('translate')->__invoke($string);
    }

    /**
     * @return HelperPluginManager
     */
    public function getHelperPluginManager(): HelperPluginManager
    {
        return $this->helperPluginManager;
    }

    /**
     * @param HelperPluginManager $helperPluginManager
     *
     * @return AbstractViewHelper
     */
    public function setHelperPluginManager($helperPluginManager): AbstractViewHelper
    {
        $this->helperPluginManager = $helperPluginManager;

        return $this;
    }
}
