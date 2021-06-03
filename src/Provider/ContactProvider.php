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
use Contact\Service\AddressService;

/**
 * Class ContactProvider
 * @package Contact\Provider
 */
class ContactProvider
{
    private AddressService $addressService;

    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    public function generateArray(Entity\Contact $contact): array
    {
        /** @var Entity\AddressType $mailAddress */
        $mailAddress = $this->addressService->find(\Contact\Entity\AddressType::class, \Contact\Entity\AddressType::ADDRESS_TYPE_MAIL);
        $address     = $this->addressService->findAddressByContactAndType($contact, $mailAddress);

        $addressData = [];
        if (null !== $address) {
            $addressData = [
                'address' => [
                    'address'  => $address->getAddress(),
                    'city'     => $address->getCity(),
                    'zip_code' => $address->getZipCode(),
                    'country'  => $address->getCountry()->getCd(),
                ]
            ];
        }

        $funderData = [];
        if ($contact->isFunder()) {
            $funderData = [
                'funder' => [
                    'country' => $contact->getFunder()->getCountry()->getIso3()
                ]
            ];
        }

        return array_merge([
            'id'         => $contact->getId(),
            'cluster'    => 'itea',
            'first_name' => $contact->getFirstName(),
            'last_name'  => trim(implode(' ', [$contact->getMiddleName(), $contact->getLastName()])),
            'email'      => $contact->getEmail(),
            'is_funder'  => $contact->isFunder(),
        ], $addressData, $funderData);
    }
}
