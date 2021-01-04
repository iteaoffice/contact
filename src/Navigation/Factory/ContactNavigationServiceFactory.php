<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\Navigation\Factory;

use Contact\Navigation\Service\ContactNavigationService;
use Contact\Service\ContactService;
use Interop\Container\ContainerInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Navigation\Navigation;
use Laminas\ServiceManager\Factory\FactoryInterface;

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
        $contactNavigationService->setTranslator($container->get(TranslatorInterface::class));

        if ($container->get(AuthenticationService::class)->hasIdentity()) {
            $contactNavigationService->setContact(
                $container->get(AuthenticationService::class)
                    ->getIdentity()
            );
        }

        $config = $container->get('Config');

        /* @var $navigation Navigation */
        $navigation = $container->get($config['general_option']['community_navigation_container']);
        $contactNavigationService->setNavigation($navigation);

        return $contactNavigationService;
    }
}
