<?php
/**
 * ITEA copyright message placeholder
 *
 * @category    ContactTest
 * @package     Entity
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
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
        $this->entityManager  = $this->serviceManager->get('doctrine.entitymanager.orm_default');

        $contact = $this->entityManager->find("Contact\Entity\Contact", 1);
        $country = $this->entityManager->find("General\Entity\Country", 1);

        $type = new \Contact\Entity\AddressType();
        $type->setType('This is the type');

        $this->addressData = array(
            'contact' => $contact,
            'country' => $country,
            'type'    => $type,
            'address' => 'This is the Address',
            'zipcode' => '1234',
            'city'    => 'This is the City');

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
