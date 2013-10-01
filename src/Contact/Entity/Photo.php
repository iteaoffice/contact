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
     * @ORM\Column(name="photo_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="photo", type="blob", nullable=true)
     * @var resource
     */
    private $photo;
    /**
     * @ORM\Column(name="height", type="integer", nullable=true)
     * @var integer
     */
    private $height;
    /**
     * @ORM\Column(name="width", type="integer", nullable=true)
     * @var integer
     */
    private $width;
    /**
     * @ORM\Column(name="thumb", type="blob", nullable=true)
     * @var resource
     */
    private $thumb;
    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable=true)
     * @var \DateTime
     */
    private $dateUpdated;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\ContentType", cascade={"persist"}, inversedBy="contactDnd")
     * @ORM\JoinColumn(name="contenttype_id", referencedColumnName="contenttype_id", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\File")
     * @Annotation\Options({"label":"txt-dnd-file"})
     * @var \General\Entity\ContentType
     */
    private $contentType;
    /**
     * @ORM\Column(name="contenttype", type="string", nullable=true)
     * @var integer
     */
    private $oldContentType;
    /**
     * @ORM\ManyToOne(targetEntity="Contact", inversedBy="photo", cascade={"persist"})
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=false)
     * })
     * @var \Contact\Entity\Contact
     */
    private $contact;

    /**
     * Get the corresponding fileName of a file if it was cached
     * Use a dash (-) to make the distinction between the format to avoid the need of an extra folder
     *
     * @return string
     * @todo: make the location variable (via the serviceManager?)
     */
    public function getCacheFileName()
    {
        $cacheDir = __DIR__ . '/../../../../../../public' . DIRECTORY_SEPARATOR . 'assets' .
            DIRECTORY_SEPARATOR . 'contact-photo';

        return $cacheDir . DIRECTORY_SEPARATOR
        . $this->getContact()->parseHash() . '.'
        . $this->getContentType()->getExtension();
    }

    /**
     * @param \Contact\Entity\Contact $contact
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return \Contact\Entity\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param mixed $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @return mixed
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param \DateTime $dateUpdated
     */
    public function setDateUpdated($dateUpdated)
    {
        $this->dateUpdated = $dateUpdated;
    }

    /**
     * @return \DateTime
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * @param int $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $oldContentType
     */
    public function setOldContentType($oldContentType)
    {
        $this->oldContentType = $oldContentType;
    }

    /**
     * @return int
     */
    public function getOldContentType()
    {
        return $this->oldContentType;
    }

    /**
     * @param resource $photo
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }

    /**
     * @return resource
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param mixed $thumb
     */
    public function setThumb($thumb)
    {
        $this->thumb = $thumb;
    }

    /**
     * @return mixed
     */
    public function getThumb()
    {
        return $this->thumb;
    }

    /**
     * @param int $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }
}
