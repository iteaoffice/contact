<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\InputFilter\Selection;

use Contact\Entity\Selection\Type;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Validator;
use Laminas\InputFilter\InputFilter;

/**
 * Class TypeFilter
 * @package Contact\InputFilter\Selection
 */
final class TypeFilter extends InputFilter
{
    public function __construct(EntityManager $entityManager)
    {
        $inputFilter = new InputFilter();
        $inputFilter->add(
            [
                'name'       => 'name',
                'required'   => true,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => Validator\UniqueObject::class,
                        'options' => [
                            'object_repository' => $entityManager->getRepository(Type::class),
                            'object_manager'    => $entityManager,
                            'use_context'       => true,
                            'fields'            => 'name',
                        ],
                    ],
                ],
            ]
        );


        $this->add($inputFilter, 'contact_entity_selection_type');
    }
}
