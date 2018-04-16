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

/**
 * Entity for a DND.
 *
 * @ORM\Table(name="contact_dnd_object")
 * @ORM\Entity
 *
 * @category    Contact
 */
class DndObject extends EntityAbstract
{
    /**
     * @var integer
     *
     * @ORM\Column(name="object_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @ORM\Column(name="object", type="blob", nullable=false)
     *
     * @var string
     */
    private $object;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Dnd", cascade="persist", inversedBy="object")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="dnd_id", referencedColumnName="dnd_id", unique=true)
     * })
     *
     * @var \Contact\Entity\Dnd;
     */
    private $dnd;

    /**
     * @param $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        return $this->$property;
    }

    /**
     * @param $property
     * @param $value
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * @param $property
     *
     * @return bool
     */
    public function __isset($property): bool
    {
        return isset($this->$property);
    }

    /**
     * @return \Contact\Entity\Dnd
     */
    public function getDnd()
    {
        return $this->dnd;
    }

    /**
     * @param \Contact\Entity\Dnd $dnd
     */
    public function setDnd($dnd)
    {
        $this->dnd = $dnd;
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
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param string $object
     */
    public function setObject($object)
    {
        $this->object = $object;
    }
}
