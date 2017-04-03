<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

namespace Contact\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * Domain.
 *
 * @ORM\Table(name="contact_note")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("contact_note")
 *
 * @category    Contact
 */
class Note extends EntityAbstract implements ResourceInterface
{
    const SOURCE_SIGNATURE = 'signature';

    /**
     * @ORM\Column(name="note_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="note", type="string", length=255, nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Textarea")
     * @Annotation\Options({"label":"txt-note"})
     *
     * @var string
     */
    private $note;
    /**
     * @ORM\Column(name="source", type="string", length=32, nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-source"})
     *
     * @var string
     */
    private $source;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $dateCreated;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", cascade={"persist"}, inversedBy="note")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")
     * })
     *
     * @var \Contact\Entity\Contact
     */
    private $contact;


    /**
     * Magic Getter.
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
     * Magic Setter.
     *
     * @param $property
     * @param $value
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
        return (string)$this->getNote();
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param string $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * Returns the string identifier of the Resource.
     *
     * @return string
     */
    public function getResourceId()
    {
        return __NAMESPACE__ . ':' . __CLASS__ . ':' . $this->id;
    }

    /**
     * Set input filter.
     *
     * @param InputFilterInterface $inputFilter
     *
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
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();
            $inputFilter->add(
                $factory->createInput(
                    [
                        'name'       => 'source',
                        'required'   => true,
                        'filters'    => [
                            ['name' => 'StripTags'],
                            ['name' => 'StringTrim'],
                        ],
                        'validators' => [
                            [
                                'name'    => 'StringLength',
                                'options' => [
                                    'encoding' => 'UTF-8',
                                    'min'      => 1,
                                    'max'      => 80,
                                ],
                            ],
                        ],
                    ]
                )
            );
            $inputFilter->add(
                $factory->createInput(
                    [
                        'name'     => 'note',
                        'required' => true,
                        'filters'  => [
                            ['name' => 'StripTags'],
                            ['name' => 'StringTrim'],
                        ],
                    ]
                )
            );
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    /**
     * @return \Contact\Entity\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param \Contact\Entity\Contact $contact
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param \DateTime $dateCreated
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }
}
