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

/**
 * ContactCommunity.
 *
 * @ORM\Table(name="contact_community")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("contact_community")
 *
 * @category    Contact
 */
class Community extends EntityAbstract
{
    /**
     * @ORM\Column(name="community_id", length=10, type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="community", type="string", length=40, nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-community"})
     *
     * @var string
     */
    private $community;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", cascade="persist", inversedBy="community")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=false)
     * })
     * @Annotation\Type("\Zend\Form\Element\Hidden")
     *
     * @var \Contact\Entity\Contact
     */
    private $contact;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\CommunityType", cascade={"persist"}, inversedBy="community")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="type_id", referencedColumnName="type_id", nullable=false)
     * })
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({"target_class":"General\Entity\CommunityType"})
     * @Annotation\Attributes({"label":"txt-community-type"})
     *
     * @var \General\Entity\CommunityType
     */
    private $type;

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

    /** Set input filter
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
                    array(
                        'name'     => 'community',
                        'required' => false,
                        'filters'  => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'type',
                        'required' => false,
                    )
                )
            );
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    public function populate()
    {
        return $this->getArrayCopy();
    }

    /**
     * Needed for the hydration of form elements.
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return array(
            'community' => $this->community,
            'contact'   => $this->contact,
            'type'      => $this->type,
        );
    }

    /**
     * @return string
     */
    public function getCommunity()
    {
        return $this->community;
    }

    /**
     * @param string $community
     */
    public function setCommunity($community)
    {
        $this->community = $community;
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
     * @return \General\Entity\CommunityType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param \General\Entity\CommunityType $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
}
