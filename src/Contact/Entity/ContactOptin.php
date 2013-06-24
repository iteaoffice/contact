<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * ContactOptin
 *
 * @ORM\Table(name="contact_optin")
 * @ORM\Entity
 */
class ContactOptin
{
    /**
     * @var integer
     *
     * @ORM\Column(name="contact_optin_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $contactOptinId;

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
     * @var \Optin
     *
     * @ORM\ManyToOne(targetEntity="Optin")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="optin_id", referencedColumnName="optin_id")
     * })
     */
    private $optin;

}
