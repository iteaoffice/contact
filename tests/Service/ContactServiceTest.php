<?php
/**
 * ITEA copyright message placeholder
 *
 * @category    ContactTest
 * @package     Entity
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */
namespace ContactTest\Service;

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

        $this->contactService = new ContactService();
        $this->contactService->setServiceLocator($this->serviceManager);
    }

    public function testCanInstantiate()
    {
        $this->assertInstanceOf(ContactService::class, $this->contactService);
    }


}
