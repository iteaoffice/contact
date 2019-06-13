<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Calecontactr
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Navigation\Factory;

use Contact\Navigation\Service\ContactNavigationService;
use Contact\Service\ContactService;
use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Navigation\Navigation;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class ContactNavigationServiceFactory
 *
 * @package Contact\Navigation\Factory
 */
final class ContactNavigationServiceFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ): ContactNavigationService {
        $contactNavigationService = new ContactNavigationService();

        $contactService = $container->get(ContactService::class);
        $contactNavigationService->setContactService($contactService);
        $application = $container->get('application');
        $contactNavigationService->setRouteMatch($application->getMvcEvent()->getRouteMatch());
        $contactNavigationService->setRouter($application->getMvcEvent()->getRouter());

        if ($container->get(AuthenticationService::class)->hasIdentity()) {
            $contactNavigationService->setContact(
                $container->get(AuthenticationService::class)
                    ->getIdentity()
            );
        }

        $config = $container->get('Config');

        /* @var $navigation Navigation */
        $navigation = $container->get($config['admin_option']['community_navigation_container']);
        $contactNavigationService->setNavigation($navigation);

        return $contactNavigationService;
    }
}
