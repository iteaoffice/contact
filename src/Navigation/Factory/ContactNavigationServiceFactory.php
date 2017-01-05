<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Calecontactr
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

namespace Contact\Navigation\Factory;

use Contact\Navigation\Service\ContactNavigationService;
use Contact\Service\ContactService;
use Interop\Container\ContainerInterface;
use Zend\Navigation\Navigation;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * NodeService.
 *
 * this is a wrapper for node entity related services
 */
final class ContactNavigationServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return ContactNavigationService
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ): ContactNavigationService {
        $contactNavigationService = new ContactNavigationService();

        /**
         * @var $contactService ContactService
         */
        $contactService = $container->get(ContactService::class);
        $contactNavigationService->setContactService($contactService);
        $application = $container->get('application');
        $contactNavigationService->setRouteMatch($application->getMvcEvent()->getRouteMatch());
        $contactNavigationService->setRouter($application->getMvcEvent()->getRouter());

        if ($container->get('Application\Authentication\Service')->hasIdentity()) {
            $contactNavigationService->setContact(
                $container->get('Application\Authentication\Service')
                          ->getIdentity()
            );
        }

        /* @var $navigation Navigation */
        $navigation = $container->get('Zend\Navigation\Community');
        //This is a nastry trick, but currently I do not have a better solution
        if (defined("ITEAOFFICE_HOST") && ITEAOFFICE_HOST === 'aeneas') {
            $navigation = $container->get('Zend\Navigation\Project-community');
        }
        $contactNavigationService->setNavigation($navigation);

        return $contactNavigationService;
    }
}
