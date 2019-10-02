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

namespace Contact\Form;

use Contact\Entity\OptIn;
use Doctrine\ORM\EntityManager;
use DoctrineORMModule\Form\Element\EntityMultiCheckbox;
use Zend\Form\Element\Submit;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use function sprintf;

final class ProfileBody extends Form implements InputFilterProviderInterface
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct();
        $this->setAttribute('action', '');
        $this->setAttribute('class', 'form-horizontal');

        $this->add(
            [
                'name'       => 'password',
                'options'    => [
                    'label'      => _('txt-new-password'),
                    'help-block' => _('txt-new-password-form-help'),
                ],
                'attributes' => [
                    'type' => 'password',
                ],
            ]
        );
        $this->add(
            [
                'name'       => 'passwordVerify',
                'options'    => [
                    'label' => _('txt-new-password-verify'),
                ],
                'attributes' => [
                    'type' => 'password',
                ],
            ]
        );
        $this->add(
            [
                'type'    => EntityMultiCheckbox::class,
                'name'    => 'optIn',
                'options' => [
                    'target_class'    => OptIn::class,
                    'object_manager'  => $entityManager,
                    'find_method'     => [
                        'name'   => 'findBy',
                        'params' => [
                            'criteria' => [
                                'active' => OptIn::ACTIVE_ACTIVE
                            ],
                            'orderBy'  => [
                                'optIn' => 'ASC']
                        ]
                    ],
                    'label_generator' => static function (OptIn $optIn) {
                        return sprintf('%s (%s)', $optIn->getOptIn(), $optIn->getDescription());
                    },
                    'label'           => _('txt-select-your-opt-in-label'),
                    'help-block'      => _('txt-select-your-opt-in-help-block'),
                ],


            ]
        );
        $this->add(
            [
                'type'       => Submit::class,
                'name'       => 'submit',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => _('txt-submit'),
                ],
            ]
        );
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'password'       => [
                'required'   => true,
                'filters'    => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 6,
                        ],
                    ],
                ],
            ],
            'passwordVerify' => [
                'required'   => true,
                'filters'    => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 6,
                        ],
                    ],
                    [
                        'name'    => 'Identical',
                        'options' => [
                            'token' => 'password',
                        ],
                    ],
                ],
            ],
            'optIn'          => [
                'required' => false
            ],
        ];
    }
}
