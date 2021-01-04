<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

namespace ContactTest\Service;

use Contact\InputFilter\FacebookFilter;
use Testing\Util\AbstractInputFilterTest;

/**
 * Class FacebookFilterTest
 *
 * @package ContactTest\Service
 */
class FacebookFilterTest extends AbstractInputFilterTest
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
    public function testCanCreateFacebookFilterInputFilter()
    {
        $facebookFilter = new FacebookFilter($this->getEntityManagerMock());
        $this->assertInstanceOf(FacebookFilter::class, $facebookFilter);
    }


    /**
     *
     */
    public function testContactInputFilterHasElements()
    {
        $facebookFilter = new FacebookFilter($this->getEntityManagerMock());
        $this->assertInstanceOf(FacebookFilter::class, $facebookFilter);
        $this->assertNotNull($facebookFilter->get('contact_entity_facebook'));
        $this->assertNotNull($facebookFilter->get('contact_entity_facebook')->get('facebook'));
        $this->assertNotNull($facebookFilter->get('contact_entity_facebook')->get('public'));
        $this->assertNotNull($facebookFilter->get('contact_entity_facebook')->get('canSendMessage'));
    }
}
