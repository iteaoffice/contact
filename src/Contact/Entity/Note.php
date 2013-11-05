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
 * @ORM\Table(name="contact_note")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("contact_note")
 *
 * @category    Contact
 * @package     Entity
 */
class Note
{
    /**
     * @ORM\Column(name="note_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="note", type="string", length=255, nullable=true)
     * @var string
     */
    private $note;
    /**
     * @ORM\Column(name="source", type="string", length=32, nullable=true)
     * @var string
     */
    private $source;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=true)
     * @var \DateTime
     */
    private $dateCreated;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", cascade={"persist"}, inversedBy="note")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")
     * })
     * @var \Contact\Entity\Contact
     */
    private $contact;
}
