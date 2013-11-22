<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Contact
 * @package     Service
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
namespace Contact\Service;

use Contact\Entity\Address;
use Contact\Entity\AddressType;
use Contact\Entity\Contact;

/**
 * AddressService
 *
 * this is a generic wrapper service for all the other services
 *
 * First parameter of all methods (lowercase, underscore_separated)
 * will be used to fetch the correct model service, one exception is the 'linkModel'
 * method.
 *
 */
class AddressService extends ServiceAbstract
{
    /**
     * @var Address
     */
    protected $address;

    /** @param int $id
     *
     * @return AddressService;
     */
    public function setAddressId($id)
    {
        $this->setAddress($this->findEntityById('address', $id));

        return $this;
    }

    /**
     * Returns the address of a contact, where the addressTypeSort table is used to find alternative addresses
     *
     * @param Contact $contact
     * @param         $type
     *
     * @return AddressService;
     */
    public function findAddressByContactAndType(Contact $contact, $type)
    {
        $address = $this->getEntityManager()->getRepository(
            $this->getFullEntityName('address')
        )->findAddressByContactAndType($contact, $type);

        return $this->createServiceElement($address);
    }

    /**
     * @param Address $address
     *
     * @return AddressService
     */
    private function createServiceElement(Address $address)
    {
        $addressService = new self();
        $addressService->setServiceLocator($this->getServiceLocator());
        $addressService->setAddress($address);

        return $addressService;
    }

    /**
     * @param \Contact\Entity\Address $address
     *
     * @return AddressService;
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return \Contact\Entity\Address
     */
    public function getAddress()
    {
        return $this->address;
    }
}