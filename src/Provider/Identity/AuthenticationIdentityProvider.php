<?php

/**
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Contact\Provider\Identity;

use Admin\Service\AdminService;
use BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider as BjyAuthorizeAuthenticationIdentityProvider;
use BjyAuthorize\Provider\Role\ProviderInterface as RoleProviderInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\Permissions\Acl\Role\RoleInterface;

/**
 * Class AuthenticationIdentityProvider
 *
 * @package Contact\Provider\Identity
 */
final class AuthenticationIdentityProvider extends BjyAuthorizeAuthenticationIdentityProvider
{
    private AdminService $adminService;

    public function __construct(AuthenticationService $authService, AdminService $adminService)
    {
        parent::__construct($authService);
        $this->adminService = $adminService;
    }

    public function getIdentityRoles(): array
    {
        if (! $identity = $this->authService->getIdentity()) {
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
