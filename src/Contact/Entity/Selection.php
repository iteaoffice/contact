<?php
/**
 * Debranova copyright message placeholder
 *
 * @category    Contact
 * @package     Entity
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 Debranova
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
 * Entity for the Selection
 *
 * @ORM\Table(name="selection")
 * @ORM\Entity(repositoryClass="Contact\Repository\Selection")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("contact_selection")
 *
 * @category    Contact
 * @package     Entity
 */
class Selection extends EntityAbstract
{
    /**
     * Constant for notPersonal = 0 (not personal)
     */
    const NOT_PERSONAL = 0;
    /**
     * Constant for notPersonal = 1 (personal)
     */
    const PERSONAL = 1;
    /**
     * Constant for private = 0 (not private)
     */
    const NOT_PRIVATE = 0;
    /**
     * Constant for private = 1 (hidden)
     */
    const IS_PRIVATE = 1;
    /**
     * Textual versions of the hideForOthers
     *
     * @var array
     */
    protected $personalTemplates = [
        self::NOT_PERSONAL => 'txt-not-personal',
        self::PERSONAL     => 'txt-personal',
    ];
    /**
     * Textual versions of the hideForOthers
     *
     * @var array
     */
    protected $privateTemplates = [
        self::NOT_PRIVATE => 'txt-not-private',
        self::IS_PRIVATE  => 'txt-private',
    ];
    /**
     * @ORM\Column(name="selection_id", length=10, type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="selection", type="string", length=80, nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-selection"})
     * @var string
     */
    private $selection;
    /**
     * @ORM\Column(name="tag", type="string", length=20, nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-tag"})
     * @var string
     */
    private $tag;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="update")
     * @Annotation\Exclude()
     * @var \DateTime
     */
    private $dateCreated;
    /**
     * @ORM\Column(name="date_deleted", type="datetime", nullable=false)
     * @Annotation\Exclude()
     * @var \DateTime
     */
    private $dateDeleted;
    /**
     * @ORM\Column(name="note", type="string", length=40, nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-note"})
     * @var string
     */
    private $note;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", cascade={"persist"}, inversedBy="selection")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=false)
     * })
     * @Annotation\Exclude()
     * @var Contact
     */
    private $contact;
    /**
     * @ORM\Column(name="personal", type="smallint", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"personalTemplates"})
     * @Annotation\Attributes({"label":"txt-personal", "required":"true"})
     * @Annotation\Required(true)
     * @var int
     */
    private $personal;
    /**
     * @ORM\Column(name="private", type="smallint", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"privateTemplates"})
     * @Annotation\Attributes({"label":"txt-private", "required":"true"})
     * @Annotation\Required(true)
     * @var int
     */
    private $private;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\SelectionContact", cascade={"persist"}, mappedBy="selection")
     * @Annotation\Exclude()
     * @var \Contact\Entity\SelectionContact[]|Collections\ArrayCollection
     */
    private $selectionContact;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\SelectionMailinglist", cascade={"persist"}, mappedBy="selection")
     * @Annotation\Exclude()
     * @var \Contact\Entity\SelectionMailingList[]|Collections\ArrayCollection
     */
    private $mailingList;
    /**
     * @ORM\OneToOne(targetEntity="Contact\Entity\SelectionSql", cascade={"persist"}, mappedBy="selection")
     * @Annotation\Exclude()
     * @var \Contact\Entity\SelectionSql
     */
    private $sql;
    /**
     * @ORM\ManyToMany(targetEntity="Mailing\Entity\Mailing", cascade={"persist"}, mappedBy="selection")
     * @Annotation\Exclude()
     * @var \Mailing\Entity\Mailing[]|Collections\ArrayCollection
     */
    private $mailing;
    /**
     * @ORM\OneToMany(targetEntity="Event\Entity\Exempt", cascade={"persist"}, mappedBy="selection")
     * @Annotation\Exclude()
     * @var \Event\Entity\Exempt[]|Collections\ArrayCollection
     */
    private $exempt;
    /**
     * @ORM\ManyToMany(targetEntity="Admin\Entity\Access", cascade={"persist"}, mappedBy="selection")
     * @Annotation\Exclude();
     * @var \Admin\Entity\Access[]|Collections\ArrayCollection
     */
    private $access;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->selectionContact = new Collections\ArrayCollection();
        $this->mailingList = new Collections\ArrayCollection();
        $this->mailing = new Collections\ArrayCollection();
        $this->exempt = new Collections\ArrayCollection();
        $this->access = new Collections\ArrayCollection();
    }

    /**
     * Magic Getter
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
     * Magic Setter
     *
     * @param $property
     * @param $value
     *
     * @return void
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
    public function getPersonalTemplates()
    {
        return $this->personalTemplates;
    }

    /**
     * @return array
     */
    public function getPrivateTemplates()
    {
        return $this->privateTemplates;
    }

    /**
     * Set input filter
     *
     * @param InputFilterInterface $inputFilter
     *
     * @return void
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Setting an inputFilter is currently not supported");
    }

    /**
     * @return \Zend\InputFilter\InputFilter|\Zend\InputFilter\InputFilterInterface
     */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();
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
                        'required' => true,
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
                        'required' => true,
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
                        'name'       => 'personal',
                        'required'   => true,
                        'validators' => [
                            [
                                'name'    => 'InArray',
                                'options' => [
                                    'haystack' => array_keys($this->getPersonalTemplates())
                                ]
                            ]
                        ]
                    ]
                )
            );
            $inputFilter->add(
                $factory->createInput(
                    [
                        'name'       => 'private',
                        'required'   => true,
                        'validators' => [
                            [
                                'name'    => 'InArray',
                                'options' => [
                                    'haystack' => array_keys($this->getPrivateTemplates())
                                ]
                            ]
                        ]
                    ]
                )
            );
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    /**
     * Needed for the hydration of form elements
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return [
            'selection'   => $this->selection,
            'tag'         => $this->tag,
            'dateCreated' => $this->dateCreated,
            'dateDeleted' => $this->dateDeleted,
            'note'        => $this->note,
            'personal'    => $this->personal,
            'private'     => $this->private,
        ];
    }

    public function populate()
    {
        return $this->getArrayCopy();
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
    public function getPersonal()
    {
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
     * @return int
     */
    public function getPrivate()
    {
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
     * @param \Event\Entity\Exempt[]|Collections\ArrayCollection $exempt
     */
    public function setExempt($exempt)
    {
        $this->exempt = $exempt;
    }

    /**
     * @return \Event\Entity\Exempt[]|Collections\ArrayCollection
     */
    public function getExempt()
    {
        return $this->exempt;
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
}
