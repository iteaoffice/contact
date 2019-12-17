<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Entity;

use Doctrine\ORM\Mapping as ORM;
use General\Entity\Country;
use Zend\Form\Annotation;

/**
 * @ORM\Table(name="contact_address")
 * @ORM\Entity(repositoryClass="Contact\Repository\Address")
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("contact_address")
 */
class Address extends AbstractEntity
{
    /**
     * @ORM\Column(name="address_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="address", type="string", length=1000, nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Textarea")
     * @Annotation\Options({"label":"txt-address"})
     *
     * @var string
     */
    private $address;
    /**
     * @ORM\Column(name="zipcode", type="string", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-zip-code"})
     *
     * @var string
     */
    private $zipCode;
    /**
     * @ORM\Column(name="city", type="string", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-city"})
     *
     * @var string
     */
    private $city;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\AddressType", cascade={"persist"}, inversedBy="address")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="type_id", nullable=false)
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({"target_class":"Contact\Entity\AddressType"})
     * @Annotation\Attributes({"label":"txt-type"})
     *
     * @var AddressType
     */
    private $type;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", cascade={"persist"}, inversedBy="address")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")
     * @Annotation\Type("\Zend\Form\Element\Hidden")
     *
     * @var Contact
     */
    private $contact;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\Country", cascade={"persist"}, inversedBy="address")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="country_id", nullable=false)
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({
     *      "target_class":"General\Entity\Country",
     *      "find_method":{
     *          "name":"findForForm",
     *          "params": {
     *              "criteria":{},
     *              "orderBy":{}
     *          }}
     *      }
     * )
     * @Annotation\Attributes({"label":"txt-country"})
     *
     * @var Country
     */
    private $country;

    public function __toString(): string
    {
        return (string)$this->getAddress();
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): Address
    {
        $this->address = $address;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'address' => $this->address,
            'zipCode' => $this->zipCode,
            'city'    => $this->city,
            'country' => $this->country->getId()
        ];
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): Address
    {
        $this->id = $id;
        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(?string $zipCode): Address
    {
        $this->zipCode = $zipCode;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): Address
    {
        $this->city = $city;
        return $this;
    }

    public function getType(): ?AddressType
    {
        return $this->type;
    }

    public function setType(AddressType $type): Address
    {
        $this->type = $type;
        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(Contact $contact): Address
    {
        $this->contact = $contact;
        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): Address
    {
        $this->country = $country;
        return $this;
    }
}
