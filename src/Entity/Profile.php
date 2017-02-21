<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

namespace Contact\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 * Profile.
 *
 * @ORM\Table(name="contact_profile")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("contact_profile")
 *
 * @category    Contact
 */
class Profile extends EntityAbstract
{
    /**
     * Constant for hideForOthers = 0 (not hidden).
     */
    const NOT_HIDE_FOR_OTHERS = 0;
    /**
     * Constant for hideForOthers = 1 (hidden).
     */
    const HIDE_FOR_OTHERS = 1;
    /**
     * Constant for hidePhoto = 0 (not hidden).
     */
    const NOT_HIDE_PHOTO = 0;
    /**
     * Constant for hidePhoto = 1 (hidden).
     */
    const HIDE_PHOTO = 1;
    /**
     * Constant for visible = 0 (hidden).
     */
    const VISIBLE_HIDDEN = 0;
    /**
     * Constant for visible = 1 (community).
     */
    const VISIBLE_COMMUNITY = 1;
    /**
     * Textual versions of the hideForOthers.
     *
     * @var array
     */
    protected $hideForOthersTemplates
        = array(
            self::NOT_HIDE_FOR_OTHERS => 'txt-not-hide-for-others',
            self::HIDE_FOR_OTHERS     => 'txt-hide-for-others',
        );
    /**
     * Textual versions of the hideForOthers.
     *
     * @var array
     */
    protected $hidePhotoTemplates
        = array(
            self::NOT_HIDE_PHOTO => 'txt-not-hide-photo',
            self::HIDE_PHOTO     => 'txt-hide-photo',
        );
    /**
     * Textual versions of the visibility.
     *
     * @var array
     */
    protected $visibleTemplates
        = array(
            self::VISIBLE_HIDDEN    => 'txt-visibility-hidden',
            self::VISIBLE_COMMUNITY => 'txt-visibility-community',
        );
    /**
     * @ORM\Column(name="profile_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
     *
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="hide_for_others", type="smallint", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"hideForOthersTemplates"})
     * @Annotation\Attributes({"label":"txt-hide-for-others", "required":"true"})
     * @Annotation\Required(true)
     * @deprecated
     *
     * @var integer
     */
    private $hideForOthers;
    /**
     * @ORM\Column(name="hide_photo", type="smallint", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"hidePhotoTemplates"})
     * @Annotation\Attributes({"label":"txt-hide-photo", "required":"true"})
     * @Annotation\Required(true)
     *
     * @var integer
     */
    private $hidePhoto;
    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Textarea")
     * @Annotation\Options({"label":"txt-expertise"})
     *
     * @var string
     */
    private $description;
    /**
     * @ORM\Column(name="visible", type="smallint", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"visibleTemplates"})
     * @Annotation\Attributes({"label":"txt-visibility", "required":"true"})
     * @Annotation\Required(true)
     *
     * @var integer
     */
    private $visible;
    /**
     * @ORM\OneToOne(targetEntity="Contact\Entity\Contact", cascade="persist", inversedBy="profile")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=false)
     * })
     * @Annotation\Type("\Zend\Form\Element\Hidden")
     *
     * @var \Contact\Entity\Contact
     */
    private $contact;

    /**
     * Default value when a new profile is created.
     */
    public function __construct()
    {
        $this->hideForOthers = self::NOT_HIDE_FOR_OTHERS;
        $this->hidePhoto     = self::NOT_HIDE_PHOTO;
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
     * @return \Contact\Entity\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param \Contact\Entity\Contact $contact
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
     * @param int $hideForOthers
     */
    public function setHideForOthers($hideForOthers)
    {
        $this->hideForOthers = $hideForOthers;
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
     * @param int $hidePhoto
     */
    public function setHidePhoto($hidePhoto)
    {
        $this->hidePhoto = $hidePhoto;
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
     */
    public function setId($id)
    {
        $this->id = $id;
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

    /**
     * @param int $visible
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    }
}
