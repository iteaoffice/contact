<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\InputFilter;

use Contact\Entity\OptIn;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Validator;
use Zend\InputFilter\InputFilter;

/**
 * Class ContactFilter
 *
 * @package Contact\InputFilter
 */
class OptInFilter extends InputFilter
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
