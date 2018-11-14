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
use Zend\Form\Annotation;

/**
 * Optin.
 *
 * @ORM\Table(name="optin")
 * @ORM\Entity(repositoryClass="Contact\Repository\OptIn")
 */
class OptIn extends AbstractEntity
{
    public const ACTIVE_INACTIVE = 0;
    public const ACTIVE_ACTIVE = 1;

    protected static $activeTemplates
        = [
            self::ACTIVE_INACTIVE => 'txt-inactive',
            self::ACTIVE_ACTIVE   => 'txt-active',
        ];

    /**
     * @ORM\Column(name="optin_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Type("\Zend\Form\Element\Hidden")
     *
     * @var integer|string|null
     */
    private $id;
    /**
     * @ORM\Column(name="optin", type="string", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-opt-in-title-title","help-block": "txt-opt-in-title-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-opt-in-title-placeholder"})
     *
     * @var string|null
     */
    private $optIn;
    /**
     * @ORM\Column(name="active", type="integer", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"activeTemplates"})
     * @Annotation\Options({"label":"txt-opt-in-active-title","help-block": "txt-opt-in-active-help-block"})
     * @var integer
     */
    private $active;
    /**
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-opt-in-description-title","help-block": "txt-opt-in-description-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-opt-in-description-placeholder"})
     *
     * @var string
     */
    private $description;
    /**
     * @ORM\ManyToMany(targetEntity="Contact\Entity\Contact", cascade={"persist"}, mappedBy="optIn", fetch="EXTRA_LAZY")
     * @Annotation\Exclude();
     *
     * @var \Contact\Entity\Contact[]|Collections\ArrayCollection
     */
    private $contact;
    /**
     * @ORM\OneToMany(targetEntity="Mailing\Entity\Mailing", cascade={"persist"}, mappedBy="optIn", fetch="EXTRA_LAZY")
     * @Annotation\Exclude()
     *
     * @var \Mailing\Entity\Mailing[]|Collections\ArrayCollection
     */
    private $mailing;


    public function __construct()
    {
        $this->contact = new Collections\ArrayCollection();
        $this->mailing = new Collections\ArrayCollection();

        $this->active = self::ACTIVE_ACTIVE;
    }


    public static function getActiveTemplates(): array
    {
        return self::$activeTemplates;
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
        return (string)$this->optIn;
    }

    public function isActive(): bool
    {
        return self::ACTIVE_ACTIVE === $this->active;
    }

    public function addContact(Collections\Collection $collection): void
    {
        foreach ($collection as $singleContact) {
            $this->contact->add($singleContact);
        }
    }

    public function removeContact(Collections\Collection $collection): void
    {
        foreach ($collection as $singleContact) {
            $this->contact->removeElement($singleContact);
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): OptIn
    {
        $this->id = $id;
        return $this;
    }

    public function getOptIn()
    {
        return $this->optIn;
    }

    public function setOptIn(?string $optIn): OptIn
    {
        $this->optIn = $optIn;
        return $this;
    }

    public function getActive(bool $textual = false)
    {
        if ($textual) {
            return self::$activeTemplates[$this->active];
        }

        return $this->active;
    }

    public function setActive(int $active): OptIn
    {
        $this->active = $active;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): OptIn
    {
        $this->description = $description;
        return $this;
    }

    public function getContact()
    {
        return $this->contact;
    }

    public function setContact($contact): OptIn
    {
        $this->contact = $contact;
        return $this;
    }

    public function getMailing()
    {
        return $this->mailing;
    }

    public function setMailing($mailing): OptIn
    {
        $this->mailing = $mailing;
        return $this;
    }
}
