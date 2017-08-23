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

use Contact\Entity\Contact;
use Contact\Service\FormService;
use Testing\Util\AbstractServiceTest;
use Zend\Form\Form;

/**
 * Class ContactServiceTest
 *
 * @package ContactTest\Service
 */
class FormServiceTest extends AbstractServiceTest
{
    /**
     *
     */
    public function testCanCreateService()
    {
        $service = new FormService();
        $this->assertInstanceOf(FormService::class, $service);
    }
}