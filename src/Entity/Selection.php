<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Contact\Entity;

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Zend\Form\Annotation;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

/**
 * Entity for the Selection.
 *
 * @ORM\Table(name="selection")
 * @ORM\Entity(repositoryClass="Contact\Repository\Selection")
 * @Gedmo\SoftDeleteable(fieldName="dateDeleted")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("contact_selection")
 *
 * @category    Contact
 */
class Selection extends EntityAbstract
{
    const SELECTION_INVOICE_CORE = 1;
    const SELECTION_PROJECT_MANAGEMENT = 219;
    /**
     * Constant for notPersonal = 0 (not personal).
     */
    const NOT_PERSONAL = 0;
    /**
     * Constant for notPersonal = 1 (personal).
     */
    const PERSONAL = 1;
    /**
     * Constant for private = 0 (not private).
     */
    const NOT_PRIVATE = 0;
    /**
     * Constant for private = 1 (hidden).
     */
    const IS_PRIVATE = 1;
    const TYPE_SQL = 1;
    const TYPE_FIXED = 2;

    /**
     * Textual versions of the hideForOthers.
     *
     * @var array
     */
    protected static $personalTemplates
        = [
            self::NOT_PERSONAL => 'txt-not-personal',
            self::PERSONAL     => 'txt-personal',
        ];
    /**
     * Textual versions of the hideForOthers.
     *
     * @var array
     */
    protected static $privateTemplates
        = [
            self::NOT_PRIVATE => 'txt-not-private',
            self::IS_PRIVATE  => 'txt-private',
        ];
    /**
     * @ORM\Column(name="selection_id", length=10, type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
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
     * @ORM\Column(name="date_deleted", type="datetime", nullable=false)
     * @Annotation\Exclude()
     *
     * @var \DateTime
     */
    private $dateDeleted;
    /**
     * @ORM\Column(name="note", type="string", length=40, nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Textarea")
     * @Annotation\Options({"label":"txt-note"})
     *
     * @var string
     */
    private $note;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", cascade={"persist"}, inversedBy="selection")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=false)
     * })
     * @Annotation\Type("Contact\Form\Element\Contact")
     * @Annotation\Options({"label":"txt-owner"})
     *
     * @var Contact
     */
    private $contact;
    /**
     * @ORM\Column(name="personal", type="smallint", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"personalTemplates"})
     * @Annotation\Attributes({"label":"txt-personal"})
     *
     * @var int
     */
    private $personal;
    /**
     * @ORM\Column(name="private", type="smallint", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"privateTemplates"})
     * @Annotation\Attributes({"label":"txt-private", "required":"true"})
     * @Annotation\Required(true)
     *
     * @var int
     */
    private $private;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\SelectionContact", cascade={"persist"}, mappedBy="selection")
     * @Annotation\Exclude()
     *
     * @var \Contact\Entity\SelectionContact[]|Collections\ArrayCollection
     */
    private $selectionContact;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\SelectionMailinglist", cascade={"persist"}, mappedBy="selection")
     * @Annotation\Exclude()
     *
     * @var \Contact\Entity\SelectionMailingList[]|Collections\ArrayCollection
     */
    private $mailingList;
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

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->private  = self::NOT_PRIVATE;
        $this->personal = self::NOT_PERSONAL;

        $this->selectionContact  = new Collections\ArrayCollection();
        $this->mailingList       = new Collections\ArrayCollection();
        $this->mailing           = new Collections\ArrayCollection();
        $this->meeting           = new Collections\ArrayCollection();
        $this->meetingOptionCost = new Collections\ArrayCollection();
        $this->meetingCost       = new Collections\ArrayCollection();
        $this->access            = new Collections\ArrayCollection();
    }

    /**
     * Magic Getter.
     *
     * @param $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        return $this->$property;
    }

    /**
     * Magic Setter.
     *
     * @param $property
     * @param $value
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->selection;
    }

    /**
     * @return array
     */
    public static function getPersonalTemplates()
    {
        return self::$personalTemplates;
    }

    /**
     * @return array
     */
    public static function getPrivateTemplates()
    {
        return self::$privateTemplates;
    }

    /**
     * Set input filter.
     *
     * @param InputFilterInterface $inputFilter
     *
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception(
            "Setting an inputFilter is currently not supported"
        );
    }

    /**
     * @return \Zend\InputFilter\InputFilter|\Zend\InputFilter\InputFilterInterface
     */
    public function getInputFilter()
    {
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();
            $inputFilter->add(
                $factory->createInput(
                    [
                        'name'       => 'selection',
                        'required'   => true,
                        'filters'    => [
                            ['name' => 'StripTags'],
                            ['name' => 'StringTrim'],
                        ],
                        'validators' => [
                            [
                                'name'    => 'StringLength',
                                'options' => [
                                    'encoding' => 'UTF-8',
                                    'min'      => 1,
                                    'max'      => 80,
                                ],
                            ],
                        ],
                    ]
                )
            );
            $inputFilter->add(
                $factory->createInput(
                    [
                        'name'     => 'note',
                        'required' => false,
                        'filters'  => [
                            ['name' => 'StripTags'],
                            ['name' => 'StringTrim'],
                        ],
                    ]
                )
            );
            $inputFilter->add(
                $factory->createInput(
                    [
                        'name'     => 'tag',
                        'required' => false,
                        'filters'  => [
                            ['name' => 'StripTags'],
                            ['name' => 'StringTrim'],
                        ],
                    ]
                )
            );
            $inputFilter->add(
                $factory->createInput(
                    [
                        'name'     => 'personal',
                        'required' => true,
                    ]
                )
            );
            $inputFilter->add(
                $factory->createInput(
                    [
                        'name'     => 'private',
                        'required' => true,
                    ]
                )
            );
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }


    /**
     * @param \Contact\Entity\Contact $contact
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return \Contact\Entity\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param \DateTime $dateCreated
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param \DateTime $dateDeleted
     */
    public function setDateDeleted($dateDeleted)
    {
        $this->dateDeleted = $dateDeleted;
    }

    /**
     * @return \DateTime
     */
    public function getDateDeleted()
    {
        return $this->dateDeleted;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param int $personal
     */
    public function setPersonal($personal)
    {
        $this->personal = $personal;
    }

    /**
     * @return int
     */
    public function getPersonal($textual = false)
    {
        if ($textual) {
            return self::$personalTemplates[$this->personal];
        }

        return $this->personal;
    }

    /**
     * @param int $private
     */
    public function setPrivate($private)
    {
        $this->private = $private;
    }

    /**
     * @param bool $textual
     *
     * @return int
     */
    public function getPrivate($textual = false)
    {
        if ($textual) {
            return self::$privateTemplates[$this->private];
        }

        return $this->private;
    }

    /**
     * @param string $selection
     */
    public function setSelection($selection)
    {
        $this->selection = $selection;
    }

    /**
     * @return string
     */
    public function getSelection()
    {
        return $this->selection;
    }

    /**
     * @param string $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param \Contact\Entity\SelectionContact[]|Collections\ArrayCollection $selectionContact
     */
    public function setSelectionContact($selectionContact)
    {
        $this->selectionContact = $selectionContact;
    }

    /**
     * @return \Contact\Entity\SelectionContact[]|Collections\ArrayCollection
     */
    public function getSelectionContact()
    {
        return $this->selectionContact;
    }

    /**
     * @param \Contact\Entity\SelectionMailingList[]|Collections\ArrayCollection $mailingList
     */
    public function setMailingList($mailingList)
    {
        $this->mailingList = $mailingList;
    }

    /**
     * @return \Contact\Entity\SelectionMailingList[]|Collections\ArrayCollection
     */
    public function getMailingList()
    {
        return $this->mailingList;
    }

    /**
     * @param \Contact\Entity\SelectionSql $sql
     */
    public function setSql($sql)
    {
        $this->sql = $sql;
    }

    /**
     * @return \Contact\Entity\SelectionSql
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * @param \Mailing\Entity\Mailing[]|Collections\ArrayCollection $mailing
     */
    public function setMailing($mailing)
    {
        $this->mailing = $mailing;
    }

    /**
     * @return \Mailing\Entity\Mailing[]|Collections\ArrayCollection
     */
    public function getMailing()
    {
        return $this->mailing;
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
     * @param \Admin\Entity\Access[]|Collections\ArrayCollection $access
     */
    public function setAccess($access)
    {
        $this->access = $access;
    }

    /**
     * @return \Admin\Entity\Access[]|Collections\ArrayCollection
     */
    public function getAccess()
    {
        return $this->access;
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
}
