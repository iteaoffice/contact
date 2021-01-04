<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
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
