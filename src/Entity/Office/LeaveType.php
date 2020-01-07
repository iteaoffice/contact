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

namespace Contact\Entity\Office;

use Contact\Entity\AbstractEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Laminas\Form\Annotation;

/**
 * Entity for ITEA office employee leave types.
 *
 * @ORM\Table(name="contact_office_leave_type")
 * @ORM\Entity()
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
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Options({"label":"txt-type"})
     *
     * @var string
     */
    private $type;
    /**
     * @ORM\Column(name="factor", type="smallint", nullable=false)
     * @Annotation\Type("\Laminas\Form\Element\Number")
     * @Annotation\Options({"label":"txt-factor"})
     *
     * @var int
     */
    private $factor = -1;

    /**
     * @ORM\Column(name="calendar", type="boolean", nullable=false)
     * @Annotation\Type("\Laminas\Form\Element\Checkbox")
     * @Annotation\Options({"label":"txt-on-calendar"})
     *
     * @var bool
     */
    private $onCalendar = false;

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

    public function __toString(): string
    {
        return (string)$this->type;
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

    public function getFactor(): int
    {
        return $this->factor;
    }

    public function setFactor(int $factor): LeaveType
    {
        $this->factor = $factor;
        return $this;
    }

    public function getOnCalendar(): bool
    {
        return $this->onCalendar;
    }

    public function setOnCalendar(bool $onCalendar): LeaveType
    {
        $this->onCalendar = $onCalendar;
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
