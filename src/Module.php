<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * PHP Version 5
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2016 ITEA Office
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

namespace Contact;

use Contact\Controller\Plugin\GetFilter;
use Contact\Controller\Plugin\HandleImport;
use Contact\Controller\Plugin\PartnerSearch;
use Contact\Navigation\Service\ContactNavigationService;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManager;
use Zend\ModuleManager\Feature;
use Zend\Mvc\MvcEvent;

//Makes the module class more strict
/**
 *
 */
class Module implements Feature\AutoloaderProviderInterface, Feature\ConfigProviderInterface, Feature\BootstrapListenerInterface
{
    /**
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\ClassMapAutoloader' => [
                __DIR__ . '/../autoload_classmap.php',
            ],
        ];
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
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
        /** @var EventManager $em */
        $em = $app->getEventManager();
        $em->attach(MvcEvent::EVENT_DISPATCH, function (MvcEvent $event) {
            $event->getApplication()->getServiceManager()->get(ContactNavigationService::class)->update();
        });
    }
}
