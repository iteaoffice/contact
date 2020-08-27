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

use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Resource\GenericResource;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

final class Profile extends AbstractAssertion
{
    public function assert(
        Acl $acl,
        RoleInterface $role = null,
        ResourceInterface $resource = null,
        $privilege = null
    ): bool {
        $this->setPrivilege($privilege);

        /** @var \Contact\Entity\Contact $contact */
        $contact = $resource;

        switch ($this->getPrivilege()) {
            case 'contact':
                if ($contact instanceof GenericResource) {
                    $contact = $this->contactService->findContactByHash($this->getRouteMatch()->getParam('hash'));
                }

                return $contact->isVisibleInCommunity();

            case 'send-message':
                return $this->hasContact();
        }
    }
}
