<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

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
     * @param Acl               $acl
     * @param RoleInterface     $role
     * @param ResourceInterface $facebook
     * @param string            $privilege
     *
     * @return bool
     */
    public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $facebook = null, $privilege = null)
    {
        /*
         * A meeting can be shown when we have a contact
         */
        if (strpos($this->getRouteMatch()->getMatchedRouteName(), 'zfcadmin')) {
            return $this->rolesHaveAccess(Access::ACCESS_OFFICE);
        }

        $id = $this->getRouteMatch()->getParam('id');
        /*
         * When the privilege is_null (not given by the isAllowed helper), we cannot grab it from the
         * routeMatch, but we assume that we are viewing an idea
         */
        if (is_null($privilege)) {
            $privilege = $this->getRouteMatch()->getParam('privilege', 'view');
        }
        if (!$facebook instanceof FacebookEntity && !is_null($id)) {
            /*
             * @var FacebookEntity
             */
            $facebook = $this->getContactService()->findEntityById('facebook', $id);
        }

        switch ($privilege) {
            case 'view':
                if ($facebook->getPublic() === FacebookEntity::IS_PUBLIC) {
                    return true;
                }

                return $this->rolesHaveAccess($facebook->getAccess()->toArray());
            case 'send-message':
                return $facebook->getCanSendMessage() === FacebookEntity::CAN_SEND_MESSAGE &&
                $this->getContactService()->findContactInFacebook($facebook);
            default:
                return $this->rolesHaveAccess(strtolower(Access::ACCESS_OFFICE));
        }
    }
}
