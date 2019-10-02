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

/**
 * AddressTypeSort.
 *
 * @ORM\Table(name="address_type_sort")
 * @ORM\Entity
 */
class AddressTypeSort extends AbstractEntity
{
    public const ADDRESS_TYPE_MAIL = 1;
    public const ADDRESS_TYPE_VISIT = 2;
    public const ADDRESS_TYPE_FINANCIAL = 3;
    public const ADDRESS_TYPE_HOME = 4;
    /**
     * @ORM\Column(name="sort_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="AddressType", cascade={"persist"}, inversedBy="sort")
     * @ORM\JoinColumn(name="main_type_id", referencedColumnName="type_id", nullable=false)
     *
     * @var AddressType
     */
    private $mainType;
    /**
     * @ORM\ManyToOne(targetEntity="AddressType", cascade={"persist"}, inversedBy="subSort")
     * @ORM\JoinColumn(name="sub_type_id", referencedColumnName="type_id", nullable=false)
     *
     * @var AddressType
     */
    private $subType;
    /**
     * @ORM\Column(name="sort", type="integer", nullable=true)
     *
     * @var string
     */
    private $sort;

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
     * @return AddressType
     */
    public function getMainType()
    {
        return $this->mainType;
    }

    /**
     * @param AddressType $mainType
     */
    public function setMainType($mainType)
    {
        $this->mainType = $mainType;
    }

    /**
     * @return string
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param string $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     * @return AddressType
     */
    public function getSubType()
    {
        return $this->subType;
    }

    /**
     * @param AddressType $subType
     */
    public function setSubType($subType)
    {
        $this->subType = $subType;
    }
}
