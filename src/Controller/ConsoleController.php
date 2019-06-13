<?php

/**
 * Jield copyright message placeholder.
 *
 * @category  Contact
 *
 * @author    Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright @copyright Copyright (c) 2004-2017 ITEA Office (http://itea3.org)
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
    /**
     * @var ContactService;
     */
    private $contactService;

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
