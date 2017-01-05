<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

namespace Contact\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

/**
 * AddressTypeSort.
 *
 * @ORM\Table(name="address_type_sort")
 * @ORM\Entity
 */
class AddressTypeSort extends EntityAbstract
{
    const ADDRESS_TYPE_MAIL = 1;
    const ADDRESS_TYPE_VISIT = 2;
    const ADDRESS_TYPE_FINANCIAL = 3;
    const ADDRESS_TYPE_HOME = 4;
    /**
     * @ORM\Column(name="sort_id", length=10, type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var integer
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="AddressType", cascade={"persist"}, inversedBy="sort")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="main_type_id", referencedColumnName="type_id", nullable=false)
     * })
     *
     * @var \Contact\Entity\AddressType
     */
    private $mainType;
    /**
     * @ORM\ManyToOne(targetEntity="AddressType", cascade={"persist"}, inversedBy="subSort")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="sub_type_id", referencedColumnName="type_id", nullable=false)
     * })
     *
     * @var \Contact\Entity\AddressType
     */
    private $subType;
    /**
     * @ORM\Column(name="sort", length=10, type="integer", nullable=true)
     *
     * @var string
     */
    private $sort;

    /**
     * Class constructor.
     */
    public function __construct()
    {
    }

    /**
     * Magic Getter.
     *
     * @param $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        return $this->$property;
    }

    /**
     * Magic Setter.
     *
     * @param $property
     * @param $value
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * Set input filter.
     *
     * @param InputFilterInterface $inputFilter
     *
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Setting an inputFilter is currently not supported");
    }

    /**
     * @return \Zend\InputFilter\InputFilter|\Zend\InputFilter\InputFilterInterface
     */
    public function getInputFilter()
    {
        return new InputFilter();
    }

    public function populate()
    {
        return $this->getArrayCopy();
    }

    /**
     * Needed for the hydration of form elements.
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return array(
            'sort' => $this->sort,
        );
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
     * @return \Contact\Entity\AddressType
     */
    public function getMainType()
    {
        return $this->mainType;
    }

    /**
     * @param \Contact\Entity\AddressType $mainType
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
     * @return \Contact\Entity\AddressType
     */
    public function getSubType()
    {
        return $this->subType;
    }

    /**
     * @param \Contact\Entity\AddressType $subType
     */
    public function setSubType($subType)
    {
        $this->subType = $subType;
    }
}
