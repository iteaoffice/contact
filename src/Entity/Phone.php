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

namespace Contact\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 * Phone.
 *
 * @ORM\Table(name="contact_phone")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("contact_phone")
 *
 * @category    Contact
 */
class Phone extends AbstractEntity
{
    /**
     * @ORM\Column(name="phone_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Type("\Zend\Form\Element\Hidden")
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="phone", type="string", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-phone-number"})
     *
     * @var string
     */
    private $phone;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", cascade={"persist"}, inversedBy="phone")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=false)
     * })
     *
     * @var \Contact\Entity\Contact
     */
    private $contact;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\PhoneType", cascade={"persist"}, inversedBy="phone")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="type_id", referencedColumnName="type_id", nullable=false)
     * })
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({"target_class":"Contact\Entity\PhoneType"})
     * @Annotation\Attributes({"label":"txt-type"})
     *
     * @var \Contact\Entity\PhoneType
     */
    private $type;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    public function __isset($property)
    {
        return isset($this->$property);
    }

    public function __toString(): string
    {
        return (string)$this->phone;
    }

    /**
     * @return \Contact\Entity\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param \Contact\Entity\Contact $contact
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return \Contact\Entity\PhoneType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param \Contact\Entity\PhoneType $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
}
