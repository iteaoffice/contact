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

use Contact\Entity\Contact;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Validator;
use Organisation\Entity\Organisation;
use Zend\InputFilter\InputFilter;

/**
 * Jield webdev copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2015 Jield (http://jield.nl)
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
        $inputFilter->add([
            'name'       => 'email',
            'required'   => true,
            'validators' => [
                [
                    'name' => 'Emailaddress',
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
        ]);


        $inputFilter->add([
            'name'       => 'organisation',
            'required'   => false,
            'validators' => [
                [
                    'name'    => Validator\ObjectExists::class,
                    'options' => [
                        'object_repository' => $entityManager->getRepository(Organisation::class),
                        'object_manager'    => $entityManager,
                        'use_context'       => true,
                        'fields'            => 'organisation',
                    ],
                ],
            ],

        ]);

        $this->add($inputFilter, 'contact');
    }
}
