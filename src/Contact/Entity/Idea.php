<?php
/**
 * Debranova copyright message placeholder
 *
 * @category    Contact
 * @package     Entity
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Debranova
 */
namespace Contact\Entity;

use Zend\Form\Annotation;

use Doctrine\ORM\Mapping as ORM;

/**
 * Domain
 *
 * @ORM\Table(name="contact_idea")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("contact_idea")
 *
 * @category    Contact
 * @package     Entity
 */
class Idea
{
    /**
     * @var integer
     *
     * @ORM\Column(name="contact_idea_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * @var \Idea
     *
     * @ORM\ManyToOne(targetEntity="Idea")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idea_id", referencedColumnName="idea_id")
     * })
     */
    private $idea;
}
