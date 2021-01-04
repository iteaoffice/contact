<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\Service;

use Contact\Entity\Address;
use Contact\Entity\AddressType;
use Contact\Entity\Contact;

/**
 * Class AddressService
 *
 * @package Contact\Service
 */
class AddressService extends AbstractService
{
    public function findAddressById(int $id): ?Address
    {
        return $this->entityManager->getRepository(Address::class)->find($id);
    }

    public function findAddressByContactAndType(Contact $contact, AddressType $type): ?Address
    {
        /** @var \Contact\Repository\Address $repository */
        $repository = $this->entityManager->getRepository(Address::class);

        return $repository->findAddressByContactAndType($contact, $type);
    }
}
