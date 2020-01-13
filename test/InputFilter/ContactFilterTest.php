<?php

/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

namespace ContactTest\Service;

use Contact\Entity;
use Contact\InputFilter\ContactFilter;
use Contact\Repository;
use Doctrine\ORM\EntityManager;
use Organisation\Repository\Organisation;
use Testing\Util\AbstractInputFilterTest;

/**
 * Class ContactFilterTest
 *
 * @package ContactTest\Service
 */
class ContactFilterTest extends AbstractInputFilterTest
{
    /**
     * Set up basic properties
     */
    public function setUp(): void
    {
    }

    /**
     *
     */
    public function testCanCreateContactInputFilter()
    {
        $entityManagerMockBuilder = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor();
        $entityManagerMockBuilder->setMethods(['getRepository']);
/** @var EntityManager $entityManagerMock */
        $entityManagerMock = $entityManagerMockBuilder->getMock();
// Mock the repository, disabling the constructor
        $contactRepositoryMock = $this->getMockBuilder(Repository\Contact::class)->disableOriginalConstructor()
            ->getMock();
        $organisationRepositoryMock = $this->getMockBuilder(Organisation::class)->disableOriginalConstructor()->getMock();
        $map = [
            [Entity\Contact::class, $contactRepositoryMock],
            [\Organisation\Entity\Organisation::class, $organisationRepositoryMock]
        ];
        $entityManagerMock->expects($this->atLeastOnce())
            ->method('getRepository')
            ->will($this->returnValueMap($map));
        $contactFilter = new ContactFilter($entityManagerMock);
        $this->assertInstanceOf(ContactFilter::class, $contactFilter);
    }

    public function testContactInputFilterHasElements(): void
    {
        $entityManagerMockBuilder = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor();
        $entityManagerMockBuilder->onlyMethods(['getRepository']);
/** @var EntityManager $entityManagerMock */
        $entityManagerMock = $entityManagerMockBuilder->getMock();
// Mock the repository, disabling the constructor
        $contactRepositoryMock = $this->getMockBuilder(Repository\Contact::class)->disableOriginalConstructor()
            ->getMock();
        $organisationRepositoryMock = $this->getMockBuilder(Organisation::class)->disableOriginalConstructor()->getMock();
        $map = [
            [Entity\Contact::class, $contactRepositoryMock],
            [\Organisation\Entity\Organisation::class, $organisationRepositoryMock]
        ];
        $entityManagerMock->expects($this->atLeastOnce())
            ->method('getRepository')
            ->will($this->returnValueMap($map));
        $contactFilter = new ContactFilter($entityManagerMock);
        $this->assertNotNull($contactFilter->get('contact_entity_contact'));
        $this->assertNotNull($contactFilter->get('contact_entity_contact')->get('email'));
        $this->assertNotNull($contactFilter->get('contact_entity_contact')->get('dateOfBirth'));
        $this->assertNotNull($contactFilter->get('contact_entity_contact')->get('access'));
    }
}
