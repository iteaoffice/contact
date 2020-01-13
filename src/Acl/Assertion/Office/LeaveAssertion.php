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
use Contact\Entity\Office\Leave;
use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

final class LeaveAssertion extends AbstractAssertion
{
    public function assert(
        Acl $acl,
        RoleInterface $role = null,
        ResourceInterface $resource = null,
        $privilege = null
    ): bool {
        $this->setPrivilege($privilege);

        /** @var Leave $leave */
        $leave = $resource;

        if ($this->rolesHaveAccess(Access::ACCESS_MANAGEMENT_ASSISTANT)) {
            return true;
        }

        switch ($this->getPrivilege()) {
            case 'new':
            case 'list':
                return $this->rolesHaveAccess(Access::ACCESS_OFFICE);
            case 'edit':
            case 'view':
                return ($leave->getOfficeContact()->getContact() === $this->contact);
            default:
                return false;
        }
    }
}