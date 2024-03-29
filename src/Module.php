<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact;

use Contact\Navigation\Service\ContactNavigationService;
use Laminas\EventManager\EventInterface;
use Laminas\EventManager\EventManager;
use Laminas\ModuleManager\Feature;
use Laminas\Mvc\MvcEvent;

/**
 * Class Module
 *
 * @package Contact
 */
final class Module implements
    Feature\ConfigProviderInterface,
    Feature\BootstrapListenerInterface
{
    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(EventInterface $e): void
    {
        $app = $e->getParam('application');
        /** @var EventManager $em */
        $em = $app->getEventManager();
        $em->attach(
            MvcEvent::EVENT_DISPATCH,
            static function (MvcEvent $event) {
                $event->getApplication()->getServiceManager()->get(ContactNavigationService::class)->update();
            }
        );
    }
}
