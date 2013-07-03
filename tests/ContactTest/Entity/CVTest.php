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
use Contact\Entity\Cv;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;

use GeneralTest\Bootstrap;


class CvTest extends \PHPUnit_Framework_TestCase
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
    protected $cvData;
    /**
     * @var Cv;
     */
    protected $cv;
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

        $this->cvData = array(
            'contact' => $this->contact,
            'cv' => file_get_contents(__DIR__ . '/../_files/php.exe'));

        $this->cv = new Cv();
    }

    public function testCanCreateEntity()
    {
        $this->assertInstanceOf("Contact\Entity\Cv", $this->cv);
        $this->assertNull($this->cv->getId(), 'The "Id" should be null');

        $today = new \DateTime();
        $this->cv->setDateCreated($today);
        $this->cv->setDateUpdated($today);

        $id = 1;
        $this->cv->setId($id);

        $this->assertEquals($today, $this->cv->getDateCreated(), 'The "DateCreated" should be the same as the setter');
        $this->assertEquals($today, $this->cv->getDateUpdated(), 'The "DateUpdated" should be the same as the setter');
        $this->assertEquals($id, $this->cv->getId(), 'The "Id" should be the same as the setter');

        $this->assertTrue(is_array($this->cv->getArrayCopy()));
        $this->assertTrue(is_array($this->cv->populate()));
    }

    public function testMagicGettersAndSetters()
    {
        $this->cv->cv = 'test';
        $this->assertEquals('test', $this->cv->cv);
    }

    /**
     * @expectedException \Exception
     */
    public function testCannotSetInputFilter()
    {
        $this->cv->setInputFilter(new InputFilter());
    }


    public function testHasInputFilter()
    {
        $this->assertInstanceOf('Zend\InputFilter\InputFilter', $this->cv->getInputFilter());
    }


    public function testCanSaveEntityInDatabase()
    {
        $hydrator = new DoctrineObject(
            $this->entityManager,
            'Contact\Entity\Cv'
        );

        $this->cv = $hydrator->hydrate($this->cvData, new Cv());
        $this->entityManager->persist($this->cv);
        $this->entityManager->flush();

        $this->assertInstanceOf('Contact\Entity\Cv', $this->cv);
        $this->assertNotNull($this->cv->getId());
        $this->assertNotNull($this->cv->getDateCreated());
        $this->assertNotNull($this->cv->getDateUpdated());
        $this->assertEquals($this->cv->getContact()->getId(), $this->cvData['contact']->getId());
        $this->assertEquals($this->cv->getCv(), $this->cvData['cv']);

        $this->assertNotNull($this->cv->getResourceId());
    }


}
