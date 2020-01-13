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

use Contact\Service\Office\ContactService;
use Doctrine\ORM\EntityManager;
use Testing\Util\AbstractServiceTest;

/**
 * Class ContactServiceTest
 *
 * @package ContactTest\Service\Office
 */
class ContactServiceTest extends AbstractServiceTest
{
    public function testCanCreateService(): void
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->getEntityManagerMock();
        $service = new ContactService($entityManager);
        $this->assertInstanceOf(ContactService::class, $service);
    }
}
