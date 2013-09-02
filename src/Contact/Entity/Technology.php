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
 * Technology
 *
 * @ORM\Table(name="contact_technology")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("contact_technology")
 *
 * @category    Contact
 * @package     Entity
 */
class Technology
{
    /**
     * @var integer
     *
     * @ORM\Column(name="contact_technology_id", type="integer", nullable=false)
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
     * @var \Technology
     *
     * @ORM\ManyToOne(targetEntity="Technology")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="technology_id", referencedColumnName="technology_id")
     * })
     */
    private $technology;
}
