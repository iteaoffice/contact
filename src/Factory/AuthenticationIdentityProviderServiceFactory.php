<?php

/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize).
 *
 * @link    https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 *
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Contact\Factory;

use Admin\Service\AdminService;
use Contact\Provider\Identity\AuthenticationIdentityProvider;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Simple authentication provider factory.
 *
 * @author Ingo Walz <ingo.walz@googlemail.com>
 */
final class AuthenticationIdentityProviderServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return AuthenticationIdentityProvider
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $user = $container->get('zfcuser_user_service');

        /** @var AdminService $adminService */
        $adminService = $container->get(AdminService::class);
        /** @var AuthenticationIdentityProvider $simpleIdentityProvider */
        $simpleIdentityProvider = new $requestedName($user->getAuthService(), $adminService, $options);
        $config = $container->get('BjyAuthorize\Config');
        $simpleIdentityProvider->setDefaultRole($config['default_role']);
        $simpleIdentityProvider->setAuthenticatedRole($config['authenticated_role']);

        return $simpleIdentityProvider;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param string                  $canonicalName
     * @param string                  $requestedName
     *
     * @return AuthenticationIdentityProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $canonicalName = null, $requestedName = null)
    {
        return $this($serviceLocator, $requestedName);
    }
}
