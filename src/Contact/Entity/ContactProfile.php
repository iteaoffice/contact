<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * ContactProfile
 *
 * @ORM\Table(name="contact_profile")
 * @ORM\Entity
 */
class ContactProfile
{
    /**
     * @var integer
     *
     * @ORM\Column(name="profile_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $profileId;

    /**
     * @var integer
     *
     * @ORM\Column(name="hide_for_others", type="smallint", nullable=false)
     */
    private $hideForOthers;

    /**
     * @var integer
     *
     * @ORM\Column(name="hide_photo", type="smallint", nullable=false)
     */
    private $hidePhoto;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="visible", type="smallint", nullable=false)
     */
    private $visible;

    /**
     * @var \Contact
     *
     * @ORM\ManyToOne(targetEntity="Contact")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")
     * })
     */
    private $contact;

}
