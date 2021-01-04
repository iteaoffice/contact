<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\Acl\Assertion;

use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Resource\GenericResource;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

final class Contact extends AbstractAssertion
{
    public function assert(
        Acl $acl,
        RoleInterface $role = null,
        ResourceInterface $resource = null,
        $privilege = null
    ): bool {
        $this->setPrivilege($privilege);

        if (strpos($this->getRouteMatch()->getMatchedRouteName(), 'zfcadmin')) {
            return $this->rolesHaveAccess('office');
        }

        /** @var \Contact\Entity\Contact $contact */
        $contact = $resource;

        switch ($this->getPrivilege()) {
            case 'view-admin':
            case 'edit-admin':
            case 'impersonate':
            case 'new':
            case 'permit':
            case 'list-duplicate':
            case 'list-inactive':
            case 'add-project':
                return $this->rolesHaveAccess('office');
            case 'contact':
            case 'send-message':
                if ($contact instanceof GenericResource) {
                    $contact = $this->contactService->findContactByHash($this->getRouteMatch()->getParam('hash'));
                }

                return $contact->isVisibleInCommunity();
            default:
                return $this->hasContact();
        }
    }
}
