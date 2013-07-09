<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * ContactLink
 *
 * @ORM\Table(name="contact_link")
 * @ORM\Entity
 */
class ContactLink
{
    /**
     * @var integer
     *
     * @ORM\Column(name="link_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $linkId;

    /**
     * @var \Contact
     *
     * @ORM\ManyToOne(targetEntity="Contact")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact1_id", referencedColumnName="contact_id")
     * })
     */
    private $contact1;

    /**
     * @var \Contact
     *
     * @ORM\ManyToOne(targetEntity="Contact")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact2_id", referencedColumnName="contact_id")
     * })
     */
    private $contact2;

}
