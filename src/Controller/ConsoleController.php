<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\Controller;

use Contact\Service\ContactService;

/**
 * Class ConsoleController
 *
 * @package Contact\Controller
 */
final class ConsoleController extends ContactAbstractController
{
    private ContactService $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    public function resetAccessRolesAction(): void
    {
        $this->contactService->resetAccessRoles();
    }

    public function contactCleanupAction(): void
    {
        $this->contactService->removeInactiveContacts();
    }
}
