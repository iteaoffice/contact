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
use Laminas\Form\Annotation;

/**
 * Phone.
 *
 * @ORM\Table(name="phone_type")
 * @ORM\Entity
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectProperty")
 * @Annotation\Name("phone_type")
 *
 * @category    Contact
 */
class PhoneType extends AbstractEntity
{
    public const PHONE_TYPE_DIRECT = 1;
    public const PHONE_TYPE_MOBILE = 2;
    public const PHONE_TYPE_HOME = 3;
    public const PHONE_TYPE_FAX = 4;
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
     * @ORM\OneToMany(targetEntity="Contact\Entity\Phone", cascade={"persist"}, mappedBy="type")
     *
     * @var Phone[]
     */
    private $phone;

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

    public function __toString(): string
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
     * @return Phone[]
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param Phone[] $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }
}
