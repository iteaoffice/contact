<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    SoloDB
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 *
 * @version     4.0
 */

namespace Contact;

use Contact\Controller\Plugin\GetFilter;
use Contact\Controller\Plugin\HandleImport;
use Contact\Controller\Plugin\PartnerSearch;
use Contact\Version\Version;
use Zend\Console\Adapter\AdapterInterface;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature;
use Zend\Mvc\MvcEvent;

//Makes the module class more strict
/**
 *
 */
class Module implements
    Feature\AutoloaderProviderInterface,
    Feature\ServiceProviderInterface,
    Feature\ConfigProviderInterface,
    Feature\BootstrapListenerInterface
{
    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\ClassMapAutoloader' => [
                __DIR__ . '/../../autoload_classmap.php',
            ],
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/../../src/' . __NAMESPACE__,
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    /**
     * Go to the service configuration.
     *
     * @return array
     */
    public function getServiceConfig()
    {
        return include __DIR__ . '/../../config/services.config.php';
    }

    /**
     * Go to the service configuration.
     *
     * @return array
     */
    public function getViewHelperConfig()
    {
        return include __DIR__ . '/../../config/viewhelpers.config.php';
    }

    /**
     * Move this to here to have config cache working.
     *
     * @return array
     */
    public function getControllerPluginConfig()
    {
        return [
            'invokables' => [
                'handleImport'     => HandleImport::class,
                'partnerSearch'    => PartnerSearch::class,
                'getContactFilter' => GetFilter::class,
            ],
        ];
    }

    /**
     * Listen to the bootstrap event.
     *
     * @param EventInterface $e
     *
     * @return array
     */
    public function onBootstrap(EventInterface $e)
    {
        $app = $e->getParam('application');
        $em = $app->getEventManager();
        $em->attach(MvcEvent::EVENT_DISPATCH, function (MvcEvent $event) {
            $event->getApplication()->getServiceManager()
                ->get('contact_contact_navigation_service')->update();
        });
    }

    /**
     * @param \Zend\Console\Adapter\AdapterInterface $console
     *
     * @return array
     */
    public function getConsoleUsage(AdapterInterface $console)
    {
        return [
            'Contact management',
            // Describe available commands
            'partner-search reset'  => 'Reset the partner search (wipe and rebuilt index)',
            'partner-search update' => 'Update the Partner search',

        ];
    }

    /**
     * Returns a string containing a banner text, that describes the module and/or the application.
     * The banner is shown in the console window, when the user supplies invalid command-line parameters or invokes
     * the application with no parameters.
     *
     * The method is called with active Zend\Console\Adapter\AdapterInterface that can be used to directly access Console and send
     * output.
     *
     * @param AdapterInterface $console
     *
     * @return string|null
     */
    public function getConsoleBanner(AdapterInterface $console)
    {
        return 'debranova/contact ' . Version::VERSION
        . ' console application - powered by Zend Framework '
        . \Zend\Version\Version::VERSION;
    }
}
