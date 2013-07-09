<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * ContactPhone
 *
 * @ORM\Table(name="contact_phone")
 * @ORM\Entity
 */
class ContactPhone
{
    /**
     * @var integer
     *
     * @ORM\Column(name="phone_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $phoneId;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=40, nullable=false)
     */
    private $phone;

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
     * @var \PhoneType
     *
     * @ORM\ManyToOne(targetEntity="PhoneType")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="type_id", referencedColumnName="type_id")
     * })
     */
    private $type;

}
