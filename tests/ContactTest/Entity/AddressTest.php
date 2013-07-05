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
     * @var Contact;
     */
    protected $contact;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();
        $this->entityManager = $this->serviceManager->get('doctrine.entitymanager.orm_default');

        $this->contact = $this->entityManager->find("Contact\Entity\Contact", 1);

        $this->addressData = array(
            'contact' => $this->contact,
            'address' => file_get_contents(__DIR__ . '/../_files/php.exe'));

        $this->address = new Address();
    }

    public function testCanCreateEntity()
    {
        $this->assertInstanceOf("Contact\Entity\Address", $this->address);
        $this->assertNull($this->address->getId(), 'The "Id" should be null');

        $today = new \DateTime();
        $this->address->setDateCreated($today);
        $this->address->setDateUpdated($today);

        $id = 1;
        $this->address->setId($id);

        $this->assertEquals($today, $this->address->getDateCreated(), 'The "DateCreated" should be the same as the setter');
        $this->assertEquals($today, $this->address->getDateUpdated(), 'The "DateUpdated" should be the same as the setter');
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
        $this->assertNotNull($this->address->getDateUpdated());
        $this->assertEquals($this->address->getContact()->getId(), $this->addressData['contact']->getId());
        $this->assertEquals($this->address->getAddress(), $this->addressData['address']);

        $this->assertNotNull($this->address->getResourceId());
    }


}
