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
use Contact\Entity\Web;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;

use ContactTest\Bootstrap;

class WebTest extends \PHPUnit_Framework_TestCase
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
    protected $webData;
    /**
     * @var Web;
     */
    protected $web;
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

        $this->webData = array(
            'contact' => $this->contact,
            'web' => 'http://www.example.com');

        $this->web = new Web();
    }

    public function testCanCreateEntity()
    {
        $this->assertInstanceOf("Contact\Entity\Web", $this->web);
        $this->assertNull($this->web->getId(), 'The "Id" should be null');

        $id = 1;
        $this->web->setId($id);
        $this->assertEquals($id, $this->web->getId(), 'The "Id" should be the same as the setter');

        $this->assertTrue(is_array($this->web->getArrayCopy()));
        $this->assertTrue(is_array($this->web->populate()));
    }

    /**
     * @expectedException \Exception
     */
    public function testCannotSetInputFilter()
    {
        $this->web->setInputFilter(new InputFilter());
    }

    public function testHasFilter()
    {
        return $this->assertInstanceOf('Zend\InputFilter\InputFilter', $this->web->getInputFilter());
    }

    public function testMagicGettersAndSetters()
    {
        $this->web->web = 'test';
        $this->assertEquals('test', $this->web->web);
    }


    public function testCanSaveEntityInDatabase()
    {
        $hydrator = new DoctrineObject(
            $this->entityManager,
            'Contact\Entity\Web'
        );

        $this->web = $hydrator->hydrate($this->webData, new Web());
        $this->entityManager->persist($this->web);
        $this->entityManager->flush();

        $this->assertInstanceOf('Contact\Entity\Web', $this->web);
        $this->assertNotNull($this->web->getId());
        $this->assertNotNull($this->web->getResourceId());
        $this->assertEquals($this->web->getContact()->getId(), $this->webData['contact']->getId());
        $this->assertEquals($this->web->getWeb(), $this->webData['web']);
    }


}
