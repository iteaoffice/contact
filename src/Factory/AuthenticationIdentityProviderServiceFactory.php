<?php
/**
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */
declare(strict_types=1);

namespace Contact\Factory;

use Admin\Service\AdminService;
use Contact\Provider\Identity\AuthenticationIdentityProvider;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class AuthenticationIdentityProviderServiceFactory
 *
 * @package Contact\Factory
 */
final class AuthenticationIdentityProviderServiceFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ): AuthenticationIdentityProvider {
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
}
