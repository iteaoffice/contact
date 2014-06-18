<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     Hydrator
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Contact\Hydrator;

use Contact\Entity\Address;
use Contact\Entity\AddressType;
use Contact\Entity\Community;
use Contact\Entity\Contact;
use Contact\Entity\Phone;
use Contact\Entity\PhoneType;
use Contact\Service\ContactService;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Organisation\Service\OrganisationService;

/**
 * Class Profile
 * @package Contact\Hydrator
 */
class Profile extends DoctrineObject
{
    /**
     * @param Contact $object
     *
     * @return array
     */
    public function extract($object)
    {
        $this->prepare($object);
        $values = $this->extractByValue($object);
        unset($values['phone']);
        foreach ($object->getPhone() as $phone) {
            $values['phone'][$phone->getType()->getId()]['phone'] = $phone->getPhone();
        }
        unset($values['address']);
        foreach ($object->getAddress() as $address) {
            if ($address->getType()->getId() === AddressType::ADDRESS_TYPE_MAIL) {
                $values['address']['address'] = $address->getAddress();
                $values['address']['zipCode'] = $address->getZipCode();
                $values['address']['city']    = $address->getCity();
                $values['address']['country'] = $address->getCountry();
            }
        }
        unset($values['community']);
        foreach ($object->getCommunity() as $community) {
            $values['community'][$community->getType()->getId()]['community'] = $community->getCommunity();
        }
        unset($values['profile']);
        $values['profile']['visible']     = !is_null($object->getProfile()) ? $object->getProfile()->getVisible(
        ) : null;
        $values['profile']['description'] = !is_null($object->getProfile()) ? $object->getProfile()->getDescription(
        ) : null;
        /**
         * Set the contact organisation
         */
        $contactService = new ContactService();
        $contactService->setContact($object);
        if (!is_null($object->getContactOrganisation())) {
            $organisationService = new OrganisationService();
            $organisationService->setOrganisation($object->getContactOrganisation()->getOrganisation());
            $values['contact_organisation']['organisation'] = $organisationService->parseOrganisationWithBranch(
                $contactService->getContact()->getContactOrganisation()->getBranch()
            );
            if (!is_null($object->getContactOrganisation())) {
                $values['contact_organisation']['country'] =
                    $object->getContactOrganisation()->getOrganisation()->getCountry()->getId();
            }
        }

        return $values;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param array   $data
     * @param Contact $object
     *
     * @return object
     */
    public function hydrate(array $data, $object)
    {
        $this->prepare($object);
        /**
         * Reformat the phone, address and community for the Contact object
         */
        if ($object instanceof Contact) {
            /**
             * Reset the data array and store the values locally
             */
            $phoneData           = $data['phone'];
            $data['phone']       = [];
            $addressInfo         = $data['address'];
            $data['address']     = [];
            $communityData       = $data['community'];
            $data['community']   = [];
            $contact             = $this->hydrateByValue($data, $object);
            $currentPhoneNumbers = $contact->getPhone()->getSnapshot();
            //Reset the array
            $contact->getPhone()->clear();
            foreach ($phoneData as $phoneTypeId => $phoneElement) {
                if (!empty($phoneElement['phone'])) {
                    $phone = new Phone();
                    $phone->setType($this->objectManager->getReference('Contact\Entity\PhoneType', $phoneTypeId));
                    $phone->setPhone($phoneElement['phone']);
                    $phone->setContact($contact);
                    $contact->getPhone()->add($phone);
                }
            }
            foreach ($currentPhoneNumbers as $phone) {
                if (!in_array(
                    $phone->getType()->getId(),
                    array(
                        PhoneType::PHONE_TYPE_MOBILE,
                        PhoneType::PHONE_TYPE_DIRECT
                    )
                )
                ) {
                    $contact->getPhone()->add($phone);
                }
            }
            $currentAddress = $contact->getAddress()->getSnapshot();
            /**
             * Reformat the address
             */
            $contact->getAddress()->clear();
            if (array_key_exists('address', $addressInfo)) {
                if (!empty($addressInfo['address'])) {
                    $address = new Address();
                    $address->setType(
                        $this->objectManager->getReference('Contact\Entity\AddressType', AddressType::ADDRESS_TYPE_MAIL)
                    );
                    $address->setAddress($addressInfo['address']);
                    $address->setZipCode($addressInfo['zipCode']);
                    $address->setCity($addressInfo['city']);
                    $address->setContact($contact);
                    $address->setCountry(
                        $this->objectManager->getReference('General\Entity\Country', $addressInfo['country'])
                    );
                    $contact->getAddress()->add($address);
                }
            }
            foreach ($currentAddress as $address) {
                if (!in_array($address->getType()->getId(), array(AddressType::ADDRESS_TYPE_MAIL))) {
                    $contact->getAddress()->add($address);
                }
            }
            /**
             * Reformat the community
             */
            $contact->getCommunity()->clear();
            foreach ($communityData as $communityTypeId => $communityInfo) {
                if (!empty($communityInfo['community'])) {
                    $community = new Community();
                    $community->setType(
                        $this->objectManager->getReference('General\Entity\CommunityType', $communityTypeId)
                    );
                    $community->setCommunity($communityInfo['community']);
                    $community->setContact($contact);
                    $contact->getCommunity()->add($community);
                }
            }
            $contact->getProfile()->setContact($contact);

            return $contact;
        }

        return $this->hydrateByValue($data, $object);
    }
}
