<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link    https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Contact\Provider\Identity;

use Zend\Authentication\AuthenticationService;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Cache\StorageFactory;

use Contact\Service\ContactService;

use BjyAuthorize\Provider\Role\ProviderInterface as RoleProviderInterface;
use BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider as BjyAuthorizeAuthenticationIdentityProvider;

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
        $this->contactService = $serviceLocator->get('contact_contact_service');
        $this->cache          = $serviceLocator->get('contact_cache');
        $this->config         = $serviceLocator->get('contact_module_config');
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentityRoles()
    {

        if (!$identity = $this->authService->getIdentity()) {
            return array($this->defaultRole);
        }

        if ($identity instanceof RoleInterface) {
            return array($identity);
        }

        if ($identity instanceof RoleProviderInterface) {

            $success = false;
            $key     = $this->config['cache_key'] . '-role-list-identity-' . $identity->getId();
            $roles   = $this->cache->getItem($key, $success);

            if (!$success) {
                //Get also the roles assigned via selections
                $this->contactService->setContact($identity);
                $localRoles = array();
                foreach ($this->contactService->findAll('access') as $access) {
                    if ($this->contactService->inSelection($access->getSelection())) {
                        $localRoles[] = strtolower($access->getAccess());
                    }
                };

                $roles = array_merge($localRoles, $identity->getRoles());

                $this->cache->setItem($key, $roles);
            }

            return $roles;
        }

        return array($this->authenticatedRole);
    }
}
