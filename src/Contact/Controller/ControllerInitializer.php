<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Controller
 * @package     Service
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   2004-2014 Japaveh Webdesign
 * @license     http://solodb.net/license.txt proprietary
 * @link        http://solodb.net
 */
namespace Contact\Controller;

use Contact\Service\FormService;
use Contact\Service\FormServiceAwareInterface;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Controller
 * @package     Service
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   2004-2014 Japaveh Webdesign
 * @license     http://solodb.net/license.txt proprietary
 * @link        http://solodb.net
 */
class ControllerInitializer implements InitializerInterface
{
    /**
     * @param                                           $instance
     * @param ServiceLocatorInterface|ControllerManager $serviceLocator
     *
     * @return ContactAbstractController
     */
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        /**
         * @var $sm ServiceLocatorInterface
         */
        $sm = $serviceLocator->getServiceLocator();

        if ($instance instanceof FormServiceAwareInterface) {
            /**
             * @var $formService FormService
             */
            $formService = $sm->get('contact_form_service');
            $instance->setFormService($formService);
        }
    }
}
