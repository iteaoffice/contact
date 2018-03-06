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

namespace Contact\Service;

use Contact\Entity\Address;
use Contact\Entity\AddressType;
use Contact\Entity\Contact;

/**
 * Class AddressService
 *
 * @package Contact\Service
 */
class AddressService extends ServiceAbstract
{
    /**
     * @param $id
     *
     * @return Address|null|object
     */
    public function findAddressById($id)
    {
        return $this->getEntityManager()->getRepository(Address::class)->find($id);
    }

    /**
     * Returns the address of a contact, where the addressTypeSort table is used to find alternative addresses.
     *
     * @param Contact $contact
     * @param AddressType $type
     *
     * @return Address|null
     */
    public function findAddressByContactAndType(Contact $contact, AddressType $type):?Address
    {
        /** @var \Contact\Repository\Address $repository */
        $repository = $this->getEntityManager()->getRepository(Address::class);

        return $repository->findAddressByContactAndType($contact, $type);
    }
}
