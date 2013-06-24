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

class ContactTest extends \PHPUnit_Framework_TestCase
{

    protected $bootstrap;
    /**
     * @var ServiceManager
     */
    protected $sm;

    /**
     * setup ZF application and service manager
     * - $this->application Zend\Mvc\Application
     * - $this->sm          Zend\ServiceManager\ServiceManager
     *
     * @return void
     */
//    public function setUp()
//    {
//        $application = require 'C:\Users\jvdheide\wamp\www\itea\tests\Bootstrap.php';
//        $this->application = $application;
//        $this->sm = $application->getServiceManager();
//    }


    public function testCanCreateEntity()
    {
        $contact = new Contact();
        $this->assertInstanceOf("Contact\Entity\Contact", $contact);
    }

    public function testHasFilter()
    {
        $project = new Contact();
        return $this->assertInstanceOf('Zend\InputFilter\InputFilter', $project->getInputFilter());
    }

}
