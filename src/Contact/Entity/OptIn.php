<?php
/**
 * Debranova copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 Debranova
 */

namespace Contact\Entity;

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

/**
 * Optin.
 *
 * @ORM\Table(name="optin")
 * @ORM\Entity
 */
class OptIn extends EntityAbstract
{
    /**
     * Feature to decide which opt-ins to enable on registration.
     */
    const AUTO_SUBSCRIBE = 1;
    const NO_AUTO_SUBSCRIBE = 0;

    /**
     * @var array
     */
    protected static $autoSubscribeTemplates = [
        self::AUTO_SUBSCRIBE    => "txt-auto-subscribe",
        self::NO_AUTO_SUBSCRIBE => "txt-no-auto-subscribe",
    ];

    /**
     * @ORM\Column(name="optin_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="optin", type="string", length=40, nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-opt-in"})
     * @Annotation\Attributes({"class":"span3"})
     *
     * @var string
     */
    private $optIn;
    /**
     * @ORM\Column(name="auto_subscribe", type="integer", nullable=false)
     *
     * @var integer
     */
    private $autoSubscribe;
    /**
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-description"})
     *
     * @var string
     */
    private $description;
    /**
     * @ORM\ManyToMany(targetEntity="Contact\Entity\Contact", cascade={"persist"}, mappedBy="optIn")
     * @Annotation\Exclude();
     *
     * @var \Contact\Entity\Contact[]
     */
    private $contact;
    /**
     * @ORM\OneToMany(targetEntity="Mailing\Entity\Mailing", cascade={"persist"}, mappedBy="optIn")
     * @Annotation\Exclude()
     *
     * @var \Mailing\Entity\Mailing[]
     */
    private $mailing;

    /**
     *
     */
    public function __construct()
    {
        $this->contact = new Collections\ArrayCollection();
        $this->mailing = new Collections\ArrayCollection();
        $this->autoSubscribe = self::AUTO_SUBSCRIBE;
    }

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
        return (string)$this->optIn;
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
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();
            $inputFilter->add(
                $factory->createInput(
                    [
                        'name'     => 'optIn',
                        'required' => true,
                    ]
                )
            );
            $inputFilter->add(
                $factory->createInput(
                    [
                        'name'     => 'description',
                        'required' => true,
                    ]
                )
            );
            $inputFilter->add(
                $factory->createInput(
                    [
                        'name'     => 'autoSubscribe',
                        'required' => true,
                    ]
                )
            );
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    /**
     * @return array
     */
    public static function getAutoSubscribeTemplates()
    {
        return self::$autoSubscribeTemplates;
    }

    /**
     * New function needed to make the hydrator happy.
     *
     * @param Collections\Collection $collection
     */
    public function addContact(Collections\Collection $collection)
    {
        foreach ($collection as $singleContact) {
            $singleContact->optIn = $this;
            $this->contact->add($singleContact);
        }
    }

    /**
     * @param Collections\Collection $collection
     */
    public function removeContact(Collections\Collection $collection)
    {
        foreach ($collection as $singleContact) {
            $this->contact->removeElement($singleContact);
        }
    }

    /**
     * @param \Contact\Entity\Contact[] $contact
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return \Contact\Entity\Contact[]
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
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
     * @param string $optIn
     */
    public function setOptIn($optIn)
    {
        $this->optIn = $optIn;
    }

    /**
     * @return string
     */
    public function getOptIn()
    {
        return $this->optIn;
    }

    /**
     * @param bool $textual
     * @return int|string
     */
    public function getAutoSubscribe($textual = false)
    {
        if ($textual) {
            return self::$autoSubscribeTemplates[$this->autoSubscribe];
        }
        return $this->autoSubscribe;
    }

    /**
     * @param int $autoSubscribe
     */
    public function setAutoSubscribe($autoSubscribe)
    {
        $this->autoSubscribe = $autoSubscribe;
    }

    /**
     * @return \Mailing\Entity\Mailing[]
     */
    public function getMailing()
    {
        return $this->mailing;
    }

    /**
     * @param \Mailing\Entity\Mailing[] $mailing
     */
    public function setMailing($mailing)
    {
        $this->mailing = $mailing;
    }
}
