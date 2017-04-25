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

namespace ContactTest\Service;

use Contact\Service\ContactService;
use Testing\Util\AbstractServiceTest;

/**
 * Class ContactServiceTest
 *
 * @package ContactTest\Service
 */
class ContactServiceTest extends AbstractServiceTest
{
    /**
     *
     */
    public function testCanCreateService()
    {
        $service = new ContactService();
        $this->assertInstanceOf(ContactService::class, $service);
    }
}