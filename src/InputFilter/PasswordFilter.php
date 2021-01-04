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
 * Class PasswordFilter
 *
 * @package Contact\InputFilter
 */
final class PasswordFilter extends InputFilter
{
    public function __construct()
    {
        $this->add(
            [
                'name'       => 'password',
                'required'   => true,
                'filters'    => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 6,
                        ],
                    ],
                ],
            ]
        );
        $this->add(
            [
                'name'       => 'passwordVerify',
                'required'   => true,
                'filters'    => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 6,
                        ],
                    ],
                    [
                        'name'    => 'Identical',
                        'options' => [
                            'token' => 'password',
                        ],
                    ],
                ],
            ]
        );
    }
}
