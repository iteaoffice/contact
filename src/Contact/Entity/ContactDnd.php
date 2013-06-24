<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * ContactDnd
 *
 * @ORM\Table(name="contact_dnd")
 * @ORM\Entity
 */
class ContactDnd
{
    /**
     * @var integer
     *
     * @ORM\Column(name="dnd_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $dndId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     */
    private $dateCreated;

    /**
     * @var integer
     *
     * @ORM\Column(name="size", type="integer", nullable=false)
     */
    private $size;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_updated", type="datetime", nullable=false)
     */
    private $dateUpdated;

    /**
     * @var \Contact
     *
     * @ORM\ManyToOne(targetEntity="Contact")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")
     * })
     */
    private $contact;

    /**
     * @var \Contenttype
     *
     * @ORM\ManyToOne(targetEntity="Contenttype")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="contenttype_id", referencedColumnName="contenttype_id")
     * })
     */
    private $contenttype;

    /**
     * @var \Program
     *
     * @ORM\ManyToOne(targetEntity="Program")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="program_id", referencedColumnName="program_id")
     * })
     */
    private $program;

}
