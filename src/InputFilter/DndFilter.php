<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\InputFilter;

use Laminas\InputFilter\InputFilter;

/**
 * Class DndFilter
 *
 * @package Contact\InputFilter
 */
final class DndFilter extends InputFilter
{
    public function __construct()
    {
        $inputFilter = new InputFilter();

        $inputFilter->add(
            [
                'name'     => 'file',
                'required' => true,
            ]
        );
        $this->add($inputFilter, 'contact_entity_dnd');
    }
}
