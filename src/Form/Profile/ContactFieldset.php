<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\Form\Profile;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use DoctrineORMModule\Form\Element\EntitySelect;
use General\Entity\Gender;
use General\Entity\Title;
use Laminas\Form\Element;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;

/**
 * Class AddressFieldset
 *
 * @package Contact\Form\Profile
 */
final class ContactFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct('contact');

        $this->add(
            [
                'type'       => Element\Text::class,
                'name'       => 'firstName',
                'options'    => [
                    'label' => _('txt-contact-first-name-label'),
                ],
                'attributes' => [
                    'placeholder' => _('txt-contact-first-name-placeholder'),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Element\Text::class,
                'name'       => 'middleName',
                'options'    => [
                    'label' => _('txt-contact-middle-name-label'),
                ],
                'attributes' => [
                    'placeholder' => _('txt-contact-middle-name-placeholder'),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Element\Text::class,
                'name'       => 'lastName',
                'options'    => [
                    'label' => _('txt-contact-last-name-label'),
                ],
                'attributes' => [
                    'placeholder' => _('txt-contact-last-name-placeholder'),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Element\Text::class,
                'name'       => 'department',
                'options'    => [
                    'label' => _('txt-contact-department-label'),
                ],
                'attributes' => [
                    'placeholder' => _('txt-contact-department-placeholder'),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Element\Text::class,
                'name'       => 'position',
                'options'    => [
                    'label' => _('txt-contact-position-label'),
                ],
                'attributes' => [
                    'placeholder' => _('txt-contact-position-placeholder'),
                ],
            ]
        );

        $this->add(
            [
                'type'    => EntitySelect::class,
                'name'    => 'title',
                'options' => [
                    'label'          => _('txt-title'),
                    'object_manager' => $entityManager,
                    'target_class'   => Title::class,
                    'find_method'    => [
                        'name'   => 'findBy',
                        'params' => [
                            'criteria' => [],
                            'orderBy'  => [
                                'name' => Criteria::ASC
                            ],
                        ],
                    ],
                ],
            ]
        );
        $this->add(
            [
                'type'    => EntitySelect::class,
                'name'    => 'gender',
                'options' => [
                    'label'          => _('txt-gender'),
                    'object_manager' => $entityManager,
                    'target_class'   => Gender::class,
                    'find_method'    => [
                        'name'   => 'findBy',
                        'params' => [
                            'criteria' => [],
                            'orderBy'  => [
                                'name' => Criteria::ASC
                            ],
                        ],
                    ],
                ],
            ]
        );
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'firstName' => [
                'required' => true
            ],
            'lastName'  => [
                'required' => true
            ],
        ];
    }
}
