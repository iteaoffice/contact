<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Acl\Assertion;

use Admin\Entity\Access;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

final class Address extends AbstractAssertion
{
    public function assert(
        Acl $acl,
        RoleInterface $role = null,
        ResourceInterface $address = null,
        $privilege = null
    ): bool {
        return $this->rolesHaveAccess(Access::ACCESS_OFFICE);
    }
}
