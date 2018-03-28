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

use Contact\Entity\Contact;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Validator;
use Organisation\Entity\Organisation;
use Zend\InputFilter\InputFilter;

/**
 * Class ContactFilter
 *
 * @package Contact\InputFilter
 */
class ContactFilter extends InputFilter
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


        $inputFilter->add(
            [
                'name'       => 'organisation',
                'required'   => false,
                'validators' => [
                    [
                        'name'    => Validator\ObjectExists::class,
                        'options' => [
                            'object_repository' => $entityManager->getRepository(Organisation::class),
                            'object_manager'    => $entityManager,
                            'use_context'       => true,
                            'fields'            => 'id',
                        ],
                    ],
                ],

            ]
        );

        $this->add($inputFilter, 'contact_entity_contact');
    }
}
