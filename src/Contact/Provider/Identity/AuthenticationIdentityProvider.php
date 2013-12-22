<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link    https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Contact\Provider\Identity;

use BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider as BjyAuthorizeAuthenticationIdentityProvider;
use Zend\Authentication\AuthenticationService;
use Contact\Service\ContactService;

use BjyAuthorize\Provider\Role\ProviderInterface as RoleProviderInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Simple identity provider to handle simply guest|user
 *
 * @author Ingo Walz <ingo.walz@googlemail.com>
 */
class AuthenticationIdentityProvider extends BjyAuthorizeAuthenticationIdentityProvider
{
    /**
     * ServiceLocatorAwareInterface
     */
    protected $serviceLocator;

    /**
     * @param AuthenticationService $authService
     * @param ContactService        $contactService
     */
    public function __construct(AuthenticationService $authService, ContactService $contactService)
    {
        parent::__construct($authService);
        $this->contactService = $contactService;
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

            //Get also the roles assigned via selections
            $this->contactService->setContact($identity);
            $localRoles = array();
            foreach ($this->contactService->findAll('access') as $access) {
                if ($this->contactService->inSelection($access->getSelection())) {
                    $localRoles[] = $access;
                }
            };

            return array_merge($localRoles, $identity->getRoles());
        }

        return array($this->authenticatedRole);
    }
}
