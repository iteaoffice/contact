<?php
/**
 * Debranova copyright message placeholder
 *
 * @category    Contact
 * @package     Entity
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Debranova
 */
namespace Contact\Entity;

use Zend\Form\Annotation;

use Doctrine\ORM\Mapping as ORM;

/**
 * Phone
 *
 * @ORM\Table(name="phone_type")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("phone_type")
 *
 * @category    Contact
 * @package     Entity
 */
class PhoneType
{
    const PHONE_TYPE_DIRECT = 1;
    const PHONE_TYPE_MOBILE = 2;
    const PHONE_TYPE_HOME   = 3;
    const PHONE_TYPE_FAX    = 4;

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
     * @ORM\OneToMany(targetEntity="Contact\Entity\Phone", cascade={"persist"}, mappedBy="type")
     * @Annotation\Exclude()
     * @var \Contact\Entity\Phone[]
     */
    private $phone;

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
}
