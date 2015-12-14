<?php
/**
 * Japaveh Webdesign copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   2004-2014 Japaveh Webdesign
 * @license     http://solodb.net/license.txt proprietary
 *
 * @link        http://solodb.net
 */

namespace Contact\Service;

use Contact\Search;
use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Japaveh Webdesign copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   2004-2014 Japaveh Webdesign
 * @license     http://solodb.net/license.txt proprietary
 *
 * @link        http://solodb.net
 */
class ServiceInitializer implements InitializerInterface
{
    /**
     * @param                         $instance
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        if (!is_object($instance)) {
            return;
        }
        /*
         * Have a central place to inject the service locator
         */
        if ($instance instanceof ServiceLocatorAwareInterface) {
            $instance->setServiceLocator($serviceLocator);
        }
        $arrayCheck = [
            SelectionServiceAwareInterface::class            => SelectionService::class,
            AddressServiceAwareInterface::class              => 'contact_address_service',
            ContactServiceAwareInterface::class              => 'contact_contact_service',
            Search\ContactSearchServiceAwareInterface::class => Search\ContactServiceAwareInterface::class,
        ];
        /*
         * Go over each interface to see if we should add an interface
         */
        foreach (class_implements($instance) as $interface) {
            if (array_key_exists($interface, $arrayCheck)) {
                $this->setInterface($instance, $interface, $serviceLocator->get($arrayCheck[$interface]));
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
