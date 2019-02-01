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

namespace Contact\Acl\Assertion;

use Admin\Entity\Access;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

class Contact extends AssertionAbstract
{
    public function assert(
        Acl $acl,
        RoleInterface $role = null,
        ResourceInterface $resource = null,
        $privilege = null
    ): bool {
        $this->setPrivilege($privilege);

        if (strpos($this->getRouteMatch()->getMatchedRouteName(), 'zfcadmin')) {
            return $this->rolesHaveAccess(Access::ACCESS_OFFICE);
        }

        switch ($this->getPrivilege()) {
            case 'view-admin':
            case 'edit-admin':
            case 'impersonate':
            case 'new':
            case 'permit':
            case 'list-duplicate':
            case 'list-inactive':
            case 'add-project':
                return $this->rolesHaveAccess(Access::ACCESS_OFFICE);
            default:
                return $this->hasContact();
        }
    }
}
