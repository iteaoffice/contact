<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * ContactOpenid
 *
 * @ORM\Table(name="contact_openid")
 * @ORM\Entity
 */
class ContactOpenid
{
    /**
     * @var integer
     *
     * @ORM\Column(name="openid_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $openidId;

    /**
     * @var string
     *
     * @ORM\Column(name="identity", type="string", length=255, nullable=false)
     */
    private $identity;

    /**
     * @var \Contact
     *
     * @ORM\ManyToOne(targetEntity="Contact")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")
     * })
     */
    private $contact;

}
