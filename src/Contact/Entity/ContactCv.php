<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * ContactCv
 *
 * @ORM\Table(name="contact_cv")
 * @ORM\Entity
 */
class ContactCv
{
    /**
     * @var integer
     *
     * @ORM\Column(name="cv_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $cvId;

    /**
     * @var string
     *
     * @ORM\Column(name="cv", type="blob", nullable=false)
     */
    private $cv;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     */
    private $dateCreated;

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

}
