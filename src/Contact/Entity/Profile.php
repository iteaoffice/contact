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

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

/**
 * Profile
 *
 * @ORM\Table(name="contact_profile")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("contact_profile")
 *
 * @category    Contact
 * @package     Entity
 */
class Profile extends EntityAbstract
{
    /**
     * Constant for hideForOthers = 0 (not hidden)
     */
    const NOT_HIDE_FOR_OTHERS = 0;
    /**
     * Constant for hideForOthers = 1 (hidden)
     */
    const HIDE_FOR_OTHERS = 1;
    /**
     * Constant for hidePhoto = 0 (not hidden)
     */
    const NOT_HIDE_PHOTO = 0;
    /**
     * Constant for hidePhoto = 1 (hidden)
     */
    const HIDE_PHOTO = 1;
    /**
     * Constant for visible = 0 (hidden)
     */
    const VISIBLE_HIDDEN = 0;
    /**
     * Constant for visible = 1 (community)
     */
    const VISIBLE_COMMUNITY = 1;
    /**
     * Constant for visible = 1 (public)
     */
    const VISIBLE_PUBLIC = 2;
    /**
     * Textual versions of the hideForOthers
     *
     * @var array
     */
    protected $hideForOthersTemplates = array(
        self::NOT_HIDE_FOR_OTHERS => 'txt-not-hide-for-others',
        self::HIDE_FOR_OTHERS     => 'txt-hide-for-others',
    );
    /**
     * Textual versions of the hideForOthers
     *
     * @var array
     */
    protected $hidePhotoTemplates = array(
        self::NOT_HIDE_PHOTO => 'txt-not-hide-photo',
        self::HIDE_PHOTO     => 'txt-hide-photo',
    );
    /**
     * Textual versions of the visibility
     *
     * @var array
     */
    protected $visibleTemplates = array(
        self::VISIBLE_HIDDEN    => 'txt-visibility-hidden',
        self::VISIBLE_COMMUNITY => 'txt-visibility-community',
        self::VISIBLE_PUBLIC    => 'txt-visibility-public',
    );
    /**
     * @ORM\Column(name="profile_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="hide_for_others", type="smallint", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"hideForOthersTemplates"})
     * @Annotation\Attributes({"label":"txt-hide-for-others", "required":"true"})
     * @Annotation\Required(true)
     * @var integer
     */
    private $hideForOthers;
    /**
     * @ORM\Column(name="hide_photo", type="smallint", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"hidePhotoTemplates"})
     * @Annotation\Attributes({"label":"txt-hide-photo", "required":"true"})
     * @Annotation\Required(true)
     * @var integer
     */
    private $hidePhoto;
    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Textarea")
     * @Annotation\Options({"label":"txt-expertise"})
     * @var string
     */
    private $description;
    /**
     * @ORM\Column(name="visible", type="smallint", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"visibleTemplates"})
     * @Annotation\Attributes({"label":"txt-visibility", "required":"true"})
     * @Annotation\Required(true)
     * @var integer
     */
    private $visible;
    /**
     * @ORM\OneToOne(targetEntity="Contact\Entity\Contact", cascade="persist", inversedBy="profile")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=false)
     * })
     * @Annotation\Type("\Zend\Form\Element\Hidden")
     * @var \Contact\Entity\Contact
     */
    private $contact;

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
     * Default value when a new profile is created
     */
    public function __construct()
    {
        $this->hideForOthers = self::NOT_HIDE_FOR_OTHERS;
        $this->hidePhoto     = self::NOT_HIDE_PHOTO;
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
     * @return array
     */
    public function getHideForOthersTemplates()
    {
        return $this->hideForOthersTemplates;
    }

    /**
     * @return array
     */
    public function getHidePhotoTemplates()
    {
        return $this->hidePhotoTemplates;
    }

    /**
     * @return array
     */
    public function getVisibleTemplates()
    {
        return $this->visibleTemplates;
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
                        'name'     => 'description',
                        'required' => false,
                        'filters'  => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'       => 'hideForOthers',
                        'required'   => false,
                        'validators' => array(
                            array(
                                'name'    => 'InArray',
                                'options' => array(
                                    'haystack' => array_keys($this->getHideForOthersTemplates()),
                                ),
                            ),
                        ),
                    )
                )
            );
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'       => 'hidePhoto',
                        'required'   => false,
                        'validators' => array(
                            array(
                                'name'    => 'InArray',
                                'options' => array(
                                    'haystack' => array_keys($this->getHidePhotoTemplates()),
                                ),
                            ),
                        ),
                    )
                )
            );
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'       => 'visible',
                        'required'   => true,
                        'validators' => array(
                            array(
                                'name'    => 'InArray',
                                'options' => array(
                                    'haystack' => array_keys($this->getVisibleTemplates()),
                                ),
                            ),
                        ),
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
            'contact'       => $this->contact,
            'description'   => $this->description,
            'hideForOthers' => $this->hideForOthers,
            'hidePhoto'     => $this->hidePhoto,
            'visible'       => $this->visible,
        );
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
     * @param int $hideForOthers
     */
    public function setHideForOthers($hideForOthers)
    {
        $this->hideForOthers = $hideForOthers;
    }

    /**
     * @param bool $textual
     *
     * @return int
     */
    public function getHideForOthers($textual = false)
    {
        if ($textual) {
            return $this->hideForOthersTemplates[$this->hideForOthers];
        }

        return $this->hideForOthers;
    }

    /**
     * @param int $hidePhoto
     */
    public function setHidePhoto($hidePhoto)
    {
        $this->hidePhoto = $hidePhoto;
    }

    /**
     * @param $textual
     *
     * @return int
     */
    public function getHidePhoto($textual = false)
    {
        if ($textual) {
            return $this->hidePhotoTemplates[$this->hidePhoto];
        }

        return $this->hidePhoto;
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
     * @param int $visible
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    }

    /**
     * @param bool $textual
     *
     * @return int
     */
    public function getVisible($textual = false)
    {
        if ($textual) {
            return $this->visibleTemplates[$this->visible];
        }

        return $this->visible;
    }
}
