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
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 * Entity for ITEA office employee leave.
 *
 * @ORM\Table(name="contact_office_leave")
 * @ORM\Entity(repositoryClass="Contact\Repository\Office\LeaveRepository")
 * @Annotation\Name("contact_office_leave")
 *
 * @category    Contact
 */
class Leave extends AbstractEntity
{
    /**
     * @ORM\Column(name="leave_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Type("\Zend\Form\Element\Hidden")
     * @Annotation\Attributes({"id":"leave-id"})
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Office\Contact", cascade={"persist"}, inversedBy="leave")
     * @ORM\JoinColumn(name="office_contact_id", referencedColumnName="office_contact_id")
     * @Annotation\Exclude()
     *
     * @var Contact
     */
    private $officeContact;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Office\LeaveType", inversedBy="leave")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="type_id")
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({"target_class":"Contact\Entity\Office\LeaveType", "label":"txt-type"})
     * @Annotation\Attributes({"id":"type-id", "required":"required"})
     *
     * @var LeaveType
     */
    private $type;
    /**
     * @ORM\Column(name="description", type="string", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Attributes({"maxlength":255, "id":"description", "required":"required"})
     * @Annotation\Options({"label":"txt-description"})
     *
     * @var string
     */
    private $description;
    /**
     * @ORM\Column(name="date_start", type="date", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Date")
     * @Annotation\Options({"label":"txt-start-date"})
     * @Annotation\Attributes({"id":"date-start", "required":"required"})
     *
     * @var DateTime
     */
    private $dateStart;
    /**
     * @ORM\Column(name="date_end", type="date", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Date")
     * @Annotation\Options({"label":"txt-end-date"})
     * @Annotation\Attributes({"id":"date-end", "required":"required"})
     *
     * @var DateTime
     */
    private $dateEnd;
    /**
     * @ORM\Column(name="hours", type="decimal", precision=6, scale=2)
     * @Annotation\Type("\Zend\Form\Element\Number")
     * @Annotation\Options({"label":"txt-hours"})
     * @Annotation\Attributes({"id":"hours", "required":"required"})
     *
     * @var float
     */
    private $hours = 0.0;


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
        return (string)$this->hours;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): Leave
    {
        $this->id = $id;
        return $this;
    }

    public function getOfficeContact(): ?Contact
    {
        return $this->officeContact;
    }

    public function setOfficeContact(Contact $officeContact): Leave
    {
        $this->officeContact = $officeContact;
        return $this;
    }

    public function getType(): ?LeaveType
    {
        return $this->type;
    }

    public function setType(LeaveType $type): Leave
    {
        $this->type = $type;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): Leave
    {
        $this->description = $description;
        return $this;
    }

    public function getDateStart(): ?DateTime
    {
        return $this->dateStart;
    }

    public function setDateStart(DateTime $dateStart): Leave
    {
        $this->dateStart = $dateStart;
        return $this;
    }

    public function getDateEnd(): ?DateTime
    {
        return $this->dateEnd;
    }

    public function setDateEnd(DateTime $dateEnd): Leave
    {
        $this->dateEnd = $dateEnd;
        return $this;
    }

    public function getHours(): float
    {
        return (float)$this->hours;
    }

    public function setHours(float $hours): Leave
    {
        $this->hours = $hours;
        return $this;
    }
}
