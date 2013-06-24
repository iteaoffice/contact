<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * ContactWeb
 *
 * @ORM\Table(name="contact_web")
 * @ORM\Entity
 */
class ContactWeb
{
    /**
     * @var integer
     *
     * @ORM\Column(name="web_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $webId;

    /**
     * @var string
     *
     * @ORM\Column(name="web", type="string", length=60, nullable=false)
     */
    private $web;

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
