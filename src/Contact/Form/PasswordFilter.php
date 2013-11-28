<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Contact
 * @package     Form
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
namespace Contact\Form;

use Zend\InputFilter\InputFilter;

class PasswordFilter extends InputFilter
{
    /**
     * Have a custom password validator
     */
    public function __construct()
    {

        $this->add(array(
            'name'       => 'password',
            'required'   => true,
            'filters'    => array(
                array(
                    'name' => 'StringTrim'
                )
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'min' => 6,
                    ),
                ),
            ),
        ));

        $this->add(array(
            'name'       => 'passwordVerify',
            'required'   => true,
            'filters'    => array(
                array(
                    'name' => 'StringTrim'
                )
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
        ));
    }
}
