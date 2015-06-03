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

use Contact\Entity\Contact;
use ContactTest\Bootstrap;
use General\Entity\Gender;
use General\Entity\Title;
use GeneralTest\Entity\GenderTest;
use GeneralTest\Entity\TitleTest;
use Zend\Math\Rand;

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
        $this->entityManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');

    }

    public function provider()
    {
        $genderTest = new GenderTest();
        $titleTest = new TitleTest();

        $contact = new Contact();
        $contact->setFirstName('Jan');
        $contact->setMiddleName('van der');
        $contact->setLastName('Vliet');
        $contact->setEmail('info+' . Rand::getString(4) . '@example.com');
        $contact->setGender($genderTest->provider()[0][0]);
        $contact->setTitle($titleTest->provider()[0][0]);

        return [
            [$contact]
        ];
    }

    public function testCanCreateEntity()
    {
        $contact = new Contact();
        $this->assertInstanceOf("Contact\Entity\Contact", $contact);
        $this->assertInstanceOf("Contact\Entity\EntityInterface", $contact);
        $this->assertNull($contact->getFirstName(), 'The "Firstname" should be null');
        $today = new \DateTime();
        $contact->setDateCreated($today);
        $contact->setLastUpdate($today);
        $id = 1;
        $contact->setId($id);
        $this->assertEquals(
            $today,
            $contact->getDateCreated(),
            'The "DateCreated" should be the same as the setter'
        );
        $this->assertEquals(
            $today,
            $contact->getLastUpdate(),
            'The "LastUpdate" should be the same as the setter'
        );
        $this->assertEquals($id, $contact->getId(), 'The "Id" should be the same as the setter');
        $this->assertTrue(is_array($contact->getArrayCopy()));
        $this->assertTrue(is_array($contact->populate()));
    }

    public function testMagicGettersAndSetters()
    {
        $contact = new Contact();
        $contact->username = 'test';
        $this->assertEquals('test', $contact->username);
    }

    public function testHasInputFilter()
    {
        $contact = new Contact();
        $this->assertInstanceOf('Zend\InputFilter\InputFilter', $contact->getInputFilter());
    }

    /**
     * @param Contact $contact
     *
     * @dataProvider provider
     */
    public function testCanSaveEntityInDatabase(Contact $contact)
    {
        $this->entityManager->persist($contact);
        $this->entityManager->flush();
        $this->assertInstanceOf('Contact\Entity\Contact', $contact);
        $this->assertNotNull($contact->getId());

        $this->assertNotNull($contact->getDisplayName());
    }

}
