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

namespace Contact\Hydrator;

use Contact\Entity\Address;
use Contact\Entity\AddressType;
use Contact\Entity\Contact;
use Contact\Entity\Phone;
use Contact\Entity\PhoneType;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use General\Entity\Country;
use Organisation\Service\OrganisationService;

/**
 * Class Profile.
 */
class Profile extends DoctrineObject
{
    /**
     * @param Contact $contact
     *
     * @return array
     */
    public function extract($contact): array
    {
        $this->prepare($contact);
        $values = $this->extractByValue($contact);
        unset($values['phone']);
        foreach ($contact->getPhone() as $phone) {
            $values['phone'][$phone->getType()->getId()]['phone'] = $phone->getPhone();
        }
        unset($values['address']);
        foreach ($contact->getAddress() as $address) {
            if ($address->getType()->getId() === AddressType::ADDRESS_TYPE_MAIL) {
                $values['address']['address'] = $address->getAddress();
                $values['address']['zipCode'] = $address->getZipCode();
                $values['address']['city'] = $address->getCity();
                $values['address']['country'] = $address->getCountry();
            }
        }

        unset($values['profile']);
        $values['profile']['visible'] = null !== $contact->getProfile() ? $contact->getProfile()->getVisible()
            : null;
        $values['profile']['id'] = null !== $contact->getProfile() ? $contact->getProfile()->getId()
            : null;
        $values['profile']['description'] = null !== $contact->getProfile() ? $contact->getProfile()->getDescription()
            : null;
        /*
         * Set the contact organisation, this will be taken from the contact_organisation item and can be used
         * to pre-fill the values
         */
        if ($contact->hasOrganisation()) {
            $values['contact_organisation']['organisation_id']
                = $contact->getContactOrganisation()->getOrganisation()->getId();
            $values['contact_organisation']['organisation']
                = OrganisationService::parseBranch(
                    $contact->getContactOrganisation()->getBranch(),
                    $contact->getContactOrganisation()->getOrganisation()
                );
            $values['contact_organisation']['type'] = $contact->getContactOrganisation()->getOrganisation()
                ->getType()->getId();
            $values['contact_organisation']['country'] = $contact->getContactOrganisation()->getOrganisation()
                ->getCountry()->getId();
        }

        return $values;
    }

    public function hydrate(array $data, $contact)
    {
        unset($data['contact_organisation']);

        $this->prepare($contact);
        /**
         * Reformat the phone, address and community for the Contact object
         *
         */
        if ($contact instanceof Contact) {
            /*
             * Reset the data array and store the values locally
             */
            $phoneData = $data['phone'];
            $data['phone'] = [];
            $addressInfo = $data['address'];
            $data['address'] = [];
            /**
             * @var $contact Contact
             */
            $contact = $this->hydrateByValue($data, $contact);
            /**
             * @var Phone[] $currentPhoneNumbers
             */
            $currentPhoneNumbers = $contact->getPhone()->getSnapshot();
            //Reset the array
            $contact->getPhone()->clear();
            foreach ($phoneData as $phoneTypeId => $phoneElement) {
                if (!empty($phoneElement['phone'])) {
                    $phone = new Phone();
                    /** @var PhoneType $phoneType */
                    $phoneType = $this->objectManager->getRepository(PhoneType::class)->find($phoneTypeId);
                    $phone->setType($phoneType);
                    $phone->setPhone($phoneElement['phone']);
                    $phone->setContact($contact);
                    $contact->getPhone()->add($phone);
                }
            }
            foreach ($currentPhoneNumbers as $phone) {
                if (!\in_array(
                    $phone->getType()->getId(),
                    [
                        PhoneType::PHONE_TYPE_MOBILE,
                        PhoneType::PHONE_TYPE_DIRECT,
                    ],
                    true
                )
                ) {
                    $contact->getPhone()->add($phone);
                }
            }


            //Find the Mail address
            $mailAddress = new Address();
            foreach ($contact->getAddress() as $address) {
                if ($address->getType()->getId() === AddressType::ADDRESS_TYPE_MAIL) {
                    $mailAddress = $address;
                }
            }


            if (array_key_exists(
                'address',
                $addressInfo
            )
                && !empty($addressInfo['address'])
                && !empty($addressInfo['country'])
            ) {

                /** @var AddressType $addressType */
                $addressType = $this->objectManager->getRepository(AddressType::class)
                    ->find(AddressType::ADDRESS_TYPE_MAIL);
                $mailAddress->setType($addressType);
                $mailAddress->setAddress($addressInfo['address']);
                $mailAddress->setZipCode($addressInfo['zipCode']);
                $mailAddress->setCity($addressInfo['city']);
                $mailAddress->setContact($contact);
                /** @var Country $country */
                $country = $this->objectManager->getRepository(Country::class)->find($addressInfo['country']);
                $mailAddress->setCountry($country);

                //Save the address
                $this->objectManager->persist($mailAddress);

                $contact->getAddress()->add($mailAddress);
            }

            $contact->getProfile()->setContact($contact);

            return $contact;
        }

        return $this->hydrateByValue($data, $contact);
    }
}
