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

use Contact\Entity\Contact;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use GeneralTest\Bootstrap;

class ContactTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceManager;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();
    }

    public function testCanCreateEntity()
    {
        $contact = new Contact();
        $this->assertInstanceOf("Contact\Entity\Contact", $contact);

        $this->assertNull($contact->getFirstName(), 'The "Firstname" should be null');
    }

//    public function testExchangeArraySetsPropertiesCorrectly()
//    {
//        $contact = new Contact();
//        $data = array(
//            'firstName' => 'Jan',
//            'middleName' => 'van der',
//            'lastName' => 'Vliet',
//            'email' => 'info@example.com',
//            'password' => md5(microtime()),
//            'position' => 1,
//            'addDate' => new \DateTime(),
//            'lastUpdate' => new \DateTime(),
//            'dateEnd' => new \DateTime(),
//            'messenger' => 'Lorem Ipsum',
//            'gender' => 1,
//            'title' => 1,
//        );
//
//        $contact->exchangeArray($data);
//
//        $this->assertSame($data['firstName'], $contact->getFirstName(), '"firstname" was not set correctly');
//        $this->assertSame($data['middleName'], $contact->getMiddleName(), '"middleName" was not set correctly');
//        $this->assertSame($data['lastName'], $contact->getLastName(), '"lastName" was not set correctly');
//    }


    public function testCanHydrateEntity()
    {

        $hydrator = new DoctrineObject(
            $this->serviceManager->get('doctrine.entitymanager.orm_default'),
            'Contact\Entity\Contact'
        );


        $contact = new Contact();
        $data = array(
            'firstName' => 'Jan',
            'middleName' => 'van der',
            'lastName' => 'Vliet',
            'email' => 'info@example.com',
            'password' => md5(microtime()),
            'position' => 1,
            'addDate' => new \DateTime(),
            'lastUpdate' => new \DateTime(),
            'dateEnd' => new \DateTime(),
            'messenger' => 'Lorem Ipsum',
            'gender' => 1,
            'title' => 1,
        );

        $contact = $hydrator->hydrate($data, $contact);

        $dataArray = $hydrator->extract($contact);

        $this->assertSame($data['firstName'], $dataArray['firstName']);
        $this->assertSame($data['middleName'], $dataArray['middleName']);
        $this->assertSame($data['lastName'], $dataArray['lastName']);
    }


}
