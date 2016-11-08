<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Contact\Entity;

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

/**
 * Phone.
 *
 * @ORM\Table(name="phone_type")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("phone_type")
 *
 * @category    Contact
 */
class PhoneType extends EntityAbstract
{
    const PHONE_TYPE_DIRECT = 1;
    const PHONE_TYPE_MOBILE = 2;
    const PHONE_TYPE_HOME = 3;
    const PHONE_TYPE_FAX = 4;
    /**
     * @ORM\Column(name="type_id", length=10, type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="type", type="string", length=20, nullable=false)
     *
     * @var string
     */
    private $type;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\Phone", cascade={"persist"}, mappedBy="type")
     * @Annotation\Exclude()
     *
     * @var \Contact\Entity\Phone[]
     */
    private $phone;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->address = new Collections\ArrayCollection();
    }

    /**
     * Static array for phone types to enable validation based on types.
     *
     * @return array
     */
    public static function getPhoneTypes()
    {
        return [
            self::PHONE_TYPE_DIRECT,
            self::PHONE_TYPE_MOBILE,
            self::PHONE_TYPE_FAX,
            self::PHONE_TYPE_HOME,
        ];
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
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();
            $inputFilter->add(
                $factory->createInput(
                    [
                        'name'       => 'type',
                        'required'   => true,
                        'filters'    => [
                            ['name' => 'StripTags'],
                            ['name' => 'StringTrim'],
                        ],
                        'validators' => [
                            [
                                'name'    => 'StringLength',
                                'options' => [
                                    'encoding' => 'UTF-8',
                                    'min'      => 1,
                                    'max'      => 100,
                                ],
                            ],
                        ],
                    ]
                )
            );
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
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
        return [
            'type' => $this->type,
        ];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->type;
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
     * @return \Contact\Entity\Phone[]
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param \Contact\Entity\Phone[] $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }
}
