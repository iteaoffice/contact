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

use Contact\Entity\Selection;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Validator;
use Laminas\InputFilter\InputFilter;

/**
 * Class SelectionFilter
 *
 * @package Contact\InputFilter
 */
final class SelectionFilter extends InputFilter
{
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
                'name'     => 'type',
                'required' => false,
            ]
        );

        $this->add($inputFilter, 'contact_entity_selection');
    }
}
