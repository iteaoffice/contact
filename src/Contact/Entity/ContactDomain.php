<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * ContactDomain
 *
 * @ORM\Table(name="contact_domain")
 * @ORM\Entity
 */
class ContactDomain
{
    /**
     * @var integer
     *
     * @ORM\Column(name="contact_domain_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $contactDomainId;

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
     * @var \Domain
     *
     * @ORM\ManyToOne(targetEntity="Domain")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="domain_id", referencedColumnName="domain_id")
     * })
     */
    private $domain;

}
