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

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Zend\Form\Annotation;

/**
 * Entity for the Selection.
 *
 * @ORM\Table(name="selection")
 * @ORM\Entity(repositoryClass="Contact\Repository\Selection")
 * @Gedmo\SoftDeleteable(fieldName="dateDeleted")
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("contact_selection")
 *
 * @category    Contact
 */
class Selection extends AbstractEntity
{
    public const SELECTION_INVOICE_CORE = 1;
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
     * @ORM\Column(name="selection_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Type("\Zend\Form\Element\Hidden")
     *
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="selection", type="string", length=80, nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-selection"})
     *
     * @var string
     */
    private $selection;
    /**
     * @ORM\Column(name="tag", type="string", length=20, nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-tag"})
     *
     * @var string
     */
    private $tag;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     * @Annotation\Exclude()
     *
     * @var \DateTime
     */
    private $dateCreated;
    /**
     * @ORM\Column(name="date_deleted", type="datetime", nullable=true)
     * @Annotation\Exclude()
     *
     * @var \DateTime
     */
    private $dateDeleted;
    /**
     * @ORM\Column(name="note", type="string", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Textarea")
     * @Annotation\Options({"label":"txt-selection-note-label","help-block":"txt-selection-note-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-selection-note-placeholder"})
     *
     * @var string
     */
    private $note;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", cascade={"persist"}, inversedBy="selection")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=false)
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
     * @ORM\OneToMany(targetEntity="Contact\Entity\SelectionContact", cascade={"persist"}, mappedBy="selection")
     * @Annotation\Exclude()
     *
     * @var \Contact\Entity\SelectionContact[]|Collections\ArrayCollection
     */
    private $selectionContact;
    /**
     * @ORM\OneToOne(targetEntity="Contact\Entity\SelectionSql", cascade={"persist"}, mappedBy="selection")
     * @Annotation\Exclude()
     *
     * @var \Contact\Entity\SelectionSql
     */
    private $sql;
    /**
     * @ORM\ManyToMany(targetEntity="Mailing\Entity\Mailing", cascade={"persist"}, mappedBy="selection")
     * @Annotation\Exclude()
     *
     * @var \Mailing\Entity\Mailing[]|Collections\ArrayCollection
     */
    private $mailing;
    /**
     * @ORM\ManyToMany(targetEntity="Event\Entity\Meeting\OptionCost", cascade={"persist"}, mappedBy="selection")
     * @Annotation\Exclude()
     *
     * @var \Event\Entity\Meeting\OptionCost[]|Collections\ArrayCollection
     */
    private $meetingOptionCost;
    /**
     * @ORM\ManyToMany(targetEntity="Event\Entity\Meeting\Meeting", cascade={"persist"}, mappedBy="selection")
     * @Annotation\Exclude()
     *
     * @var \Event\Entity\Meeting\Meeting[]|Collections\ArrayCollection
     */
    private $meeting;
    /**
     * @ORM\ManyToMany(targetEntity="Event\Entity\Meeting\Cost", cascade={"persist"}, mappedBy="selection")
     * @Annotation\Exclude()
     *
     * @var \Event\Entity\Meeting\OptionCost[]|Collections\ArrayCollection
     */
    private $meetingCost;
    /**
     * @ORM\ManyToMany(targetEntity="Admin\Entity\Access", cascade={"persist"}, mappedBy="selection")
     * @Annotation\Exclude();
     *
     * @var \Admin\Entity\Access[]|Collections\ArrayCollection
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

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Selection
     */
    public function setId(int $id): Selection
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getSelection()
    {
        return $this->selection;
    }

    /**
     * @param string $selection
     *
     * @return Selection
     */
    public function setSelection(string $selection): Selection
    {
        $this->selection = $selection;
        return $this;
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param string $tag
     *
     * @return Selection
     */
    public function setTag(?string $tag): Selection
    {
        $this->tag = $tag;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param \DateTime $dateCreated
     *
     * @return Selection
     */
    public function setDateCreated(\DateTime $dateCreated): Selection
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateDeleted()
    {
        return $this->dateDeleted;
    }

    /**
     * @param \DateTime $dateDeleted
     *
     * @return Selection
     */
    public function setDateDeleted(\DateTime $dateDeleted): Selection
    {
        $this->dateDeleted = $dateDeleted;
        return $this;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param string $note
     *
     * @return Selection
     */
    public function setNote(string $note): Selection
    {
        $this->note = $note;
        return $this;
    }

    /**
     * @return Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param Contact $contact
     *
     * @return Selection
     */
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

    /**
     * @param int $core
     *
     * @return Selection
     */
    public function setCore(int $core): Selection
    {
        $this->core = $core;
        return $this;
    }

    /**
     * @return SelectionContact[]|Collections\ArrayCollection
     */
    public function getSelectionContact()
    {
        return $this->selectionContact;
    }

    /**
     * @param SelectionContact[]|Collections\ArrayCollection $selectionContact
     *
     * @return Selection
     */
    public function setSelectionContact($selectionContact)
    {
        $this->selectionContact = $selectionContact;
        return $this;
    }

    /**
     * @return SelectionSql
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * @param SelectionSql $sql
     *
     * @return Selection
     */
    public function setSql(SelectionSql $sql): Selection
    {
        $this->sql = $sql;
        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Mailing\Entity\Mailing[]
     */
    public function getMailing()
    {
        return $this->mailing;
    }

    /**
     * @param Collections\ArrayCollection|\Mailing\Entity\Mailing[] $mailing
     *
     * @return Selection
     */
    public function setMailing($mailing)
    {
        $this->mailing = $mailing;
        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Event\Entity\Meeting\OptionCost[]
     */
    public function getMeetingOptionCost()
    {
        return $this->meetingOptionCost;
    }

    /**
     * @param Collections\ArrayCollection|\Event\Entity\Meeting\OptionCost[] $meetingOptionCost
     *
     * @return Selection
     */
    public function setMeetingOptionCost($meetingOptionCost)
    {
        $this->meetingOptionCost = $meetingOptionCost;
        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Event\Entity\Meeting\Meeting[]
     */
    public function getMeeting()
    {
        return $this->meeting;
    }

    /**
     * @param Collections\ArrayCollection|\Event\Entity\Meeting\Meeting[] $meeting
     *
     * @return Selection
     */
    public function setMeeting($meeting)
    {
        $this->meeting = $meeting;
        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Event\Entity\Meeting\OptionCost[]
     */
    public function getMeetingCost()
    {
        return $this->meetingCost;
    }

    /**
     * @param Collections\ArrayCollection|\Event\Entity\Meeting\OptionCost[] $meetingCost
     *
     * @return Selection
     */
    public function setMeetingCost($meetingCost)
    {
        $this->meetingCost = $meetingCost;
        return $this;
    }

    /**
     * @return \Admin\Entity\Access[]|Collections\ArrayCollection
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * @param \Admin\Entity\Access[]|Collections\ArrayCollection $access
     *
     * @return Selection
     */
    public function setAccess($access)
    {
        $this->access = $access;
        return $this;
    }
}
