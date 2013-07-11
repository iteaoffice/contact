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
use Contact\Entity\Dnd;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;

use ContactTest\Bootstrap;


class DndTest extends \PHPUnit_Framework_TestCase
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
    protected $dndData;
    /**
     * @var Dnd;
     */
    protected $dnd;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();
        $this->entityManager  = $this->serviceManager->get('doctrine.entitymanager.orm_default');

        $contact = $this->entityManager->find("Contact\Entity\Contact", 1);

        $this->dndData = array(
            'contact' => $contact,
            'dnd'      => file_get_contents(__DIR__ . '/../_files/php.exe'));

        $this->dnd = new Dnd();
    }

    public function testCanCreateEntity()
    {
        $this->assertInstanceOf("Contact\Entity\Dnd", $this->dnd);
        $this->assertInstanceOf("Contact\Entity\EntityInterface", $this->dnd);

        $this->assertNull($this->dnd->getId(), 'The "Id" should be null');

        $today = new \DateTime();
        $this->dnd->setDateCreated($today);
        $this->dnd->setDateUpdated($today);

        $id = 1;
        $this->dnd->setId($id);

        $this->assertEquals($today, $this->dnd->getDateCreated(), 'The "DateCreated" should be the same as the setter');
        $this->assertEquals($today, $this->dnd->getDateUpdated(), 'The "DateUpdated" should be the same as the setter');
        $this->assertEquals($id, $this->dnd->getId(), 'The "Id" should be the same as the setter');

        $this->assertTrue(is_array($this->dnd->getArrayCopy()));
        $this->assertTrue(is_array($this->dnd->populate()));
    }

    public function testMagicGettersAndSetters()
    {
        $this->dnd->dnd = 'test';
        $this->assertEquals('test', $this->dnd->dnd);
    }

    /**
     * @expectedException \Exception
     */
    public function testCannotSetInputFilter()
    {
        $this->dnd->setInputFilter(new InputFilter());
    }


    public function testHasInputFilter()
    {
        $this->assertInstanceOf('Zend\InputFilter\InputFilter', $this->dnd->getInputFilter());
    }


    public function testCanSaveEntityInDatabase()
    {
        $hydrator = new DoctrineObject(
            $this->entityManager,
            'Contact\Entity\Dnd'
        );

        $this->dnd = $hydrator->hydrate($this->dndData, new Dnd());
        $this->entityManager->persist($this->dnd);
        $this->entityManager->flush();

        $this->assertInstanceOf('Contact\Entity\Dnd', $this->dnd);
        $this->assertNotNull($this->dnd->getId());
        $this->assertNotNull($this->dnd->getDateCreated());
        $this->assertNotNull($this->dnd->getDateUpdated());
        $this->assertEquals($this->dnd->getContact()->getId(), $this->dndData['contact']->getId());
        $this->assertEquals($this->dnd->getDnd(), $this->dndData['dnd']);

        $this->assertNotNull($this->dnd->getResourceId());
    }


}
