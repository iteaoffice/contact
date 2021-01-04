<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\Entity\Selection;

use Contact\Entity\AbstractEntity;
use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Laminas\Form\Annotation;

/**
 * @ORM\Table(name="selection_type")
 * @ORM\Entity(repositoryClass="Contact\Repository\Selection\Type")
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("selection_type")
 */
class Type extends AbstractEntity
{
    public const TYPE_CORE = 1;
    public const TYPE_EVAL = 2;

    protected static array $typeTemplates
        = [
            self::TYPE_CORE => 'txt-type-core',
            self::TYPE_EVAL => 'txt-type-evaluation',
        ];

    /**
     * @ORM\Column(name="type_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Type("\Laminas\Form\Element\Hidden")
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="type", type="string", nullable=false, unique=true)
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Options({"label":"txt-contact-selection-type-name-label","help-block":"txt-contact-selection-type-description-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-contact-selection-type-description-placeholder"})
     *
     * @var string
     */
    private $name;
    /**
     * @ORM\Column(name="description", type="string", nullable=false)
     * @Annotation\Type("\Laminas\Form\Element\Textarea")
     * @Annotation\Options({"label":"txt-contact-selection-type-description-label","help-block":"txt-contact-selection-type-description-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-contact-selection-type-description-placeholder"})
     *
     * @var string
     */
    private $description;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\Selection", cascade={"persist"}, mappedBy="type")
     * @Annotation\Exclude()
     *
     * @var \Contact\Entity\Selection[]|Collections\ArrayCollection
     */
    private $selection;

    public function __construct()
    {
        $this->selection = new Collections\ArrayCollection();
    }

    public static function getTypeTemplates(): array
    {
        return self::$typeTemplates;
    }

    public function __toString(): string
    {
        return (string)$this->name;
    }

    public function isCore(): bool
    {
        return $this->id === self::TYPE_CORE;
    }

    public function isEvaluation(): bool
    {
        return $this->id === self::TYPE_EVAL;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Type
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Type
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Type
    {
        $this->description = $description;
        return $this;
    }

    public function getSelection()
    {
        return $this->selection;
    }

    public function setSelection($selection): Type
    {
        $this->selection = $selection;
        return $this;
    }
}
