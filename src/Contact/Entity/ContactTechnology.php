<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * ContactTechnology
 *
 * @ORM\Table(name="contact_technology")
 * @ORM\Entity
 */
class ContactTechnology
{
    /**
     * @var integer
     *
     * @ORM\Column(name="contact_technology_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $contactTechnologyId;

    /**
     * @var \Contact
     *
     * @ORM\ManyToOne(targetEntity="Contact")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")
     * })
     */
    private $contact;

    /**
     * @var \Technology
     *
     * @ORM\ManyToOne(targetEntity="Technology")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="technology_id", referencedColumnName="technology_id")
     * })
     */
    private $technology;

}
