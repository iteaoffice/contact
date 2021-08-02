<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="contact_profile")
 * @ORM\Entity
 */
class Profile extends AbstractEntity
{
    public const VISIBLE_HIDDEN    = 0;
    public const VISIBLE_COMMUNITY = 1;

    protected static array $visibleTemplates
        = [
            self::VISIBLE_COMMUNITY => 'txt-visibility-community',
            self::VISIBLE_HIDDEN    => 'txt-visibility-hidden',
        ];
    /**
     * @ORM\Column(name="profile_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     *
     * @var string
     */
    private $description;
    /**
     * @ORM\Column(name="linked_in", type="string", length=500, nullable=true)
     *
     * @var string
     */
    private $linkedIn;
    /**
     * @ORM\Column(name="visible", type="smallint", nullable=false)
     *
     * @var int
     */
    private $visible;
    /**
     * @ORM\OneToOne(targetEntity="Contact\Entity\Contact", cascade={"persist"}, inversedBy="profile")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=false)
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

    public function isVisible(): bool
    {
        return $this->visible === self::VISIBLE_COMMUNITY;
    }

    public function toArray(): array
    {
        return [
            'visible'     => $this->visible,
            'description' => $this->description,
            'linkedIn'    => $this->linkedIn
        ];
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

    public function setDescription(?string $description): Profile
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

    public function getVisibleText(): string
    {
        return self::$visibleTemplates[$this->visible] ?? '';
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

    public function getLinkedIn(): ?string
    {
        return $this->linkedIn;
    }

    public function setLinkedIn(?string $linkedIn): Profile
    {
        $this->linkedIn = $linkedIn;
        return $this;
    }
}
