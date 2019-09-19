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

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 * AddressType.
 *
 * @ORM\Table(name="address_type")
 * @ORM\Entity
 */
class AddressType extends AbstractEntity
{
    public const ADDRESS_TYPE_MAIL = 1;
    public const ADDRESS_TYPE_VISIT = 2;
    public const ADDRESS_TYPE_FINANCIAL = 3;
    public const ADDRESS_TYPE_HOME = 4;
    public const ADDRESS_TYPE_BOOTH_FINANCIAL = 5;
    /**
     * @ORM\Column(name="type_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="type", type="string", nullable=false)
     *
     * @var string
     */
    private $type;
    /**
     * @ORM\OneToMany(targetEntity="\Contact\Entity\Address", cascade={"persist"}, mappedBy="type")
     * @Annotation\Exclude()
     *
     * @var \Contact\Entity\Address[]
     */
    private $address;
    /**
     * @ORM\OneToMany(targetEntity="\Contact\Entity\AddressTypeSort", cascade={"persist"}, mappedBy="mainType")
     * @Annotation\Exclude()
     *
     * @var \Contact\Entity\AddressTypeSort[]
     */
    private $sort;
    /**
     * @ORM\OneToMany(targetEntity="\Contact\Entity\AddressTypeSort", cascade={"persist"}, mappedBy="subType")
     * @Annotation\Exclude()
     *
     * @var \Contact\Entity\AddressTypeSort[]
     */
    private $subSort;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->address = new Collections\ArrayCollection();
    }

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
        return $this->type;
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
     * @return \Contact\Entity\Address[]
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param \Contact\Entity\Address[] $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return \Contact\Entity\AddressTypeSort[]
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param \Contact\Entity\AddressTypeSort[] $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     * @return \Contact\Entity\AddressTypeSort[]
     */
    public function getSubSort()
    {
        return $this->subSort;
    }

    /**
     * @param \Contact\Entity\AddressTypeSort[] $subSort
     */
    public function setSubSort($subSort)
    {
        $this->subSort = $subSort;
    }
}
