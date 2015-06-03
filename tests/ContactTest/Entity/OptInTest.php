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

use Contact\Entity\OptIn;
use ContactTest\Bootstrap;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Zend\InputFilter\InputFilter;

class OptInTest extends \PHPUnit_Framework_TestCase
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
    protected $optInData;
    /**
     * @var OptIn;
     */
    protected $optIn;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();
        $this->entityManager  = $this->serviceManager->get('Doctrine\ORM\EntityManager');
        $this->optInData      = array(
            'optIn'       => 'This is an option',
            'description' => 'This is a description'
        );
        $this->optIn          = new OptIn();
    }

    public function testCanCreateEntity()
    {
        $this->assertInstanceOf("Contact\Entity\OptIn", $this->optIn);
        $this->assertInstanceOf("Contact\Entity\EntityInterface", $this->optIn);
        $this->assertNull($this->optIn->getId(), 'The "Id" should be null');
        $id = 1;
        $this->optIn->setId($id);
        $this->assertEquals($id, $this->optIn->getId(), 'The "Id" should be the same as the setter');
        $this->assertTrue(is_array($this->optIn->getArrayCopy()));
        $this->assertTrue(is_array($this->optIn->populate()));
    }

    public function testMagicGettersAndSetters()
    {
        $this->optIn->optIn = 'test';
        $this->assertEquals('test', $this->optIn->optIn);
    }

    /**
     * @expectedException \Exception
     */
    public function testCannotSetInputFilter()
    {
        $this->optIn->setInputFilter(new InputFilter());
    }

    public function testHasInputFilter()
    {
        $this->assertInstanceOf('Zend\InputFilter\InputFilter', $this->optIn->getInputFilter());
    }

    public function testCanSaveEntityInDatabase()
    {
        $hydrator    = new DoctrineObject(
            $this->entityManager,
            'Contact\Entity\OptIn'
        );
        $this->optIn = $hydrator->hydrate($this->optInData, new OptIn());
        $this->entityManager->persist($this->optIn);
        $this->entityManager->flush();
        $this->assertInstanceOf('Contact\Entity\OptIn', $this->optIn);
        $this->assertNotNull($this->optIn->getId());
        $this->assertEquals($this->optIn->getOptIn(), $this->optInData['optIn']);
    }

    public function testCanAddOptInToUser()
    {
        $contact = $this->entityManager->find("Contact\Entity\Contact", 1);
        var_dump($contact->getId());
        $optIn = new \Contact\Entity\OptIn();
        $optIn->setOptIn(2);
        $optIn->setDescription('This is the description');
        $contact->setOptIn(array($optIn));
        $this->entityManager->persist($contact);
        $this->entityManager->flush();
        $this->assertEquals(1, sizeof($contact->getOptIn()));
    }

    public function testCanAddMultipleOptInToUser()
    {
        $contact = $this->entityManager->find("Contact\Entity\Contact", 1);
        $optIn   = new \Contact\Entity\OptIn();
        $optIn->setOptIn(3);
        $optIn->setDescription('This is the description');
        $optIn2 = new \Contact\Entity\OptIn();
        $optIn2->setOptIn(4);
        $optIn2->setDescription('This is the description');
        $contact->setOptIn(array($optIn, $optIn2));
        $this->entityManager->persist($contact);
        $this->entityManager->flush();
        $this->assertEquals(2, sizeof($contact->getOptIn()));
    }
}
