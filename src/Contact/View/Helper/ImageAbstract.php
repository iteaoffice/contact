<?php

/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     View
 * @subpackage  Helper
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Contact\View\Helper;

use Zend\Mvc\Router\RouteMatch;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\Url;
use Zend\View\HelperPluginManager;

/**
 * Class LinkAbstract
 * @package Project\View\Helper
 */
abstract class ImageAbstract extends AbstractHelper implements ServiceLocatorAwareInterface
{
    /**
     * @var HelperPluginManager
     */
    protected $serviceLocator;
    /**
     * @var RouteMatch
     */
    protected $routeMatch = null;
    /**
     * @var string Text to be placed as title or as part of the linkContent
     */
    protected $text;
    /**
     * @var string
     */
    protected $router;
    /**
     * @var string
     */
    protected $imageId;
    /**
     * @var array List of parameters needed to construct the URL from the router
     */
    protected $routerParams = [];
    /**
     * @var array Classes to be given to the link
     */
    protected $classes = [];
    /**
     * @var int $height
     */
    protected $height;
    /**
     * @var bool
     */
    protected $lightBox = false;

    /**
     * This function produces the link in the end
     *
     * @return string
     */
    public function createImageUrl()
    {
        /**
         * @var $url Url
         */
        $url = $this->serviceLocator->get('url');
        /**
         * @var $config array
         */
        $config = $this->serviceLocator->getServiceLocator()->get('config');

        $cdn = null;
        if (isset($config['cdn']) && $config['cdn']['enable']) {
            $cdn = $config['cdn']['address'];
        }

        $imageUrl = '<img src="%s%s" id="%s" class="%s" %s>';

        $image = sprintf(
            $imageUrl,
            $cdn,
            $url($this->router, $this->routerParams),
            $this->imageId,
            implode(' ', $this->classes),
            is_null($this->height) ? null : ' height="' . $this->height . '"'
        );

        if (!$this->lightBox) {
            return $image;
        } else {
            return '<a href="' . $url($this->router, $this->routerParams) . '" data-lightbox="itea">' . $image . '</a>';
        }

    }

    /**
     * Get the service locator.
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator->getServiceLocator();
    }

    /**
     * Set the service locator.
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return AbstractHelper
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * Add a parameter to the list of parameters for the router
     *
     * @param string $key
     * @param        $value
     * @param bool   $allowNull
     */
    public function addRouterParam($key, $value, $allowNull = true)
    {
        if (!$allowNull && is_null($value)) {
            throw new \InvalidArgumentException(sprintf("null is not allowed for %s", $key));
        }
        if (!is_null($value)) {
            $this->routerParams[$key] = $value;
        }
    }

    /**
     * @return string
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @param string $router
     */
    public function setRouter($router)
    {
        $this->router = $router;
    }

    /**
     * @return array
     */
    public function getRouterParams()
    {
        return $this->routerParams;
    }

    /**
     * @return string
     */
    public function getImageId()
    {
        return $this->imageId;
    }

    /**
     * @param string $imageId
     */
    public function setImageId($imageId)
    {
        $this->imageId = $imageId;
    }

    /**
     * @param string $classes
     *
     * @return $this
     */
    public function addClasses($classes)
    {
        if (!is_array($classes)) {
            $classes = [$classes];
        }
        foreach ($classes as $class) {
            $this->classes[] = $class;
        }

        return $this;
    }

    /**
     * @param boolean $lightBox
     */
    public function setLightBox($lightBox)
    {
        $this->lightBox = $lightBox;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return array
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * @param array $classes
     */
    public function setClasses($classes)
    {
        $this->classes = $classes;
    }
}