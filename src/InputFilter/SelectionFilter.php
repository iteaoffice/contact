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

use Contact\Entity\Selection;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Validator;
use Zend\InputFilter\InputFilter;

/**
 * Class SelectionFilter
 *
 * @package Contact\InputFilter
 */
class SelectionFilter extends InputFilter
{
    /**
     * ContactFilter constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $inputFilter = new InputFilter();
        $inputFilter->add(
            [
                'name'       => 'selection',
                'required'   => true,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => Validator\UniqueObject::class,
                        'options' => [
                            'object_repository' => $entityManager->getRepository(Selection::class),
                            'object_manager'    => $entityManager,
                            'use_context'       => true,
                            'fields'            => 'selection',
                        ],
                    ],
                ],
            ]
        );
        $inputFilter->add(
            [
                'name'     => 'note',
                'required' => false,
                'filters'  => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
            ]
        );
        $inputFilter->add(
            [
                'name'     => 'tag',
                'required' => false,
                'filters'  => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
            ]
        );
        $inputFilter->add(
            [
                'name'     => 'personal',
                'required' => true,
            ]
        );
        $inputFilter->add(
            [
                'name'     => 'private',
                'required' => true,
            ]
        );

        $this->add($inputFilter, 'contact_entity_selection');
    }
}
