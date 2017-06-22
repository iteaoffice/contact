<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\InputFilter;

use Zend\InputFilter\InputFilter;

/**
 * Class PasswordFilter
 *
 * @package Contact\InputFilter
 */
class PasswordFilter extends InputFilter
{
    /**
     * Have a custom password validator.
     */
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
