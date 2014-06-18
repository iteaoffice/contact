<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Calecontactr
 * @package     Service
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Contact\Navigation\Factory;

use Contact\Navigation\Service\ContactNavigationService;
use Contact\Service\ContactService;
use Zend\Navigation\Navigation;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * NodeService
 *
 * this is a wrapper for node entity related services
 *
 */
class ContactNavigationServiceFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return array|mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $contactNavigationService = new ContactNavigationService();
        $contactNavigationService->setTranslator($serviceLocator->get('viewhelpermanager')->get('translate'));
        /**
         * @var $contactService ContactService
         */
        $contactService = clone $serviceLocator->get('contact_contact_service');
        $contactNavigationService->setContactService($contactService);
        $application = $serviceLocator->get('application');
        $contactNavigationService->setRouteMatch($application->getMvcEvent()->getRouteMatch());
        $contactNavigationService->setRouter($application->getMvcEvent()->getRouter());
        /**
         * @var $navigation Navigation
         */
        $navigation = $serviceLocator->get('navigation');
        $contactNavigationService->setNavigation($navigation);

        return $contactNavigationService;
    }
}
