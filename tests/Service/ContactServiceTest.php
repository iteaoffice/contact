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

/**
 * Class ContactServiceTest
 *
 * @package ContactTest\Service
 */
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

    public function testCanInstantiate()
    {
        $this->assertInstanceOf(ContactService::class, new ContactService());
    }


}
