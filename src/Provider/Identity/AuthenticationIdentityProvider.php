<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize).
 *
 * @link    https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 *
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Contact\Provider\Identity;

use Admin\Service\AdminService;
use BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider as BjyAuthorizeAuthenticationIdentityProvider;
use BjyAuthorize\Provider\Role\ProviderInterface as RoleProviderInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Cache\StorageFactory;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Class AuthenticationIdentityProvider
 *
 * @package Contact\Provider\Identity
 */
class AuthenticationIdentityProvider extends BjyAuthorizeAuthenticationIdentityProvider
{
    /**
     * @var AdminService;
     */
    protected $adminService;
    /**
     * @var StorageFactory
     */
    protected $cache;
    /**
     * @var array
     */
    protected $config;

    /**
     * AuthenticationIdentityProvider constructor.
     *
     * @param AuthenticationService $authService
     * @param AdminService          $adminService
     * @param array|null            $options
     */
    public function __construct(AuthenticationService $authService, AdminService $adminService, array $options = null)
    {
        parent::__construct($authService);
        $this->adminService = $adminService;
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentityRoles()
    {
        if (!$identity = $this->authService->getIdentity()) {
            return [$this->defaultRole];
        }
        if ($identity instanceof RoleInterface) {
            return [$identity];
        }
        if ($identity instanceof RoleProviderInterface) {
            return $this->adminService->findAccessRolesByContactAsArray($identity);
        }

        return [$this->authenticatedRole];
    }
}
