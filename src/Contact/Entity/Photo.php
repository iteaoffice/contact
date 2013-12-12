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
use Zend\Form\Annotation;
use Zend\InputFilter\FileInput;

use Gedmo\Mapping\Annotation as Gedmo;
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
class Photo extends EntityAbstract
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
     * @Annotation\Exclude()
     * @var resource
     */
    private $photo;
    /**
     * @ORM\Column(name="height", type="integer", nullable=true)
     * @Annotation\Exclude()
     * @var integer
     */
    private $height;
    /**
     * @ORM\Column(name="width", type="integer", nullable=true)
     * @Annotation\Exclude()
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
     * @Gedmo\Timestampable(on="update")
     * @var \DateTime
     */
    private $dateUpdated;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\ContentType", cascade="persist", inversedBy="contactPhoto")
     * @ORM\JoinColumn(name="contenttype_id", referencedColumnName="contenttype_id", nullable=false)
     * @Annotation\Exclude()
     * @var \General\Entity\ContentType
     */
    private $contentType;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", inversedBy="photo", cascade={"persist"})
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=false)
     * })
     * @var \Contact\Entity\Contact
     */
    private $contact;

    /**
     * Although an alternative does not have a clear hash, we can create one based on the id;
     *
     * @return string
     */
    public function getHash()
    {
        return sha1($this->id . $this->getContact()->getId());
    }

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->contentType = null;
    }

    /**
     * Magic Getter
     *
     * @param $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        return $this->$property;
    }

    /**
     * Magic Setter
     *
     * @param $property
     * @param $value
     *
     * @return void
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->phone;
    }

    /**
     * Set input filter
     *
     * @param InputFilterInterface $inputFilter
     *
     * @return void
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Setting an inputFilter is currently not supported");
    }

    /**
     * @return \Zend\InputFilter\InputFilter|\Zend\InputFilter\InputFilterInterface
     */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $fileUpload = new FileInput('file');

            $fileUpload->setRequired(false);
            $fileUpload->getValidatorChain()->attachByName(
                'File\Extension',
                array(
                    'extension' => array('jpg', 'jpeg', 'png'),
                )
            );
            $fileUpload->getValidatorChain()->attachByName(
                'File\MimeType',
                array(
                    'image/jpeg',
                    'image/jpg',
                    'image/png'
                )
            );
            $fileUpload->getValidatorChain()->attachByName(
                'File\Size',
                array(
                    'min' => '20kB',
                    'max' => '4MB',
                )
            );

            $fileUpload->getValidatorChain()->attachByName(
                'File\ImageSize',
                array(
                    'minWidth'  => 100,
                    'minHeight' => 100
                )
            );

            $inputFilter->add($fileUpload);


            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }


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
            DIRECTORY_SEPARATOR . DEBRANOVA_HOST . DIRECTORY_SEPARATOR . 'contact-photo';

        return $cacheDir . DIRECTORY_SEPARATOR
        . $this->getHash() . '.'
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

//    /**
//     * @param int $oldContentType
//     */
//    public function setOldContentType($oldContentType)
//    {
//        $this->oldContentType = $oldContentType;
//    }
//
//    /**
//     * @return int
//     */
//    public function getOldContentType()
//    {
//        return $this->oldContentType;
//    }

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
