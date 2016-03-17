<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Calecontactr
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Contact\Navigation\Factory;

use Contact\Navigation\Service\ContactNavigationService;
use Contact\Service\ContactService;
use Zend\Navigation\Navigation;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * NodeService.
 *
 * this is a wrapper for node entity related services
 */
class ContactNavigationServiceFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ContactNavigationService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $contactNavigationService = new ContactNavigationService();

        $contactNavigationService->setTranslator($serviceLocator->get('viewhelpermanager')->get('translate'));
        /**
         * @var $contactService ContactService
         */
        $contactService = clone $serviceLocator->get(ContactService::class);
        $contactNavigationService->setContactService($contactService);
        $application = $serviceLocator->get('application');
        $contactNavigationService->setRouteMatch($application->getMvcEvent()->getRouteMatch());
        $contactNavigationService->setRouter($application->getMvcEvent()->getRouter());

        if ($serviceLocator->get('Application\Authentication\Service')->hasIdentity()) {
            $contactNavigationService->setContact($serviceLocator->get('Application\Authentication\Service')
                ->getIdentity());
        }

        /* @var $navigation Navigation */
        $navigation = $serviceLocator->get('navigation');
        $contactNavigationService->setNavigation($navigation);

        return $contactNavigationService;
    }
}
