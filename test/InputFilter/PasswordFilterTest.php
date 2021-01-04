<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

namespace ContactTest\Service;

use Contact\InputFilter\PasswordFilter;
use Testing\Util\AbstractInputFilterTest;

/**
 * Class PasswordFilterTest
 *
 * @package ContactTest\Service
 */
class PasswordFilterTest extends AbstractInputFilterTest
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
    public function testCanCreatePasswordFilterInputFilter()
    {
        $passwordFilter = new PasswordFilter($this->getEntityManagerMock());
        $this->assertInstanceOf(PasswordFilter::class, $passwordFilter);
    }


    /**
     *
     */
    public function testContactInputFilterHasElements()
    {
        $passwordFilter = new PasswordFilter($this->getEntityManagerMock());
        $this->assertInstanceOf(PasswordFilter::class, $passwordFilter);
        $this->assertNotNull($passwordFilter->get('password'));
        $this->assertNotNull($passwordFilter->get('passwordVerify'));
    }
}
