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

use Contact\Entity\Contact;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Validator;
use Laminas\InputFilter\InputFilter;

/**
 * Class ContactFilter
 *
 * @package Contact\InputFilter
 */
final class ContactFilter extends InputFilter
{
    public function __construct(EntityManager $entityManager)
    {
        $inputFilter = new InputFilter();
        $inputFilter->add(
            [
                'name'       => 'email',
                'required'   => true,
                'validators' => [
                    [
                        'name' => 'EmailAddress',
                    ],
                    [
                        'name'    => Validator\UniqueObject::class,
                        'options' => [
                            'object_repository' => $entityManager->getRepository(Contact::class),
                            'object_manager'    => $entityManager,
                            'use_context'       => true,
                            'fields'            => 'email',
                        ],
                    ],
                ],
            ]
        );

        $inputFilter->add(
            [
                'name'     => 'dateOfBirth',
                'required' => false,
            ]
        );

        $inputFilter->add(
            [
                'name'     => 'access',
                'required' => false,
            ]
        );

        $this->add($inputFilter, 'contact_entity_contact');
    }
}
