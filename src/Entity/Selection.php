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

use Admin\Entity\Access;
use DateTime;
use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Event\Entity\Meeting\Meeting;
use Event\Entity\Meeting\OptionCost;
use Gedmo\Mapping\Annotation as Gedmo;
use Mailing\Entity\Mailing;
use Zend\Form\Annotation;

/**
 * @ORM\Table(name="selection")
 * @ORM\Entity(repositoryClass="Contact\Repository\Selection")
 * @Gedmo\SoftDeleteable(fieldName="dateDeleted")
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("contact_selection")
 */
class Selection extends AbstractEntity
{
    public const SELECTION_INVOICE_CORE = 1;
    public const SELECTION_BSG = 46;
    public const SELECTION_STG = 47;
    public const SELECTION_PROJECT_MANAGEMENT = 219;

    public const NOT_CORE = 0;
    public const CORE = 1;

    public const TYPE_SQL = 1;
    public const TYPE_FIXED = 2;

    protected static $coreTemplates
        = [
            self::NOT_CORE => 'txt-not-core',
            self::CORE     => 'txt-core',
        ];

    /**
     * @ORM\Column(name="selection_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Type("\Zend\Form\Element\Hidden")
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="selection", type="string", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-selection"})
     *
     * @var string
     */
    private $selection;
    /**
     * @ORM\Column(name="tag", type="string", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-tag"})
     *
     * @var string
     */
    private $tag;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     * @Annotation\Exclude()
     *
     * @var DateTime
     */
    private $dateCreated;
    /**
     * @ORM\Column(name="date_deleted", type="datetime", nullable=true)
     * @Annotation\Exclude()
     *
     * @var DateTime
     */
    private $dateDeleted;
    /**
     * @ORM\Column(name="note", type="text", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Textarea")
     * @Annotation\Options({"label":"txt-selection-note-label","help-block":"txt-selection-note-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-selection-note-placeholder"})
     *
     * @var string
     */
    private $note;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", cascade={"persist"}, inversedBy="selection")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=true)
     * @Annotation\Type("Contact\Form\Element\Contact")
     * @Annotation\Options({"label":"txt-selection-owner-label","help-block":"txt-selection-owner-help-block"})
     *
     * @var Contact
     */
    private $contact;
    /**
     * @ORM\Column(name="core", type="smallint", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"coreTemplates"})
     * @Annotation\Options({"label":"txt-selection-core-label","help-block":"txt-selection-core-help-block"})
     *
     * @var int
     */
    private $core;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\SelectionContact", cascade={"persist","remove"}, mappedBy="selection")
     * @Annotation\Exclude()
     *
     * @var SelectionContact[]|Collections\ArrayCollection
     */
    private $selectionContact;
    /**
     * @ORM\OneToOne(targetEntity="Contact\Entity\SelectionSql", cascade={"persist","remove"}, mappedBy="selection")
     * @Annotation\Exclude()
     *
     * @var SelectionSql
     */
    private $sql;
    /**
     * @ORM\ManyToMany(targetEntity="Mailing\Entity\Mailing", cascade={"persist"}, mappedBy="selection")
     * @Annotation\Exclude()
     *
     * @var Mailing[]|Collections\ArrayCollection
     */
    private $mailing;
    /**
     * @ORM\ManyToMany(targetEntity="Event\Entity\Meeting\OptionCost", cascade={"persist"}, mappedBy="selection")
     * @Annotation\Exclude()
     *
     * @var OptionCost[]|Collections\ArrayCollection
     */
    private $meetingOptionCost;
    /**
     * @ORM\ManyToMany(targetEntity="Event\Entity\Meeting\Meeting", cascade={"persist"}, mappedBy="selection")
     * @Annotation\Exclude()
     *
     * @var Meeting[]|Collections\ArrayCollection
     */
    private $meeting;
    /**
     * @ORM\ManyToMany(targetEntity="Event\Entity\Meeting\Cost", cascade={"persist"}, mappedBy="selection")
     * @Annotation\Exclude()
     *
     * @var OptionCost[]|Collections\ArrayCollection
     */
    private $meetingCost;
    /**
     * @ORM\ManyToMany(targetEntity="Admin\Entity\Access", cascade={"persist"}, mappedBy="selection")
     * @Annotation\Exclude();
     *
     * @var Access[]|Collections\ArrayCollection
     */
    private $access;

    public function __construct()
    {
        $this->core = self::NOT_CORE;

        $this->selectionContact = new Collections\ArrayCollection();
        $this->mailing = new Collections\ArrayCollection();
        $this->meeting = new Collections\ArrayCollection();
        $this->meetingOptionCost = new Collections\ArrayCollection();
        $this->meetingCost = new Collections\ArrayCollection();
        $this->access = new Collections\ArrayCollection();
    }

    public static function getCoreTemplates(): array
    {
        return self::$coreTemplates;
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
        return (string)$this->selection;
    }

    public function isCore(): bool
    {
        return $this->core === self::CORE;
    }

    public function isActive(): bool
    {
        return null === $this->dateDeleted;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId(int $id): Selection
    {
        $this->id = $id;
        return $this;
    }

    public function getSelection(): ?string
    {
        return $this->selection;
    }

    public function setSelection(string $selection): Selection
    {
        $this->selection = $selection;
        return $this;
    }

    public function getTag()
    {
        return $this->tag;
    }

    public function setTag(?string $tag): Selection
    {
        $this->tag = $tag;
        return $this;
    }

    public function getDateCreated(): ?DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(DateTime $dateCreated): Selection
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getDateDeleted(): ?DateTime
    {
        return $this->dateDeleted;
    }

    public function setDateDeleted(?DateTime $dateDeleted): Selection
    {
        $this->dateDeleted = $dateDeleted;
        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): Selection
    {
        $this->note = $note;
        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(Contact $contact): Selection
    {
        $this->contact = $contact;
        return $this;
    }

    public function getCore(bool $textual = false)
    {
        if ($textual) {
            return self::$coreTemplates[$this->core];
        }
        return $this->core;
    }

    public function setCore(int $core): Selection
    {
        $this->core = $core;
        return $this;
    }

    public function getSelectionContact()
    {
        return $this->selectionContact;
    }

    public function setSelectionContact($selectionContact)
    {
        $this->selectionContact = $selectionContact;
        return $this;
    }

    public function getSql(): ?SelectionSql
    {
        return $this->sql;
    }

    public function setSql(SelectionSql $sql): Selection
    {
        $this->sql = $sql;
        return $this;
    }

    public function getMailing()
    {
        return $this->mailing;
    }

    public function setMailing($mailing): Selection
    {
        $this->mailing = $mailing;

        return $this;
    }

    public function getMeetingOptionCost()
    {
        return $this->meetingOptionCost;
    }

    public function setMeetingOptionCost($meetingOptionCost): Selection
    {
        $this->meetingOptionCost = $meetingOptionCost;
        return $this;
    }

    public function getMeeting()
    {
        return $this->meeting;
    }

    public function setMeeting($meeting): Selection
    {
        $this->meeting = $meeting;
        return $this;
    }

    public function getMeetingCost()
    {
        return $this->meetingCost;
    }

    public function setMeetingCost($meetingCost): Selection
    {
        $this->meetingCost = $meetingCost;
        return $this;
    }

    public function getAccess()
    {
        return $this->access;
    }

    public function setAccess($access): Selection
    {
        $this->access = $access;
        return $this;
    }
}
