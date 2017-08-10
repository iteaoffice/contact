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
     * @ORM\Column(name="type_id", type="integer", nullable=false)
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
    }

    /**
     * Static array for phone types to enable validation based on types.
     *
     * @return array
     */
    public static function getPhoneTypes(): array
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
     * @param $property
     * @return bool
     */
    public function __isset($property)
    {
        return isset($this->$property);
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
