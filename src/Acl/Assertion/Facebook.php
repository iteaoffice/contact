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
use Contact\Entity\Facebook as FacebookEntity;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

class Facebook extends AssertionAbstract
{
    /**
     * Returns true if and only if the assertion conditions are met.
     *
     * This method is passed the ACL, Role, Resource, and privilege to which the authorization query applies. If the
     * $role, $resource, or $privilege parameters are null, it means that the query applies to all Roles, Resources, or
     * privileges, respectively.
     *
     * @param Acl $acl
     * @param RoleInterface $role
     * @param ResourceInterface $facebook
     * @param string $privilege
     *
     * @return bool
     */
    public function assert(
        Acl $acl,
        RoleInterface $role = null,
        ResourceInterface $facebook = null,
        $privilege = null
    ): bool {
        /*
         * A meeting can be shown when we have a contact
         */
        if (strpos($this->getRouteMatch()->getMatchedRouteName(), 'zfcadmin')) {
            return $this->rolesHaveAccess(Access::ACCESS_OFFICE);
        }

        $this->setPrivilege($privilege);
        $id = $this->getId();

        if (!$facebook instanceof FacebookEntity && !\is_null($id)) {
            /** @var FacebookEntity $facebook */
            $facebook = $this->getContactService()->findEntityById(FacebookEntity::class, $id);
        }

        if (!$facebook instanceof FacebookEntity && $facebook = $this->getRouteMatch()->getParam('facebook')) {
            /** @var FacebookEntity $facebook */
            $facebook = $this->getContactService()->findEntityById(FacebookEntity::class, $facebook);
        }

        switch ($this->getPrivilege()) {
            case 'facebook':
            case 'view':
                if ($facebook->getPublic() === FacebookEntity::IS_PUBLIC) {
                    return true;
                }

                return $this->rolesHaveAccess($facebook->getAccess()->toArray());
            case 'send-message':
                return $facebook->getCanSendMessage() === FacebookEntity::CAN_SEND_MESSAGE
                    && $this->getContactService()->isContactInFacebook($this->getContact(), $facebook);
            default:
                return $this->rolesHaveAccess(Access::ACCESS_OFFICE);
        }
    }
}
