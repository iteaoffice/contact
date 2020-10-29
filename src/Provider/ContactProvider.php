<?php

/**
 * Jield BV All rights reserved
 *
 * @author      Dr. ir. Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2004-2020 Jield BV (https://jield.nl)
 */

declare(strict_types=1);

namespace Contact\Provider;

use Contact\Entity;

final class ContactProvider
{
    public function generateArray(Entity\Contact $contact): array
    {
        return [
            'id'         => $contact->getHash(),
            'first_name' => $contact->getFirstName(),
            'last_name'  => $contact->getLastName(),
            'email'      => $contact->getEmail(),
        ];
    }
}
