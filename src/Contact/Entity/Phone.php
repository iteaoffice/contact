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

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\Factory as InputFactory;
use Zend\Form\Annotation;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Phone
 *
 * @ORM\Table(name="contact_phone")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("contact_phone")
 *
 * @category    Contact
 * @package     Entity
 */
class Phone
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
