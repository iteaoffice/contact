<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * ContactCommunity
 *
 * @ORM\Table(name="contact_community")
 * @ORM\Entity
 */
class ContactCommunity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="community_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $communityId;

    /**
     * @var string
     *
     * @ORM\Column(name="community", type="string", length=40, nullable=false)
     */
    private $community;

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
     * @var \ContactCommunityType
     *
     * @ORM\ManyToOne(targetEntity="ContactCommunityType")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="type_id", referencedColumnName="type_id")
     * })
     */
    private $type;

}
