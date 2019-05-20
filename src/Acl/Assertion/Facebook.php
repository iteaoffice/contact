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

final class Facebook extends AbstractAssertion
{
    public function assert(
        Acl $acl,
        RoleInterface $role = null,
        ResourceInterface $facebook = null,
        $privilege = null
    ): bool {
        if (strpos($this->getRouteMatch()->getMatchedRouteName(), 'zfcadmin')) {
            return $this->rolesHaveAccess(Access::ACCESS_OFFICE);
        }

        $this->setPrivilege($privilege);
        $id = $this->getId();

        if (!$facebook instanceof FacebookEntity && null !== $id) {
            /** @var FacebookEntity $facebook */
            $facebook = $this->contactService->find(FacebookEntity::class, $id);
        }

        if (!$facebook instanceof FacebookEntity && $facebook = $this->getRouteMatch()->getParam('facebook')) {
            /** @var FacebookEntity $facebook */
            $facebook = $this->contactService->find(FacebookEntity::class, (int)$facebook);
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
                    && $this->contactService->isContactInFacebook($this->contact, $facebook);
            default:
                return $this->rolesHaveAccess(Access::ACCESS_OFFICE);
        }
    }
}
