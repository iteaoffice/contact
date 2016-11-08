<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

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
    public function extract($contact)
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
                $values['address']['city']    = $address->getCity();
                $values['address']['country'] = $address->getCountry();
            }
        }

        unset($values['profile']);
        $values['profile']['visible']     = ! is_null($contact->getProfile()) ? $contact->getProfile()->getVisible()
            : null;
        $values['profile']['description'] = ! is_null($contact->getProfile()) ? $contact->getProfile()->getDescription()
            : null;
        /*
         * Set the contact organisation, this will be taken from the contact_organisation item and can be used
         * to pre-fill the values
         */
        $organisationService = new OrganisationService();
        if (! is_null($contact->getContactOrganisation())) {
            $values['contact_organisation']['organisation_id'] = $contact->getContactOrganisation()->getOrganisation()
                ->getId();
            $values['contact_organisation']['organisation']
                                                               = $organisationService->parseOrganisationWithBranch(
                                                                   $contact->getContactOrganisation()->getBranch(),
                                                                   $contact->getContactOrganisation()->getOrganisation()
                                                               );
            $values['contact_organisation']['type']            = $contact->getContactOrganisation()->getOrganisation()
                ->getType()->getId();
            if (! is_null($contact->getContactOrganisation())) {
                $values['contact_organisation']['country'] = $contact->getContactOrganisation()->getOrganisation()
                    ->getCountry()->getId();
            }
        }

        return $values;
    }

    /**
     * Hydrate $contact with the provided $data.
     *
     * @param array   $data
     * @param Contact $contact
     *
     * @return object
     */
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
            $phoneData       = $data['phone'];
            $data['phone']   = [];
            $addressInfo     = $data['address'];
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
                if (! empty($phoneElement['phone'])) {
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
                if (! in_array(
                    $phone->getType()->getId(),
                    [
                        PhoneType::PHONE_TYPE_MOBILE,
                        PhoneType::PHONE_TYPE_DIRECT,
                    ]
                )
                ) {
                    $contact->getPhone()->add($phone);
                }
            }
            /** @var Address[] $currentAddress */
            $currentAddress = $contact->getAddress()->getSnapshot();
            /*
             * Reformat the address
             */
            $contact->getAddress()->clear();
            if (array_key_exists('address', $addressInfo)) {
                if (! empty($addressInfo['address'])) {
                    $address = new Address();
                    /** @var AddressType $addressType */
                    $addressType = $this->objectManager->getRepository(AddressType::class)
                        ->find(AddressType::ADDRESS_TYPE_MAIL);
                    $address->setType($addressType);
                    $address->setAddress($addressInfo['address']);
                    $address->setZipCode($addressInfo['zipCode']);
                    $address->setCity($addressInfo['city']);
                    $address->setContact($contact);
                    /** @var Country $country */
                    $country = $this->objectManager->getRepository(Country::class)->find($addressInfo['country']);
                    $address->setCountry($country);
                    $contact->getAddress()->add($address);
                }
            }
            foreach ($currentAddress as $address) {
                if (! in_array($address->getType()->getId(), [AddressType::ADDRESS_TYPE_MAIL])) {
                    $contact->getAddress()->add($address);
                }
            }

            $contact->getProfile()->setContact($contact);

            return $contact;
        }

        return $this->hydrateByValue($data, $contact);
    }
}
