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

namespace Contact\Entity\Office;

use Contact\Entity\AbstractEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 * Entity for ITEA office employee leave types.
 *
 * @ORM\Table(name="contact_office_leave_type")
 * @ORM\Entity
 * @Annotation\Name("contact_office_leave_type")
 *
 * @category    Contact
 */
class LeaveType extends AbstractEntity
{
    /**
     * @ORM\Column(name="type_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="type", type="string", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-type"})
     *
     * @var string
     */
    private $type;
    /**
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Textarea")
     * @Annotation\Options({"label":"txt-description"})
     *
     * @var string
     */
    private $description;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\Office\Leave", mappedBy="type")
     * @Annotation\Exclude()
     *
     * @var Collection|Leave[]
     */
    private $leave;

    public function __construct()
    {
        $this->leave = new ArrayCollection();
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
        return (string) $this->type;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): LeaveType
    {
        $this->id = $id;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): LeaveType
    {
        $this->type = $type;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): LeaveType
    {
        $this->description = $description;
        return $this;
    }

    public function getLeave(): Collection
    {
        return $this->leave;
    }

    public function setLeave(Collection $leave)
    {
        $this->leave = $leave;
        return $this;
    }
}
