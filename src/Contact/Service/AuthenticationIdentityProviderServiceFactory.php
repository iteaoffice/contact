<?php

/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link    https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Contact\Service;

use Contact\Provider\Identity\AuthenticationIdentityProvider;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Simple authentication provider factory
 *
 * @author Ingo Walz <ingo.walz@googlemail.com>
 */
class AuthenticationIdentityProviderServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $user                   = $serviceLocator->get('zfcuser_user_service');
        $simpleIdentityProvider = new AuthenticationIdentityProvider($user->getAuthService(), $serviceLocator);
        $config                 = $serviceLocator->get('BjyAuthorize\Config');

        $simpleIdentityProvider->setDefaultRole($config['default_role']);
        $simpleIdentityProvider->setAuthenticatedRole($config['authenticated_role']);

        return $simpleIdentityProvider;
    }
}
