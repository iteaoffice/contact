<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * ContactIdea
 *
 * @ORM\Table(name="contact_idea")
 * @ORM\Entity
 */
class ContactIdea
{
    /**
     * @var integer
     *
     * @ORM\Column(name="contact_idea_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $contactIdeaId;

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
     * @var \Idea
     *
     * @ORM\ManyToOne(targetEntity="Idea")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idea_id", referencedColumnName="idea_id")
     * })
     */
    private $idea;

}
