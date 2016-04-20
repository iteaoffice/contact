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
namespace Contact\Acl\Factory;

use Admin\Service\AdminService;
use Contact\Acl\Assertion\AssertionAbstract;
use Contact\Service\ContactService;
use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AssertionFactory
 *
 * @package Contact\Acl\Factory
 */
class AssertionFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param null|string        $requestedName
     * @param array|null         $options
     *
     * @return AssertionAbstract
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var $assertion AssertionAbstract */
        $assertion = new $requestedName();
        $assertion->setServiceLocator($container);

        /** @var AdminService $adminService */
        $adminService = $container->get(AdminService::class);
        $assertion->setAdminService($adminService);

        //Inject the logged in user if applicable
        /** @var AuthenticationService $authenticationService */
        $authenticationService = $container->get('Application\Authentication\Service');
        if ($authenticationService->hasIdentity()) {
            $assertion->setContact($authenticationService->getIdentity());
        }

        /** @var ContactService $contactService */
        $contactService = $container->get(ContactService::class);
        $assertion->setContactService($contactService);

        return $assertion;
    }

    /**
     * @param ServiceLocatorInterface $container
     * @param null|string             $canonicalName
     * @param null|string             $requestedName
     *
     * @return AssertionAbstract
     */
    public function createService(ServiceLocatorInterface $container, $canonicalName = null, $requestedName = null)
    {
        return $this($container, $requestedName);
    }
}
