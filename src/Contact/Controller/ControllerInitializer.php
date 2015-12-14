<?php
/**
 * Japaveh Webdesign copyright message placeholder.
 *
 * @category    Controller
 *
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   2004-2014 Japaveh Webdesign
 * @license     http://solodb.net/license.txt proprietary
 *
 * @link        http://solodb.net
 */

namespace Contact\Controller;

use Contact\Search;
use Contact\Service;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Japaveh Webdesign copyright message placeholder.
 *
 * @category    Controller
 *
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   2004-2014 Japaveh Webdesign
 * @license     http://solodb.net/license.txt proprietary
 *
 * @link        http://solodb.net
 */
class ControllerInitializer implements InitializerInterface
{
    /**
     * @param string                                    $instance
     * @param ServiceLocatorInterface|ControllerManager $controllerManager
     *
     * @return void
     */
    public function initialize($instance, ServiceLocatorInterface $controllerManager)
    {
        if (!is_object($instance)) {
            return;
        }
        $arrayCheck = [
            Search\ContactSearchServiceAwareInterface::class => Search\ContactSearchService::class,
            Service\SelectionServiceAwareInterface::class    => Service\SelectionService::class,
            Service\FormServiceAwareInterface::class         => 'contact_form_service',
            Service\ContactServiceAwareInterface::class      => 'contact_contact_service',
        ];
        /*
         * @var $controllerManager ControllerManager
         */
        $sm = $controllerManager->getServiceLocator();

        foreach ($arrayCheck as $interface => $serviceName) {
            if (isset(class_implements($instance)[$interface])) {
                $this->setInterface($instance, $interface, $sm->get($serviceName));
            }
        }

        return;
    }

    /**
     * @param $interface
     * @param $instance
     * @param $service
     */
    protected function setInterface($instance, $interface, $service)
    {
        foreach (get_class_methods($interface) as $setter) {
            if (strpos($setter, 'set') !== false) {
                $instance->$setter($service);
            }
        }
    }
}
