<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\InputFilter;

use Laminas\InputFilter\InputFilter;

/**
 * Class FacebookFilter
 *
 * @package Contact\InputFilter
 */
final class FacebookFilter extends InputFilter
{
    public function __construct()
    {
        $inputFilter = new InputFilter();
        $inputFilter->add(
            [
                'name'       => 'facebook',
                'required'   => true,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 80,
                        ],
                    ],
                ],
            ]
        );
        $inputFilter->add(
            [
                'name'     => 'public',
                'required' => true,
            ]
        );
        $inputFilter->add(
            [
                'name'     => 'access',
                'required' => false,
            ]
        );
        $inputFilter->add(
            [
                'name'     => 'canSendMessage',
                'required' => true,
            ]
        );

        $this->add($inputFilter, 'contact_entity_facebook');
    }
}
