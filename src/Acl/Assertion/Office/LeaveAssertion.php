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
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

final class LeaveAssertion extends AbstractAssertion
{
    public function assert(
        Acl               $acl,
        RoleInterface     $role = null,
        ResourceInterface $resource = null,
        $privilege = null
    ): bool {
        $this->setPrivilege($privilege);

        /** @var Leave $leave */
        $leave = $resource;

        switch ($this->getPrivilege()) {
            case 'new':
            case 'list':
                return $this->rolesHaveAccess(Access::ACCESS_OFFICE);
            case 'edit':
            case 'view':
                if ($this->rolesHaveAccess(Access::ACCESS_MANAGEMENT_ASSISTANT)) {
                    return true;
                }
                return $this->rolesHaveAccess(Access::ACCESS_OFFICE)
                    && ($leave->getOfficeContact()->getContact() === $this->contact);
            default:
                return false;
        }
    }
}
