<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
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
