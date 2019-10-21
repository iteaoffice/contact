<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

namespace ContactTest\Service\Office;

use Contact\Entity\Office\Contact as OfficeContact;
use Contact\Entity\Office\Leave;
use Contact\Repository\Office\LeaveRepository;
use Contact\Service\Office\ContactService;
use DateTime;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\LazyCriteriaCollection;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Persisters\Entity\BasicEntityPersister;
use Testing\Util\AbstractServiceTest;

/**
 * Class ContactServiceTest
 *
 * @package ContactTest\Service\Office
 */
class ContactServiceTest extends AbstractServiceTest
{
    public function testCanCreateService()
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->getEntityManagerMock();
        $service       = new ContactService($entityManager);
        $this->assertInstanceOf(ContactService::class, $service);
    }

    public function testFindLeave()
    {
        /** @var EntityManager $entityManagerMock1 */
        $entityManagerMock1 = $this->getEntityManagerMock();
        $persister          = new BasicEntityPersister(
            $entityManagerMock1,
            new ClassMetadata(Leave::class)
        );

        $repositoryMock = $this->getMockBuilder(LeaveRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['matching'])
            ->getMock();
        $repositoryMock->expects($this->once())
            ->method('matching')
            ->with($this->isInstanceOf(Criteria::class))
            ->willReturn(new LazyCriteriaCollection($persister, new Criteria()));

        /** @var EntityManager $entityManagerMock2 */
        $entityManagerMock2 = $this->getEntityManagerMock(Leave::class, $repositoryMock);

        $service = new ContactService($entityManagerMock2);
        $leave   = $service->findLeave(new OfficeContact(), new DateTime(), new DateTime());

        $this->assertInstanceOf(LazyCriteriaCollection::class, $leave);
    }
}
