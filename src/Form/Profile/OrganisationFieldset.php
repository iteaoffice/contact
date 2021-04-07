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

use Contact\Entity\Contact;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use DoctrineORMModule\Form\Element\EntityRadio;
use DoctrineORMModule\Form\Element\EntitySelect;
use General\Entity\Country;
use Laminas\Form\Element;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\Callback;
use Laminas\Validator\NotEmpty;
use Organisation\Entity\Organisation;
use Organisation\Entity\Type;

use function sprintf;

/**
 * Class OrganisationFieldset
 *
 * @package Contact\Form\Profile
 */
final class OrganisationFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(EntityManager $entityManager, Contact $contact)
    {
        parent::__construct('contact_organisation');

        $this->add(
            [
                'type'       => EntityRadio::class,
                'name'       => 'organisation_id',
                'options'    => [
                    'label'                     => _('txt-organisation'),
                    'label_options'             => [
                        'disable_html_escape' => true,
                    ],
                    'escape'                    => false,
                    'disable_inarray_validator' => true,
                    'object_manager'            => $entityManager,
                    'target_class'              => Organisation::class,
                    'find_method'               => [
                        'name'   => 'findOrganisationForProfileEditByContact',
                        'params' => [
                            'criteria' => [],
                            'contact'  => $contact,
                        ],
                    ],
                    'label_generator'           => static function (Organisation $organisation) {
                        if (null !== $organisation->getCountry()) {
                            return sprintf(
                                '%s (%s) [VAT: %s]',
                                $organisation->getOrganisation(),
                                $organisation->getCountry()->getCountry(),
                                (null !== $organisation->getFinancial() ? $organisation->getFinancial()->getVat()
                                    : 'unknown')
                            );
                        }

                        return sprintf('%s', $organisation->getOrganisation());
                    },
                ],
                'attributes' => [
                    'required' => $contact->hasOrganisation(),
                    'id'       => 'organisation',
                ],
            ]
        );

        $this->add(
            [
                'type'       => Element\Text::class,
                'name'       => 'organisation',
                'options'    => [
                    'label'      => _('txt-organisation'),
                    'help-block' => _('txt-organisation-form-element-description'),
                ],
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _('txt-give-your-organisation'),
                ],
            ]
        );
        $this->add(
            [
                'type'    => EntitySelect::class,
                'name'    => 'type',
                'options' => [
                    'label'          => _('txt-organisation-type'),
                    'object_manager' => $entityManager,
                    'empty_option'   => _('txt-choose-an-organisation-type'),
                    'target_class'   => Type::class,
                    'find_method'    => [
                        'name'   => 'findBy',
                        'params' => [
                            'criteria' => [],
                            'orderBy'  => ['description' => Criteria::ASC],
                        ],
                    ],
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
            'organisation'    => [
                'required'   => true,
                'validators' => [
                    new NotEmpty(NotEmpty::NULL),
                    new Callback(
                        [
                            'messages' => [
                                Callback::INVALID_VALUE => 'Please choose an organisation when "None of the above" is chosen',
                            ],
                            'callback' => static function (string $value, array $context) {
                                if (! empty($value)) {
                                    return true;
                                }

                                return ! empty($context['organisation_id']);
                            },
                        ]
                    )
                ]
            ],
            'organisation_id' => [
                'required' => false,
            ],
            'type'         => [
                'required'   => true,
                'validators' => [
                    new NotEmpty(NotEmpty::NULL),
                    new Callback(
                        [
                            'messages' => [
                                Callback::INVALID_VALUE => 'Please choose an organisation type when "None of the above" is chosen',
                            ],
                            'callback' => static function (string $value, array $context) {
                                if (! empty($value)) {
                                    return true;
                                }

                                return ! empty($context['organisation_id']);
                            },
                        ]
                    )
                ]
            ],
            'country'         => [
                'required'   => true,
                'validators' => [
                    new NotEmpty(NotEmpty::NULL),
                    new Callback(
                        [
                            'messages' => [
                                Callback::INVALID_VALUE => 'Please choose a country when "None of the above" is chosen',
                            ],
                            'callback' => static function (string $value, array $context) {
                                if (! empty($value)) {
                                    return true;
                                }

                                return ! empty($context['organisation_id']);
                            },
                        ]
                    )
                ]
            ],
        ];
    }
}
