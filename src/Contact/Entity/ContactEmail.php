<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * ContactEmail
 *
 * @ORM\Table(name="contact_email")
 * @ORM\Entity
 */
class ContactEmail
{
    /**
     * @var integer
     *
     * @ORM\Column(name="email_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $emailId;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=60, nullable=false)
     */
    private $email;

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
