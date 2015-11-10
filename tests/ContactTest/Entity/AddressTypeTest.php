<?php
/**
 * ITEA copyright message placeholder
 *
 * @category    ContactTest
 * @package     Entity
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */
namespace ContactTest\Entity;

use Contact\Entity\AddressType;
use Contact\Entity\Contact;
use ContactTest\Bootstrap;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Zend\InputFilter\InputFilter;

class AddressTypeTest extends \PHPUnit_Framework_TestCase
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
    protected $addressTypeData;
    /**
     * @var AddressType;
     */
    protected $addressType;
    /**
     * @var Contact;
     */
    protected $contact;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->serviceManager  = Bootstrap::getServiceManager();
        $this->entityManager   = $this->serviceManager->get('Doctrine\ORM\EntityManager');
        $this->contact         = $this->entityManager->find("Contact\Entity\Contact", 1);
        $this->addressTypeData = array(
            'type' => 'address type'
        );
        $this->addressType     = new AddressType();
    }

    public function testCanCreateEntity()
    {
        $this->assertInstanceOf("Contact\Entity\AddressType", $this->addressType);
        $this->assertInstanceOf("Contact\Entity\EntityInterface", $this->addressType);
        $this->assertNull($this->addressType->getId(), 'The "Id" should be null');
        $id = 1;
        $this->addressType->setId($id);
        $this->assertEquals($id, $this->addressType->getId(), 'The "Id" should be the same as the setter');
        $this->assertTrue(is_array($this->addressType->getArrayCopy()));
        $this->assertTrue(is_array($this->addressType->populate()));
    }

    /**
     * @expectedException \Exception
     */
    public function testCannotSetInputFilter()
    {
        $this->addressType->setInputFilter(new InputFilter());
    }

    public function testHasFilter()
    {
        $this->assertInstanceOf('Zend\InputFilter\InputFilter', $this->addressType->getInputFilter());
    }

    public function testMagicGettersAndSetters()
    {
        $this->addressType->type = 'test';
        $this->assertEquals('test', $this->addressType->type);
    }

    public function testCanSaveEntityInDatabase()
    {
        $hydrator          = new DoctrineObject(
            $this->entityManager,
            'Contact\Entity\AddressType'
        );
        $this->addressType = $hydrator->hydrate($this->addressTypeData, new AddressType());
        $this->entityManager->persist($this->addressType);
        $this->entityManager->flush();
        $this->assertInstanceOf('Contact\Entity\AddressType', $this->addressType);
        $this->assertNotNull($this->addressType->getId());
        $this->assertEquals($this->addressType->getType(), $this->addressTypeData['type']);
    }

    public function testToString()
    {
        $this->addressType->type = $this->addressTypeData['type'];
        $this->assertEquals((string) $this->addressType, $this->addressTypeData['type']);
    }
}
