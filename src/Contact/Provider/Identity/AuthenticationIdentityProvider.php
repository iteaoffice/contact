<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link    https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Contact\Provider\Identity;

use Admin\Service\AdminService;
use BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider as BjyAuthorizeAuthenticationIdentityProvider;
use BjyAuthorize\Provider\Role\ProviderInterface as RoleProviderInterface;
use Contact\Service\ContactService;
use Zend\Authentication\AuthenticationService;
use Zend\Cache\StorageFactory;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Simple identity provider to handle simply guest|user
 *
 * @author Ingo Walz <ingo.walz@googlemail.com>
 */
class AuthenticationIdentityProvider extends BjyAuthorizeAuthenticationIdentityProvider
{
    /**
     * ServiceLocatorInterface
     */
    protected $serviceLocator;
    /**
     * @var ContactService;
     */
    protected $contactService;
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
     * @param AuthenticationService   $authService
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct(AuthenticationService $authService, ServiceLocatorInterface $serviceLocator)
    {
        parent::__construct($authService);
        $this->adminService = $serviceLocator->get(AdminService::class);
        $this->cache = $serviceLocator->get('contact_cache');
        $this->config = $serviceLocator->get('contact_module_config');
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
            $success = false;
            $key = sprintf("%s-role-list-identity-%s", $this->config['cache_key'], $identity->getId());
            $roles = $this->cache->getItem($key, $success);
            if (!$success) {
                //Get also the roles assigned via selections
                $accessRoles = $this->adminService->findAccessRolesByContact($identity);
                $this->cache->setItem($key, array_map('strtolower', $accessRoles->toArray()));
            }

            return $roles;
        }

        return [$this->authenticatedRole];
    }
}
