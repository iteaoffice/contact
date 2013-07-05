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
use Zend\Permissions\Acl\Resource\ResourceInterface;

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;

use Gedmo\Mapping\Annotation AS Gedmo;

/**
 * Entity for the Contact
 *
 * @ORM\Table(name="contact_address")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("contact_address")
 *
 * @category    Contact
 * @package     Entity
 */
class Address
{
    /**
     * @ORM\Column(name="address_id", length=10, type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="address", type="string", length=80, nullable=true)
     * @var string
     */
    private $address;
    /**
     * @ORM\Column(name="zipcode", type="string", length=20, nullable=true)
     * @var string
     */
    private $zipcode;
    /**
     * @ORM\Column(name="city", type="string", length=40, nullable=true)
     * @var string
     */
    private $city;
    /**
     * @ORM\ManyToOne(targetEntity="AddressType", cascade={"persist"}, inversedBy="addresses")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="type_id", referencedColumnName="type_id")
     * })
     * @var \AddressType
     */
    private $type;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")
     * })
     * @var Contact
     */
    private $contact;
    /**
     * @var \Country
     *
     * @ORM\ManyToOne(targetEntity="General\Entity\Country")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="country_id", referencedColumnName="country_id")
     * })
     */
    private $country;

}
