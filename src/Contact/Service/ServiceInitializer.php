<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Contact
 * @package     Service
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   2004-2014 Japaveh Webdesign
 * @license     http://solodb.net/license.txt proprietary
 * @link        http://solodb.net
 */
namespace Contact\Service;

use Application\Service\EntityManagerAwareInterface;
use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Contact
 * @package     Service
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   2004-2014 Japaveh Webdesign
 * @license     http://solodb.net/license.txt proprietary
 * @link        http://solodb.net
 */
class ServiceInitializer implements InitializerInterface
{
    /**
     * @param                         $instance
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return void
     */
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {

        if (!is_object($instance)) {
            return;
        }

        /**
         * Have a central place to inject the service locator
         * @todo: why is this needed for an invokable?
         */
        if ($instance instanceof ServiceLocatorAwareInterface) {
            $instance->setServiceLocator($serviceLocator);
        }

        $arrayCheck = [
            EntityManagerAwareInterface::class  => 'doctrine.entitymanager.orm_default',
            AddressServiceAwareInterface::class => 'contact_address_service',
            ContactServiceAwareInterface::class => 'contact_contact_service',
        ];

        foreach ($arrayCheck as $interface => $serviceName) {
            if (isset(class_implements($instance)[$interface])) {
                $this->setInterface($instance, $interface, $serviceLocator->get($serviceName));
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
