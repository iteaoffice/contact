<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use General\Entity\ContentType;
use Program\Entity\Program;
use Laminas\Form\Annotation;

use function sprintf;

/**
 * @ORM\Table(name="contact_dnd")
 * @ORM\Entity
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("contact_dnd")
 */
class Dnd extends AbstractEntity
{
    /**
     * @ORM\Column(name="dnd_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="update")
     * @Annotation\Exclude()
     *
     * @var DateTime
     */
    private $dateCreated;
    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="update")
     * @Annotation\Exclude()
     *
     * @var DateTime
     */
    private $dateUpdated;
    /**
     * @ORM\Column(name="size", type="integer", options={"unsigned":true})
     * @Annotation\Exclude()
     *
     * @var int
     */
    private $size;
    /**
     * @ORM\OneToMany(targetEntity="\Contact\Entity\DndObject", cascade={"persist","remove"}, mappedBy="dnd")
     * @Annotation\Exclude()
     *
     * @var DndObject
     */
    private $object;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", cascade={"persist"}, inversedBy="dnd")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=false)
     * @Annotation\Exclude()
     *
     * @var Contact
     */
    private $contact;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\ContentType", cascade={"persist"}, inversedBy="contactDnd")
     * @ORM\JoinColumn(name="contenttype_id", referencedColumnName="contenttype_id", nullable=false)
     * @Annotation\Exclude()
     *
     * @var ContentType
     */
    private $contentType;
    /**
     * @ORM\ManyToOne(targetEntity="Program\Entity\Program", cascade="persist", inversedBy="contactDnd")
     * @ORM\JoinColumn(name="program_id", referencedColumnName="program_id")
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({"target_class":"Program\Entity\Program"})
     * @Annotation\Options({"label":"txt-contact-dnd-program-label","help-block":"txt-program-dnd-program-help-block"})
     *
     * @var Program
     */
    private $program;
    /**
     * @Annotation\Type("\Laminas\Form\Element\File")
     * @Annotation\Options({"label":"txt-dnd-file"})
     *
     * @var ContentType
     */
    private $file;

    public function __construct()
    {
        $this->object = new ArrayCollection();
    }

    public function parseFileName(): string
    {
        return sprintf('DND %s for %s', $this->contact, $this->program);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): Dnd
    {
        $this->id = $id;
        return $this;
    }

    public function getDateCreated(): ?DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(DateTime $dateCreated): Dnd
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getDateUpdated(): ?DateTime
    {
        return $this->dateUpdated;
    }

    public function setDateUpdated(DateTime $dateUpdated): Dnd
    {
        $this->dateUpdated = $dateUpdated;
        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): Dnd
    {
        $this->size = $size;
        return $this;
    }

    public function getObject()
    {
        return $this->object;
    }

    public function setObject($object): Dnd
    {
        $this->object = $object;
        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(Contact $contact): Dnd
    {
        $this->contact = $contact;
        return $this;
    }

    public function getContentType(): ?ContentType
    {
        return $this->contentType;
    }

    public function setContentType(ContentType $contentType): Dnd
    {
        $this->contentType = $contentType;
        return $this;
    }

    public function getProgram(): ?Program
    {
        return $this->program;
    }

    public function setProgram(Program $program): Dnd
    {
        $this->program = $program;
        return $this;
    }
}
