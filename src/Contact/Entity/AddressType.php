<?php
/**
 * Debranova copyright message placeholder
 *
 * @category    Contact
 * @package     Entity
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 Debranova
 */
namespace Contact\Entity;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\Factory as InputFactory;
use Zend\Form\Annotation;
use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;

/**
 * AddressType
 *
 * @ORM\Table(name="address_type")
 * @ORM\Entity
 */
class AddressType extends EntityAbstract
{
    const ADDRESS_TYPE_MAIL = 1;
    const ADDRESS_TYPE_VISIT = 2;
    const ADDRESS_TYPE_FINANCIAL = 3;
    const ADDRESS_TYPE_HOME = 4;
    /**
     * @ORM\Column(name="type_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="type", type="string", length=20, nullable=false)
     * @var string
     */
    private $type;
    /**
     * @ORM\OneToMany(targetEntity="\Contact\Entity\Address", cascade={"persist"}, mappedBy="type")
     * @Annotation\Exclude()
     * @var \Contact\Entity\Address[]
     */
    private $address;
    /**
     * @ORM\OneToMany(targetEntity="\Contact\Entity\AddressTypeSort", cascade={"persist"}, mappedBy="mainType")
     * @Annotation\Exclude()
     * @var \Contact\Entity\AddressTypeSort[]
     */
    private $sort;
    /**
     * @ORM\OneToMany(targetEntity="\Contact\Entity\AddressTypeSort", cascade={"persist"}, mappedBy="subType")
     * @Annotation\Exclude()
     * @var \Contact\Entity\AddressTypeSort[]
     */
    private $subSort;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->address = new Collections\ArrayCollection();
    }

    /**
     * Magic Getter
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
     * Magic Setter
     *
     * @param $property
     * @param $value
     *
     * @return void
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * toString returns the name
     *
     * @return string
     */
    public function __toString()
    {
        return $this->type;
    }

    /**
     * Set input filter
     *
     * @param InputFilterInterface $inputFilter
     *
     * @return void
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
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'       => 'type',
                        'required'   => true,
                        'filters'    => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name'    => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'min'      => 1,
                                    'max'      => 100,
                                ),
                            ),
                        ),
                    )
                )
            );

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    /**
     * Needed for the hydration of form elements
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return array(
            'type' => $this->type,
        );
    }

    public function populate()
    {
        return $this->getArrayCopy();
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \Contact\Entity\Address[] $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return \Contact\Entity\Address[]
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
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
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param \Contact\Entity\AddressTypeSort[] $subSort
     */
    public function setSubSort($subSort)
    {
        $this->subSort = $subSort;
    }

    /**
     * @return \Contact\Entity\AddressTypeSort[]
     */
    public function getSubSort()
    {
        return $this->subSort;
    }
}
