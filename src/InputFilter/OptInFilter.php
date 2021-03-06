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

use Contact\Entity\OptIn;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Validator;
use Laminas\InputFilter\InputFilter;

/**
 * Class OptInFilter
 *
 * @package Contact\InputFilter
 */
final class OptInFilter extends InputFilter
{
    public function __construct(EntityManager $entityManager)
    {
        $inputFilter = new InputFilter();
        $inputFilter->add(
            [
                'name'       => 'optIn',
                'required'   => true,
                'validators' => [
                    [
                        'name'    => Validator\UniqueObject::class,
                        'options' => [
                            'object_repository' => $entityManager->getRepository(OptIn::class),
                            'object_manager'    => $entityManager,
                            'use_context'       => true,
                            'fields'            => 'optIn',
                        ],
                    ],
                ],
            ]
        );

        $inputFilter->add(
            [
                'name'     => 'description',
                'required' => true,
            ]
        );
        $this->add($inputFilter, 'contact_entity_optin');
    }
}
