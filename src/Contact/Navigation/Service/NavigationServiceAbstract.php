<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Program
 * @package     Navigation
 * @subpackage  Service
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Contact\Navigation\Service;

use Contact\Service\ContactService;
use Zend\I18n\View\Helper\Translate;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Mvc\Router\Http\TreeRouteStack;
use Zend\Navigation\Navigation;

/**
 * Factory for the Program admin navigation
 *
 * @package    Program
 * @subpackage Navigation\Service
 */
class NavigationServiceAbstract
{
    /**
     * @var RouteMatch
     */
    protected $routeMatch;
    /**
     * @var Translate
     */
    protected $translator;
    /**
     * @var ContactService;
     */
    protected $contactService;
    /**
     * @var TreeRouteStack
     */
    protected $router;
    /**
     * @var Navigation
     */
    protected $navigation;
    /**
     * @var Navigation
     */
    protected $cmsNavigation;

    /**
     * @return Navigation
     */
    public function getNavigation()
    {
        return $this->navigation;
    }

    /**
     * @param Navigation $navigation
     *
     * @return NavigationServiceAbstract
     */
    public function setNavigation($navigation)
    {
        $this->navigation = $navigation;

        return $this;
    }

    /**
     * @return Navigation
     */
    public function getCmsNavigation()
    {
        return $this->cmsNavigation;
    }

    /**
     * @param Navigation $cmsNavigation
     */
    public function setCmsNavigation($cmsNavigation)
    {
        $this->cmsNavigation = $cmsNavigation;
    }

    /**
     * @return RouteMatch
     */
    public function getRouteMatch()
    {
        return $this->routeMatch;
    }

    /**
     * @param RouteMatch $routeMatch
     *
     * @return NavigationServiceAbstract
     */
    public function setRouteMatch($routeMatch)
    {
        $this->routeMatch = $routeMatch;

        return $this;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function translate($string)
    {
        return $this->getTranslator()->__invoke($string);
    }

    /**
     * @return Translate
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @param Translate $translator
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return TreeRouteStack
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @param TreeRouteStack $router
     *
     * @return NavigationServiceAbstract;
     */
    public function setRouter($router)
    {
        $this->router = $router;

        return $this;
    }

    /**
     * @return ContactService
     */
    public function getContactService()
    {
        return $this->contactService;
    }

    /**
     * @param ContactService $contactService
     *
     * @return NavigationServiceAbstract
     */
    public function setContactService($contactService)
    {
        $this->contactService = $contactService;

        return $this;
    }
}
