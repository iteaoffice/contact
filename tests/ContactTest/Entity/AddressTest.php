<?php
/**
 * ITEA copyright message placeholder
 *
 * @category    ContactTest
 * @package     Entity
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 ITEA
 */
namespace ContactTest\Entity;

use Zend\InputFilter\InputFilter;

use Contact\Entity\Contact;
use Contact\Entity\Address;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;

use ContactTest\Bootstrap;


class AddressTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceManager;
    /**
     * @var \Doctrine\ORM\EntityManager;
     */
    protected $entityManager;
    /**
     * @var array
     */
    protected $addressData;
    /**
     * @var Address;
     */
    protected $address;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();
        $this->entityManager = $this->serviceManager->get('doctrine.entitymanager.orm_default');

        $contact = new Contact();
        $contact->setFirstName('Jan');
        $contact->setLastName('Dam');
        $contact->setEmail('address_test@example.com');
        $contact->setState(1);
        $contact->setPassword('password');
        $contact->setMessenger('messenger');
        $contact->setDateOfBirth(new \DateTime());

        $gender = new \General\Entity\Gender();
        $gender->setName('name for AddressTest');
        $gender->setAttention('attention for AddressTest');
        $gender->setSalutation('salutation for AddressTest');

        $contact->setGender($gender);

        $title = new \General\Entity\Title();
        $title->setName('name for AddressTest');
        $title->setAttention('attention for AddressTest');
        $title->setSalutation('salutation for AddressTest');

        $contact->setTitle($title);


        $country = new \General\Entity\Country();
        $country->setCountry('country');
        $country->setCd('cd');
        $country->setNumcode(100);
        $country->setIso3('CCD');


        $this->addressData = array(
            'contact' => $contact,
            'country' => $country,
            'address' => 'This is the Address',
            'zipcode' => '1234',
            'city' => 'This is the City');

        $this->address = new Address();
    }

    public function testCanCreateEntity()
    {
        $this->assertInstanceOf("Contact\Entity\Address", $this->address);
        $this->assertInstanceOf("Contact\Entity\EntityInterface", $this->address);
        $this->assertNull($this->address->getId(), 'The "Id" should be null');

        $today = new \DateTime();
        $this->address->setDateCreated($today);
        $this->address->setLastUpdate($today);

        $id = 1;
        $this->address->setId($id);

        $this->assertEquals($today, $this->address->getDateCreated(), 'The "DateCreated" should be the same as the setter');
        $this->assertEquals($today, $this->address->getLastUpdate(), 'The "LastUpdate" should be the same as the setter');
        $this->assertEquals($id, $this->address->getId(), 'The "Id" should be the same as the setter');

        $this->assertTrue(is_array($this->address->getArrayCopy()));
        $this->assertTrue(is_array($this->address->populate()));
    }

    public function testMagicGettersAndSetters()
    {
        $this->address->address = 'test';
        $this->assertEquals('test', $this->address->address);
    }

    /**
     * @expectedException \Exception
     */
    public function testCannotSetInputFilter()
    {
        $this->address->setInputFilter(new InputFilter());
    }


    public function testHasInputFilter()
    {
        $this->assertInstanceOf('Zend\InputFilter\InputFilter', $this->address->getInputFilter());
    }


    public function testCanSaveEntityInDatabase()
    {
        $hydrator = new DoctrineObject(
            $this->entityManager,
            'Contact\Entity\Address'
        );

        $this->address = $hydrator->hydrate($this->addressData, new Address());
        $this->entityManager->persist($this->address);
        $this->entityManager->flush();

        $this->assertInstanceOf('Contact\Entity\Address', $this->address);
        $this->assertNotNull($this->address->getId());
        $this->assertNotNull($this->address->getDateCreated());
        $this->assertNotNull($this->address->getLastUpdate());
        $this->assertEquals($this->address->getContact()->getId(), $this->addressData['contact']->getId());
        $this->assertEquals($this->address->getAddress(), $this->addressData['address']);
        $this->assertEquals($this->address->getZipcode(), $this->addressData['zipcode']);
        $this->assertEquals($this->address->getCity(), $this->addressData['city']);
        $this->assertEquals($this->address->getCountry()->getId(), $this->addressData['country']->getId());

        $this->assertNotNull($this->address->getResourceId());
    }


}
