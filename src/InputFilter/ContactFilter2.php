<?php
/**
 * Jield webdev copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2015 Jield (http://jield.nl)
 */

namespace Contact\InputFilter;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Validator;
use Zend\InputFilter\InputFilter;

/**
 * Jield webdev copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2015 Jield (http://jield.nl)
 */
class ContactFilter2 extends InputFilter
{
    /**
     * ContactFilter constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $inputFilter = new InputFilter();
        $inputFilter->add([
            'name'       => 'firstName',
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
                        'max'      => 100,
                    ],
                ],
            ],
        ]);
        $inputFilter->add([
            'name'     => 'middleName',
            'required' => false,
        ]);
        $inputFilter->add([
            'name'       => 'lastName',
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
                        'max'      => 100,
                    ],
                ],
            ],
        ]);
        $inputFilter->add([
            'name'     => 'phone',
            'required' => false,
        ]);
        $inputFilter->add([
            'name'     => 'address',
            'required' => false,
        ]);
        $inputFilter->add([
            'name'     => 'community',
            'required' => false,
        ]);
        $inputFilter->add([
            'name'     => 'emailAddress',
            'required' => false,
        ]);
        $inputFilter->add([
            'name'     => 'dateOfBirth',
            'required' => false,
        ]);
        $inputFilter->add([
            'name'     => 'dateEnd',
            'required' => false,
        ]);
        $inputFilter->add([
            'name'     => 'messenger',
            'required' => false,
        ]);
        $inputFilter->add([
            'name'     => 'access',
            'required' => false,
        ]);
        
        $this->add($inputFilter, 'contact');
    }
}
