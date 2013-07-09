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
use Contact\Entity\OptIn;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;

use ContactTest\Bootstrap;


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
        $this->entityManager  = $this->serviceManager->get('doctrine.entitymanager.orm_default');

        $this->optInData = array(
            'optIn'       => 'This is an option',
            'description' => 'This is a description'
        );

        $this->optIn = new OptIn();
    }

    public function testCanCreateEntity()
    {
        $this->assertInstanceOf("Contact\Entity\OptIn", $this->optIn);
        $this->assertInstanceOf("Contact\Entity\EntityInterface", $this->optIn);

        $this->assertNull($this->optIn->getId(), 'The "Id" should be null');

        $today = new \DateTime();
        $this->optIn->setDateCreated($today);
        $this->optIn->setDateUpdated($today);

        $id = 1;
        $this->optIn->setId($id);

        $this->assertEquals($today, $this->optIn->getDateCreated(), 'The "DateCreated" should be the same as the setter');
        $this->assertEquals($today, $this->optIn->getDateUpdated(), 'The "DateUpdated" should be the same as the setter');
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
        $hydrator = new DoctrineObject(
            $this->entityManager,
            'Contact\Entity\OptIn'
        );

        $this->optIn = $hydrator->hydrate($this->optInData, new OptIn());

        $this->entityManager->persist($this->optIn);
        $this->entityManager->flush();

        $this->assertInstanceOf('Contact\Entity\OptIn', $this->optIn);
        $this->assertNotNull($this->optIn->getId());
        $this->assertNotNull($this->optIn->getDateCreated());
        $this->assertNotNull($this->optIn->getDateUpdated());
        $this->assertEquals($this->optIn->getOptIn(), $this->optInData['optIn']);

    }

    public function testCanAddOptInToUser()
    {
        $contact = $this->entityManager->find("Contact\Entity\Contact", 1);

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

        $optIn = new \Contact\Entity\OptIn();
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
