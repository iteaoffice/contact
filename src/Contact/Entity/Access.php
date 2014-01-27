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

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\Factory as InputFactory;
use Zend\Form\Annotation;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * ContactEmail
 *
 * @ORM\Table(name="access")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("contact_access")
 *
 * @category    Contact
 * @package     Entity
 */
class Access extends EntityAbstract implements RoleInterface
{
    const ACCESS_OFFICE = 'Office';
    const ACCESS_PUBLIC = 'Public';

    /**
     * @ORM\Column(name="access_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="access", type="string", length=20, nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-access"})
     * @Annotation\Exclude()
     * @var string
     */
    private $access;
    /**
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Textarea")
     * @Annotation\Options({"label":"txt-description"})
     * @Annotation\Exclude()
     * @var string
     */
    private $description;
    /**
     * @ORM\ManyToMany(targetEntity="Contact\Entity\Contact", cascade={"persist"}, mappedBy="access")
     * @Annotation\Exclude();
     * @var \Contact\Entity\Contact[]
     */
    private $contact;
    /**
     * @ORM\ManyToMany(targetEntity="Contact\Entity\Selection", inversedBy="access", cascade={"persist"})
     * @ORM\JoinTable(name="access_selection",
     *    joinColumns={@ORM\JoinColumn(name="access_id", referencedColumnName="access_id")},
     *    inverseJoinColumns={@ORM\JoinColumn(name="selection_id", referencedColumnName="selection_id")}
     * )
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntityMultiCheckbox")
     * @Annotation\Options({
     *      "target_class":"Contact\Entity\Selection",
     *      "find_method":{
     *          "name":"findBy",
     *          "params": {
     *              "criteria":{},
     *              "orderBy":{
     *                  "selection":"ASC"}
     *              }
     *          }
     *      }
     * )
     * @Annotation\Attributes({"label":"txt-selection"})
     * @var \Contact\Entity\Selection[]
     */
    private $selection;
    /**
     * @ORM\ManyToMany(targetEntity="Publication\Entity\Type", cascade={"persist"}, mappedBy="access")
     * @Annotation\Exclude();
     * @var \Publication\Entity\Type[]
     */
    private $publicationType;
    /**
     * @ORM\ManyToMany(targetEntity="Project\Entity\ResultType", cascade={"persist"}, mappedBy="access")
     * @Annotation\Exclude()
     * @var \Project\Entity\ResultType[]
     */
    private $resultType;
    /**
     * @ORM\ManyToMany(targetEntity="Project\Entity\Document\Type", cascade={"persist"}, mappedBy="access")
     * @Annotation\Exclude();
     * @var \Project\Entity\Document\Type[]
     */
    private $documentType;
    /**
     * @ORM\ManyToMany(targetEntity="Calendar\Entity\Type", cascade={"persist"}, mappedBy="access")
     * @Annotation\Exclude();
     * @var \Calendar\Entity\Type[]
     */
    private $calendarType;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->contact         = new ArrayCollection();
        $this->publicationType = new ArrayCollection();
        $this->resultType      = new ArrayCollection();
        $this->selection       = new ArrayCollection();
        $this->documentType    = new ArrayCollection();
        $this->calendarType    = new ArrayCollection();
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
        return strtolower($this->access);
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
            $factory     = new InputFactory();

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name' => 'access',
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'description',
                        'required' => true,
                    )
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
        return array(
            'contact' => $this->contact,
        );
    }

    /**
     * Function needed for the population of forms
     *
     * @return array
     */
    public function populate()
    {
        return $this->getArrayCopy();
    }

    /**
     * @param string $access
     */
    public function setAccess($access)
    {
        $this->access = $access;
    }

    /**
     * @return string
     */
    public function getAccess()
    {
        return $this->access;
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
    public function getRoleId()
    {
        return strtolower($this->access);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \Contact\Entity\Contact[] $contact
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return \Contact\Entity\Contact[]
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param \Publication\Entity\Type[] $publicationType
     */
    public function setPublicationType($publicationType)
    {
        $this->publicationType = $publicationType;
    }

    /**
     * @return \Publication\Entity\Type[]
     */
    public function getPublicationType()
    {
        return $this->publicationType;
    }

    /**
     * @param \Project\Entity\ResultType[] $resultType
     */
    public function setResultType($resultType)
    {
        $this->resultType = $resultType;
    }

    /**
     * @return \Project\Entity\ResultType[]
     */
    public function getResultType()
    {
        return $this->resultType;
    }

    /**
     * @param \Contact\Entity\Selection[] $selection
     */
    public function setSelection($selection)
    {
        $this->selection = $selection;
    }

    /**
     * @return \Contact\Entity\Selection[]
     */
    public function getSelection()
    {
        return $this->selection;
    }

    /**
     * @param \Project\Entity\Document\Type[] $documentType
     */
    public function setDocumentType($documentType)
    {
        $this->documentType = $documentType;
    }

    /**
     * @return \Project\Entity\Document\Type[]
     */
    public function getDocumentType()
    {
        return $this->documentType;
    }

    /**
     * @param \Calendar\Entity\Type[] $calendarType
     */
    public function setCalendarType($calendarType)
    {
        $this->calendarType = $calendarType;
    }

    /**
     * @return \Calendar\Entity\Type[]
     */
    public function getCalendarType()
    {
        return $this->calendarType;
    }
}
