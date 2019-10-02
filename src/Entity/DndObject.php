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
 * @ORM\Table(name="contact_dnd_object")
 * @ORM\Entity
 */
class DndObject extends AbstractEntity
{
    /**
     * @ORM\Column(name="object_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var int
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
     * @ORM\JoinColumn(name="dnd_id", referencedColumnName="dnd_id", unique=true)
     *
     * @var Dnd;
     */
    private $dnd;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): DndObject
    {
        $this->id = $id;
        return $this;
    }

    public function getObject()
    {
        return $this->object;
    }

    public function setObject(string $object): DndObject
    {
        $this->object = $object;
        return $this;
    }

    public function getDnd(): ?Dnd
    {
        return $this->dnd;
    }

    public function setDnd(Dnd $dnd): DndObject
    {
        $this->dnd = $dnd;
        return $this;
    }
}
