<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category   Project
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2014 ITEA Office
 * @license    http://debranova.org/license.txt proprietary
 *
 * @link       http://debranova.org
 */

namespace Contact\Acl\Assertion;

use Admin\Service\AdminService;
use Contact\Service\ContactService;
use Contact\Service\ContactServiceAwareInterface;
use Zend\Mvc\Router\RouteMatch;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Create a link to an document.
 *
 * @category   Project
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2014 ITEA Office
 * @license    http://debranova.org/license.txt proprietary
 *
 * @link       http://debranova.org
 */
abstract class AssertionAbstract implements
    AssertionInterface,
    ServiceLocatorAwareInterface,
    ContactServiceAwareInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;
    /**
     * @var ContactService
     */
    protected $contactService;
    /**
     * @var array
     */
    protected $accessRoles = [];

    /**
     * @return RouteMatch
     */
    public function getRouteMatch()
    {
        return $this->getServiceLocator()->get("Application")->getMvcEvent()->getRouteMatch();
    }

    /**
     * Get the service locator.
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Set the service locator.
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return AssertionAbstract
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasContact()
    {
        return !$this->getContactService()->isEmpty();
    }

    /**
     * @return ContactService
     */
    public function getContactService()
    {
        if ($this->contactService->isEmpty() && $this->getServiceLocator()->get('zfcuser_auth_service')->hasIdentity()
        ) {
            $this->contactService->setContact(
                $this->getServiceLocator()->get('zfcuser_auth_service')->getIdentity()
            );
        }

        return $this->contactService;
    }

    /**
     * The contact service.
     *
     * @param ContactService $contactService
     *
     * @return $this;
     */
    public function setContactService(ContactService $contactService)
    {
        $this->contactService = $contactService;

        return $this;
    }

    /**
     * @return AdminService
     */
    public function getAdminService()
    {
        return $this->getServiceLocator()->get(AdminService::class);
    }

    /**
     * Returns true when a role or roles have access.
     *
     * @param $roles
     *
     * @return boolean
     */
    protected function rolesHaveAccess($roles)
    {
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        foreach ($this->getAccessRoles() as $access) {
            if (in_array(strtolower($access), $roles)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getAccessRoles()
    {
        if (empty($this->accessRoles) && !$this->getContactService()->isEmpty()) {
            $this->accessRoles = $this->getAdminService()->findAccessRolesByContactAsArray(
                $this->getContactService()->getContact()
            );
        }

        return $this->accessRoles;
    }
}
