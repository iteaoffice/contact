<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

namespace Contact\Service;

use Contact\Entity\Address;
use Contact\Entity\AddressType;
use Contact\Entity\Contact;

/**
 * AddressService.
 *
 * this is a generic wrapper service for all the other services
 *
 * First parameter of all methods (lowercase, underscore_separated)
 * will be used to fetch the correct model service, one exception is the 'linkModel'
 * method.
 */
class AddressService extends ServiceAbstract
{

    /**
     * @param $id
     *
     * @return null|Address
     */
    public function findAddressById($id)
    {
        return $this->getEntityManager()->getRepository(Address::class)->find($id);
    }

    /**
     * Returns the address of a contact, where the addressTypeSort table is used to find alternative addresses.
     *
     * @param Contact     $contact
     * @param AddressType $type
     *
     * @return Address|null
     */
    public function findAddressByContactAndType(Contact $contact, AddressType $type)
    {
        /** @var \Contact\Repository\Address $repository */
        $repository = $this->getEntityManager()->getRepository(Address::class);

        return $repository->findAddressByContactAndType($contact, $type);
    }
}
