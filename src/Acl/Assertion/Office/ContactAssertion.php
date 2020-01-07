<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Acl\Assertion\Office;

use Admin\Entity\Access;
use Contact\Acl\Assertion\AbstractAssertion;
use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

final class ContactAssertion extends AbstractAssertion
{
    public function assert(
        Acl               $acl,
        RoleInterface     $role = null,
        ResourceInterface $resource = null,
        $privilege = null
    ): bool {
        $this->setPrivilege($privilege);

        switch ($this->getPrivilege()) {
            case 'new':
            case 'list':
            case 'edit':
            case 'view':
                return $this->rolesHaveAccess(Access::ACCESS_MANAGEMENT_ASSISTANT);
            default:
                return false;
        }
    }
}
