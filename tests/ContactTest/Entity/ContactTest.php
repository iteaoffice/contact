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

use Zend\Crypt\BlockCipher;

use Contact\Entity\Contact;
use ContactTest\Bootstrap;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;

use General\Entity\Title;
use General\Entity\Gender;


class ContactTest extends \PHPUnit_Framework_TestCase
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
     * @var Contact;
     */
    protected $contact;
    /**
     * @var array
     */
    protected $contactData;
    /**
     * @var Gender;
     */
    protected $gender;
    /**
     * @var Title;
     */
    protected $title;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();
        $this->entityManager = $this->serviceManager->get('doctrine.entitymanager.orm_default');

        $this->gender = new Gender();
        $this->gender->setName("This is the gender");
        $this->gender->setAttention("attention for ContactTest");
        $this->gender->setSalutation("Salutation for ContactTest");

        $this->title = new Title();

        $this->title->setName("This is the title");
        $this->title->setAttention("Attention for ContactTest");
        $this->title->salutation = "Salutation for ContactTest";


        $this->contactData = array(
            'firstName' => 'Jan',
            'middleName' => 'van der',
            'lastName' => 'Vliet',
            'email' => 'info@example.com',
            'state' => 1,
            'password' => md5(microtime()),
            'department' => 'department',
            'position' => 'position',
            'dateEnd' => new \DateTime(),
            'messenger' => 'Lorem Ipsum',
            'gender' => $this->gender,
            'title' => $this->title,
        );

        $this->contact = new Contact();
    }

    public function testCanCreateEntity()
    {

        $this->assertInstanceOf("Contact\Entity\Contact", $this->contact);
        $this->assertInstanceOf("Contact\Entity\EntityInterface", $this->contact);

        $this->assertNull($this->contact->getFirstName(), 'The "Firstname" should be null');

        $today = new \DateTime();
        $this->contact->setDateCreated($today);
        $this->contact->setLastUpdate($today);

        $id = 1;
        $this->contact->setId($id);

        $this->assertEquals($today, $this->contact->getDateCreated(), 'The "DateCreated" should be the same as the setter');
        $this->assertEquals($today, $this->contact->getLastUpdate(), 'The "LastUpdate" should be the same as the setter');
        $this->assertEquals($id, $this->contact->getId(), 'The "Id" should be the same as the setter');

        $this->assertTrue(is_array($this->contact->getArrayCopy()));
        $this->assertTrue(is_array($this->contact->populate()));
    }

    public function testMagicGettersAndSetters()
    {
        $this->contact->username = 'test';
        $this->assertEquals('test', $this->contact->username);
    }


    public function testCanHydrateEntity()
    {
        $hydrator = new DoctrineObject(
            $this->entityManager,
            'Contact\Entity\Contact'
        );

        $this->contact = $hydrator->hydrate($this->contactData, new Contact());

        $dataArray = $hydrator->extract($this->contact);

        $this->assertSame($this->contactData['firstName'], $dataArray['firstName']);
        $this->assertSame($this->contactData['middleName'], $dataArray['middleName']);
        $this->assertSame($this->contactData['lastName'], $dataArray['lastName']);
        $this->assertSame($this->contactData['state'], 1);
        $this->assertSame($this->contactData['gender']->getName(), $this->gender->getName());
        $this->assertSame($this->contactData['gender']->getId(), $this->gender->getId());
        $this->assertSame($this->contactData['title']->getName(), $this->title->getName());
        $this->assertSame($this->contactData['title']->getId(), $this->title->getId());
    }

    public function testHasInputFilter()
    {
        $this->assertInstanceOf('Zend\InputFilter\InputFilter', $this->contact->getInputFilter());
    }

    public function testCanSaveEntityInDatabase()
    {
        $hydrator = new DoctrineObject(
            $this->entityManager,
            'Contact\Entity\Contact'
        );

        $this->contact = $hydrator->hydrate($this->contactData, new Contact());
        $this->entityManager->persist($this->contact);
        $this->entityManager->flush();

        $this->assertInstanceOf('Contact\Entity\Contact', $this->contact);
        $this->assertNotNull($this->contact->getId());
        $this->assertSame($this->contactData['firstName'], $this->contact->getFirstName());
        $this->assertSame($this->contactData['middleName'], $this->contact->getMiddleName());
        $this->assertSame($this->contactData['lastName'], $this->contact->getLastName());
        $this->assertSame($this->contactData['department'], $this->contact->getDepartment());
        $this->assertSame($this->contactData['position'], $this->contact->getPosition());
        $this->assertSame($this->contactData['email'], $this->contact->getUsername()); //We use the email addressa as username
        $this->assertSame($this->contactData['state'], $this->contact->getState());
        $this->assertSame($this->contactData['gender']->getName(), $this->contact->getGender()->getName());
        $this->assertSame($this->contactData['gender']->getId(), $this->contact->getGender()->getId());
        $this->assertSame($this->contactData['title']->getName(), $this->contact->getTitle()->getName());
        $this->assertSame($this->contactData['title']->getId(), $this->contact->getTitle()->getId());


        $this->assertNotNull($this->contact->getDisplayName());
    }

    public function testParseHash()
    {
        $contactId = 1;
        $this->contact->setId($contactId);
        $blockCipher = BlockCipher::factory('mcrypt', array('algo' => 'aes'));
        $blockCipher->setKey(Contact::CRYPT_KEY);
        $result = $blockCipher->decrypt($this->contact->parseHash());
        $this->assertEquals($result, $contactId);
    }

    public function testDecryptHash()
    {
        $contactId = 1;

        $blockCipher = BlockCipher::factory('mcrypt', array('algo' => 'aes'));
        $blockCipher->setKey(Contact::CRYPT_KEY);
        $hash = $blockCipher->encrypt($contactId);

        $result = $this->contact->decryptHash($hash);

        $this->assertEquals($result, $contactId);
    }


}
