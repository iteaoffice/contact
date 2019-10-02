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

namespace Contact;

use Contact\Navigation\Service\ContactNavigationService;
use Zend\Console\Adapter\AdapterInterface;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManager;
use Zend\ModuleManager\Feature;
use Zend\Mvc\MvcEvent;

/**
 * Class Module
 *
 * @package Contact
 */
final class Module implements
    Feature\ConfigProviderInterface,
    Feature\BootstrapListenerInterface,
    Feature\ConsoleUsageProviderInterface
{
    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(EventInterface $e)
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

    public function getConsoleUsage(AdapterInterface $console): array
    {
        return [
            'Contact management',
            // Describe available commands
            'contact reset-access' => 'Reset the access-rights of contacts',
            'contact cleanup'      => 'Perform a cleanup of to be unwanted contacts',
        ];
    }
}
