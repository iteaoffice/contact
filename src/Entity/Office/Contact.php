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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Laminas\Form\Annotation;

/**
 * @ORM\Table(name="contact_office")
 * @ORM\Entity(repositoryClass="Contact\Repository\Office\ContactRepository")
 * @Annotation\Name("contact_office")
 */
class Contact extends AbstractEntity
{
    /**
     * @ORM\Column(name="office_contact_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="hours", type="smallint", options={"unsigned":true})
     * @Annotation\Type("\Laminas\Form\Element\Number")
     * @Annotation\Options({"label":"txt-hours"})
     *
     * @var int
     */
    private $hours = 0;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     * @Annotation\Exclude()
     *
     * @var DateTime
     */
    private $dateCreated;
    /**
     * @ORM\Column(name="date_end", type="datetime", nullable=true)
     * @Annotation\Type("\Laminas\Form\Element\Date")
     * @Annotation\Options({"label":"txt-end-date"})
     *
     * @var DateTime
     */
    private $dateEnd;
    /**
     * @ORM\Column(name="calendar_color", type="string", nullable=true)
     * @Annotation\Type("\Laminas\Form\Element\Color")
     * @Annotation\Options({"label":"txt-calendar-color"})
     *
     * @var string
     */
    private $calendarColor;
    /**
     * @ORM\OneToOne(targetEntity="Contact\Entity\Contact", cascade={"persist"}, inversedBy="officeContact")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")
     * @Annotation\Type("Contact\Form\Element\Contact")
     * @Annotation\Options({"label":"txt-contact"})
     *
     * @var \Contact\Entity\Contact
     */
    private $contact;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\Office\Leave", cascade={"persist","remove"}, mappedBy="officeContact")
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
        return $this->getContact() ? $this->getContact()->parseFullName() : '';
    }

    public function getContact(): ?\Contact\Entity\Contact
    {
        return $this->contact;
    }

    public function setContact(?\Contact\Entity\Contact $contact): Contact
    {
        $this->contact = $contact;
        return $this;
    }

    public function isActive(): bool
    {
        return null === $this->dateEnd;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): Contact
    {
        $this->id = $id;
        return $this;
    }

    public function getHours(): int
    {
        return $this->hours;
    }

    public function setHours(int $hours): Contact
    {
        $this->hours = $hours;
        return $this;
    }

    public function getDateCreated(): ?DateTime
    {
        return $this->dateCreated;
    }

    public function getDateEnd(): ?DateTime
    {
        return $this->dateEnd;
    }

    public function setDateEnd(?DateTime $dateEnd): Contact
    {
        $this->dateEnd = $dateEnd;
        return $this;
    }

    public function getCalendarColor(): ?string
    {
        return $this->calendarColor;
    }

    public function setCalendarColor(string $calendarColor): Contact
    {
        $this->calendarColor = $calendarColor;
        return $this;
    }

    public function getLeave(): Collection
    {
        return $this->leave;
    }

    public function setLeave(Collection $leave): Contact
    {
        $this->leave = $leave;
        return $this;
    }
}
