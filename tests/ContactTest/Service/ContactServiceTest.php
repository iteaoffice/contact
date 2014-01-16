<?php
/**
 * ITEA copyright message placeholder
 *
 * @category    ContactTest
 * @package     Entity
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace ContactTest\Service;

use Zend\Crypt\BlockCipher;

use Contact\Entity\Contact;
use Contact\Service\ContactService;
use ContactTest\Bootstrap;

class ContactServiceTest extends \PHPUnit_Framework_TestCase
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
     * @var ContactService;
     */
    protected $contactService;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();
        $this->entityManager  = $this->serviceManager->get('doctrine.entitymanager.orm_default');

        $this->contactService = new ContactService();
        $this->contactService->setServiceLocator($this->serviceManager);
    }

    public function testCanFindContactByEmail()
    {
        $contactEmail = 'test@example.com';
        $contact      = $this->contactService->findContactByEmail($contactEmail);

        $this->assertNotNull($contact);
        $this->assertEquals($contact->getEmail(), $contactEmail);
    }

    public function testCanFindContactByHash()
    {
        $contactId   = 1;
        $blockCipher = BlockCipher::factory('mcrypt', array('algo' => 'aes'));
        $blockCipher->setKey(Contact::CRYPT_KEY);
        $hash = $blockCipher->encrypt($contactId);

        $contact = $this->contactService->findContactByHash($hash);
        $this->assertNotNull($contact);
        $this->assertEquals($contact->getId(), $contactId);
    }

    public function testCanFindAllContacts()
    {
        $contacts = $this->contactService->findAll('contact');
        $this->assertTrue(is_array($contacts));
        foreach ($contacts as $contact) {
            $this->assertInstanceOf('Contact\Entity\Contact', $contact);
        }
    }

    public function testCanFindContactById()
    {
        $contact = $this->contactService->findEntityById('contact', 1);
        $this->isInstanceOf('Contact\Entity\Contact', $contact);
        $this->assertEquals(1, $contact->getId());
    }

    public function testCanUpdateContact()
    {
        $contact = $this->contactService->findEntityById('contact', 1);
        $contact->setPassword('newPassword');
        $this->assertInstanceOf('Contact\Entity\Contact', $this->contactService->updateEntity($contact));
        $contact = $this->contactService->findEntityById('contact', 1);
        $this->assertEquals('newPassword', $contact->getPassword());
        $this->assertEquals(1, $contact->getId());
    }

    public function testCanCreateContact()
    {
        $contact = $this->contactService->findEntityById('contact', 1);
        $contact->setEmail('new-test@example.com');
        $contact = $this->contactService->newEntity($contact);
        $this->assertInstanceOf('Contact\Entity\Contact', $contact);
    }

    public function testCanRemoveContact()
    {
        $contact = $this->contactService->findEntityById('contact', 2);
        $this->assertTrue($this->contactService->removeEntity($contact));

        $contact = $this->contactService->findEntityById('contact', 2);
        $this->assertNull($contact);
    }

    public function getEntity()
    {
        $entity         = 'contact';
        $fullEntity     = $this->contactService->getEntity($entity);
        $fullEntityName = $this->contactService->getFullEntityName($entity);
        $this->assertInstanceOf($fullEntityName, $fullEntity);

    }

}
