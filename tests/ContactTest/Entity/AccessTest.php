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
use Contact\Entity\Access;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;

use ContactTest\Bootstrap;


class AccessTest extends \PHPUnit_Framework_TestCase
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
    protected $accessData;
    /**
     * @var Access;
     */
    protected $access;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();
        $this->entityManager  = $this->serviceManager->get('doctrine.entitymanager.orm_default');

        $this->accessData = array(
            'access'      => 'office',
            'description' => 'access to the office');

        $this->access = new Access();
    }

    public function testCanCreateEntity()
    {
        $this->assertInstanceOf("Contact\Entity\Access", $this->access);
        $this->assertInstanceOf("Contact\Entity\EntityInterface", $this->access);

        $this->assertNull($this->access->getId(), 'The "Id" should be null');

        $today = new \DateTime();
        $this->access->setDateCreated($today);
        $this->access->setDateUpdated($today);

        $id = 1;
        $this->access->setId($id);

        $this->assertEquals($today, $this->access->getDateCreated(), 'The "DateCreated" should be the same as the setter');
        $this->assertEquals($today, $this->access->getDateUpdated(), 'The "DateUpdated" should be the same as the setter');
        $this->assertEquals($id, $this->access->getId(), 'The "Id" should be the same as the setter');

        $this->assertTrue(is_array($this->access->getArrayCopy()));
        $this->assertTrue(is_array($this->access->populate()));
    }

    public function testMagicGettersAndSetters()
    {
        $this->access->access = 'test';
        $this->assertEquals('test', $this->access->access);
    }

    /**
     * @expectedException \Exception
     */
    public function testCannotSetInputFilter()
    {
        $this->access->setInputFilter(new InputFilter());
    }


    public function testHasInputFilter()
    {
        $this->assertInstanceOf('Zend\InputFilter\InputFilter', $this->access->getInputFilter());
    }


    public function testCanSaveEntityInDatabase()
    {
        $hydrator = new DoctrineObject(
            $this->entityManager,
            'Contact\Entity\Access'
        );

        $this->access = $hydrator->hydrate($this->accessData, new Access());
        $this->entityManager->persist($this->access);
        $this->entityManager->flush();

        $this->assertInstanceOf('Contact\Entity\Access', $this->access);
        $this->assertNotNull($this->access->getId());
        $this->assertNotNull($this->access->getDateCreated());
        $this->assertNotNull($this->access->getDateUpdated());

        $this->assertEquals($this->access->getAccess(), $this->accessData['access']);
    }

    public function testCanAddAccessToUser()
    {
        $contact = $this->entityManager->find("Contact\Entity\Contact", 1);

        $access = new \Contact\Entity\Access();
        $access->setAccess('this is a first access');
        $access->setDescription('This is a first description');

        $contact->setAccess(array($access));
        $this->entityManager->persist($contact);
        $this->entityManager->flush();

        $this->assertEquals(1, sizeof($contact->getAccess()));
    }

    public function testCanAddMultipleAccessToUser()
    {
        $contact = $this->entityManager->find("Contact\Entity\Contact", 1);

        $access = new \Contact\Entity\Access();
        $access->setAccess('this is a first access');
        $access->setDescription('This is a first description');

        $access2 = new \Contact\Entity\Access();
        $access2->setAccess('this is a second access');
        $access2->setDescription('This is a second description');

        $contact->setAccess(array($access, $access2));
        $this->entityManager->persist($contact);
        $this->entityManager->flush();

        $this->assertEquals(2, sizeof($contact->getAccess()));
    }

    public function testCanRemoveMultipleAccessToUser()
    {
        $contact = $this->entityManager->find("Contact\Entity\Contact", 1);

        $access = new \Contact\Entity\Access();
        $access->setAccess('this is a first access');
        $access->setDescription('This is a first description');

        $contact->setAccess(array($access));
        $this->entityManager->persist($contact);
        $this->entityManager->flush();

        $this->assertEquals(1, sizeof($contact->getAccess()));
    }
}
