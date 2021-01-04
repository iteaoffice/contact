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

use Doctrine\ORM\EntityManager;
use DoctrineORMModule\Form\Element\EntitySelect;
use General\Entity\Country;
use Laminas\Form\Element;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\Callback;
use Laminas\Validator\NotEmpty;

/**
 * Class AddressFieldset
 *
 * @package Contact\Form\Profile
 */
final class AddressFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct('address');

        $this->add(
            [
                'type'       => Element\Text::class,
                'name'       => 'address',
                'options'    => [
                    'label' => _('txt-address'),
                ],
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _('txt-address'),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Element\Text::class,
                'name'       => 'zipCode',
                'options'    => [
                    'label' => _('txt-zip-code'),
                ],
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _('txt-zip-code'),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Element\Text::class,
                'name'       => 'city',
                'options'    => [
                    'label' => _('txt-city'),
                ],
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _('txt-city'),
                ],
            ]
        );
        $this->add(
            [
                'type'    => EntitySelect::class,
                'name'    => 'country',
                'options' => [
                    'label'          => _('txt-country'),
                    'object_manager' => $entityManager,
                    'target_class'   => Country::class,
                    'find_method'    => [
                        'name'   => 'findForForm',
                        'params' => [
                            'criteria' => [],
                            'orderBy'  => [],
                        ],
                    ],
                ],
            ]
        );
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'address' => [
                'required'   => true,
                'validators' => [
                    new NotEmpty(NotEmpty::NULL),
                    new Callback(
                        [
                            'messages' => [
                                Callback::INVALID_VALUE => 'Please give a the address',
                            ],
                            'callback' => static function (string $value, array $context) {
                                if (
                                    empty($value) && empty($context['zipCode']) && empty($context['city'])
                                    && empty($context['country'])
                                ) {
                                    return true;
                                }
                                if (
                                    empty($value)
                                    && (! empty($context['zipCode']) || ! empty($context['city'])
                                        || ! empty($context['country']))
                                ) {
                                    return false;
                                }

                                return true;
                            },
                        ]
                    )
                ]
            ],
            'zipCode' => [
                'required'   => true,
                'validators' => [
                    new NotEmpty(NotEmpty::NULL),
                    new Callback(
                        [
                            'messages' => [
                                Callback::INVALID_VALUE => 'Please give a the zip code',
                            ],
                            'callback' => static function (string $value, array $context) {
                                if (
                                    empty($value) && empty($context['address']) && empty($context['city'])
                                    && empty($context['country'])
                                ) {
                                    return true;
                                }
                                if (
                                    empty($value)
                                    && (! empty($context['address']) || ! empty($context['city'])
                                        || ! empty($context['country']))
                                ) {
                                    return false;
                                }

                                return true;
                            },
                        ]
                    )
                ]
            ],
            'city'    => [
                'required'   => true,
                'validators' => [
                    new NotEmpty(NotEmpty::NULL),
                    new Callback(
                        [
                            'messages' => [
                                Callback::INVALID_VALUE => 'Please give a the city',
                            ],
                            'callback' => static function (string $value, array $context) {
                                if (
                                    empty($value) && empty($context['address']) && empty($context['zipCode'])
                                    && empty($context['country'])
                                ) {
                                    return true;
                                }
                                if (
                                    empty($value)
                                    && (! empty($context['address']) || ! empty($context['zipCode'])
                                        || ! empty($context['country']))
                                ) {
                                    return false;
                                }

                                return true;
                            },
                        ]
                    )
                ]
            ],
            'country' => [
                'required'   => true,
                'validators' => [
                    new NotEmpty(NotEmpty::NULL),
                    new Callback(
                        [
                            'messages' => [
                                Callback::INVALID_VALUE => 'Please select a country',
                            ],
                            'callback' => static function (string $value, array $context) {
                                if (
                                    empty($value) && empty($context['address']) && empty($context['zipCode'])
                                    && empty($context['city'])
                                ) {
                                    return true;
                                }

                                if (
                                    empty($value)
                                    && (! empty($context['address']) || ! empty($context['zipCode'])
                                        || ! empty($context['city']))
                                ) {
                                    return false;
                                }

                                return true;
                            },
                        ]
                    )
                ]
            ],
        ];
    }
}
