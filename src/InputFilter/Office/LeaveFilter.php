<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\InputFilter\Office;

use Laminas\InputFilter\InputFilter;

/**
 * Class LeaveFilter
 *
 * @package Contact\InputFilter\Office
 */
final class LeaveFilter extends InputFilter
{
    public function __construct()
    {
        $inputFilter = new InputFilter();
        $inputFilter->add(
            [
                'name'     => 'officeContact',
                'required' => false
            ]
        );

        $this->add($inputFilter, 'contact_entity_office_leave');
    }
}
