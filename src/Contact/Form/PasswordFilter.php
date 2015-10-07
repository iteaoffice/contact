<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */

namespace Contact\Form;

use Zend\InputFilter\InputFilter;

class PasswordFilter extends InputFilter
{
    /**
     * Have a custom password validator.
     */
    public function __construct()
    {
        $this->add(
            array(
                'name'       => 'password',
                'required'   => true,
                'filters'    => array(
                    array(
                        'name' => 'StringTrim',
                    ),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'min' => 6,
                        ),
                    ),
                ),
            )
        );
        $this->add(
            array(
                'name'       => 'passwordVerify',
                'required'   => true,
                'filters'    => array(
                    array(
                        'name' => 'StringTrim',
                    ),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'min' => 6,
                        ),
                    ),
                    array(
                        'name'    => 'Identical',
                        'options' => array(
                            'token' => 'password',
                        ),
                    ),
                ),
            )
        );
    }
}
