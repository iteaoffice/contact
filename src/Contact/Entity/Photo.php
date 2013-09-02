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
 * Domain
 *
 * @ORM\Table(name="contact_photo")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("contact_photo")
 *
 * @category    Contact
 * @package     Entity
 */
class Photo
{
    /**
     * @var integer
     *
     * @ORM\Column(name="photo_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="photo", type="blob", nullable=true)
     */
    private $photo;

    /**
     * @var integer
     *
     * @ORM\Column(name="height", type="integer", nullable=true)
     */
    private $height;

    /**
     * @var integer
     *
     * @ORM\Column(name="width", type="integer", nullable=true)
     */
    private $width;

    /**
     * @var string
     *
     * @ORM\Column(name="contenttype", type="string", length=20, nullable=true)
     */
    private $contentType;

    /**
     * @var string
     *
     * @ORM\Column(name="thumb", type="blob", nullable=true)
     */
    private $thumb;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_updated", type="datetime", nullable=true)
     */
    private $dateUpdated;

    /**
     * @var integer
     *
     * @ORM\Column(name="contenttype_id", type="integer", nullable=false)
     */
    private $contenttypeId;

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
