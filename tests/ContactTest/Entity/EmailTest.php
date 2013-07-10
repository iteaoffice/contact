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
use Contact\Entity\Email;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;

use ContactTest\Bootstrap;


class EmailTest extends \PHPUnit_Framework_TestCase
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
    protected $emailData;
    /**
     * @var Email;
     */
    protected $email;

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
        $contact->setEmail('email_test@example.com');
        $contact->setState(1);
        $contact->setPassword('password');
        $contact->setMessenger('messenger');
        $contact->setDateOfBirth(new \DateTime());

        $gender = new \General\Entity\Gender();
        $gender->setName('name for EmailTest');
        $gender->setAttention('attention for EmailTest');
        $gender->setSalutation('salutation for EmailTest');

        $contact->setGender($gender);

        $title = new \General\Entity\Title();
        $title->setName('name for EmailTest');
        $title->setAttention('attention for EmailTest');
        $title->setSalutation('salutation for EmailTest');

        $contact->setTitle($title);




        $this->emailData = array(
            'contact' => $contact,
            'email' => 'example@example.com');

        $this->email = new Email();
    }

    public function testCanCreateEntity()
    {
        $this->assertInstanceOf("Contact\Entity\Email", $this->email);
        $this->assertInstanceOf("Contact\Entity\EntityInterface", $this->email);

        $this->assertNull($this->email->getId(), 'The "Id" should be null');

        $today = new \DateTime();
        $this->email->setDateCreated($today);
        $this->email->setDateUpdated($today);

        $id = 1;
        $this->email->setId($id);

        $this->assertEquals($today, $this->email->getDateCreated(), 'The "DateCreated" should be the same as the setter');
        $this->assertEquals($today, $this->email->getDateUpdated(), 'The "DateUpdated" should be the same as the setter');
        $this->assertEquals($id, $this->email->getId(), 'The "Id" should be the same as the setter');

        $this->assertTrue(is_array($this->email->getArrayCopy()));
        $this->assertTrue(is_array($this->email->populate()));
    }

    public function testMagicGettersAndSetters()
    {
        $this->email->email = 'test';
        $this->assertEquals('test', $this->email->email);
    }

    /**
     * @expectedException \Exception
     */
    public function testCannotSetInputFilter()
    {
        $this->email->setInputFilter(new InputFilter());
    }


    public function testHasInputFilter()
    {
        $this->assertInstanceOf('Zend\InputFilter\InputFilter', $this->email->getInputFilter());
    }


    public function testCanSaveEntityInDatabase()
    {
        $hydrator = new DoctrineObject(
            $this->entityManager,
            'Contact\Entity\Email'
        );

        $this->email = $hydrator->hydrate($this->emailData, new Email());
        $this->entityManager->persist($this->email);
        $this->entityManager->flush();

        $this->assertInstanceOf('Contact\Entity\Email', $this->email);
        $this->assertNotNull($this->email->getId());
        $this->assertNotNull($this->email->getDateCreated());
        $this->assertNotNull($this->email->getDateUpdated());
        $this->assertEquals($this->email->getContact()->getId(), $this->emailData['contact']->getId());
        $this->assertEquals($this->email->getEmail(), $this->emailData['email']);

        $this->assertNotNull($this->email->getResourceId());
    }


}
