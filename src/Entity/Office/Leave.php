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
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 * Entity for ITEA office employee leave.
 *
 * @ORM\Table(name="contact_office_leave")
 * @ORM\Entity
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
     * @Annotation\Exclude()
     *
     * @var integer
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Office\Contact", cascade={"persist"}, inversedBy="leave")
     * @ORM\JoinColumn(name="office_contact_id", referencedColumnName="office_contact_id")
     * @Annotation\Type("\Zend\Form\Element\Hidden")
     *
     * @var Contact
     */
    private $officeContact;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Office\LeaveType", inversedBy="leave")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="type_id")
     * @var LeaveType
     */
    private $type;
    /**
     * @ORM\Column(name="date_start", type="datetime", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Date")
     * @Annotation\Options({"label":"txt-start-date"})
     *
     * @var DateTime
     */
    private $dateStart;
    /**
     * @ORM\Column(name="date_end", type="datetime", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Date")
     * @Annotation\Options({"label":"txt-end-date"})
     *
     * @var DateTime
     */
    private $dateEnd;
    /**
     * @ORM\Column(name="hours", type="decimal", precision=6, scale=2)
     * @Annotation\Type("\Zend\Form\Element\Number")
     * @Annotation\Options({"label":"txt-hours"})
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
        return (string) $this->hours;
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
        return $this->hours;
    }

    public function setHours(float $hours): Leave
    {
        $this->hours = $hours;
        return $this;
    }
}
