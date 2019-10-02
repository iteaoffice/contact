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
class Profile extends AbstractEntity
{
    public const VISIBLE_HIDDEN = 0;
    public const VISIBLE_COMMUNITY = 1;

    protected static $visibleTemplates
        = [
            self::VISIBLE_COMMUNITY => 'txt-visibility-community',
            self::VISIBLE_HIDDEN    => 'txt-visibility-hidden',
        ];
    /**
     * @ORM\Column(name="profile_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
     *
     * @var int
     */
    private $id;
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
     * @Annotation\Attributes({"label":"txt-visibility"})
     *
     * @var int
     */
    private $visible;
    /**
     * @ORM\OneToOne(targetEntity="Contact\Entity\Contact", cascade="persist", inversedBy="profile")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Hidden")
     *
     * @var Contact
     */
    private $contact;

    public function __construct()
    {
        $this->visible = self::VISIBLE_COMMUNITY;
    }

    public static function getVisibleTemplates(): array
    {
        return self::$visibleTemplates;
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

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): Profile
    {
        $this->id = $id;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): Profile
    {
        $this->description = $description;
        return $this;
    }

    public function getVisible(bool $textual = false)
    {
        if ($textual) {
            return self::$visibleTemplates[$this->visible];
        }
        return $this->visible;
    }

    public function setVisible(int $visible): Profile
    {
        $this->visible = $visible;
        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(Contact $contact): Profile
    {
        $this->contact = $contact;
        return $this;
    }
}
