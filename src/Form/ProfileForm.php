<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\Form;

use Contact\Entity\Contact;
use Contact\Entity\OptIn;
use Doctrine\ORM\EntityManager;
use DoctrineORMModule\Form\Element\EntityMultiCheckbox;
use Laminas\Form\Element;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\File\IsImage;

/**
 * Class ContactFieldset
 *
 * @package Contact\Form
 */
final class ProfileForm extends Form implements InputFilterProviderInterface
{
    public function __construct(EntityManager $entityManager, Contact $contact)
    {
        parent::__construct();

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
        $this->setAttribute('action', '');

        //Add the basic contact fields
        $contactFieldset = new Profile\ContactFieldset($entityManager);
        $contactFieldset->setUseAsBaseFieldset(true);
        $this->add($contactFieldset);

        //Add the phone information
        $phoneFieldSet = new Profile\PhoneFieldset($entityManager);
        $phoneFieldSet->setUseAsBaseFieldset(true);
        $this->add($phoneFieldSet);

        //Add the address information
        $addressFieldset = new Profile\AddressFieldset($entityManager);
        $addressFieldset->setUseAsBaseFieldset(true);
        $this->add($addressFieldset);

        //Add the organisation information
        $organisationFieldset = new Profile\OrganisationFieldset($entityManager, $contact);
        $organisationFieldset->setUseAsBaseFieldset(true);
        $this->add($organisationFieldset);

        //Add the profile information
        $profileFieldset = new Profile\ProfileFieldset();
        $profileFieldset->setUseAsBaseFieldset(true);
        $this->add($profileFieldset);

        $this->add(
            [
                'type'    => EntityMultiCheckbox::class,
                'name'    => 'optIn',
                'options' => [
                    'use_hidden_element' => true,
                    'target_class'       => OptIn::class,
                    'object_manager'     => $entityManager,
                    'find_method'        => [
                        'name'   => 'findBy',
                        'params' => [
                            'criteria' => [
                                'active' => OptIn::ACTIVE_ACTIVE
                            ],
                            'orderBy'  => [
                                'optIn' => 'ASC']
                        ]
                    ],
                    'label_generator'    => static function (OptIn $optIn) {
                        return sprintf('%s (%s)', $optIn->getOptIn(), $optIn->getDescription());
                    },
                    'label'              => _('txt-select-your-opt-in-label'),
                    'help-block'         => _('txt-select-your-opt-in-help-block'),
                ],
            ]
        );
        $this->add(
            [
                'type'    => Element\File::class,
                'name'    => 'file',
                'options' => [
                    'label'      => _('txt-contact-photo-label'),
                    'help-block' => _('txt-contact-photo-help-block'),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Element\MultiCheckbox::class,
                'name'       => 'removeFile',
                'options'    => [
                    'value_options' => [
                        'delete' => _('txt-check-to-remove-your-photo')
                    ],
                ],
                'attributes' => [
                    'label' => _('txt-remove-photo-label'),
                ]
            ]
        );
        $this->add(
            [
                'type' => Element\Csrf::class,
                'name' => 'csrf',
            ]
        );
        $this->add(
            [
                'type'       => Element\Submit::class,
                'name'       => 'submit',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => _('txt-submit'),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Element\Submit::class,
                'name'       => 'cancel',
                'attributes' => [
                    'class' => 'btn btn-warning',
                    'value' => _('txt-cancel'),
                ],
            ]
        );
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'removeFile' => [
                'required' => false
            ],
            'optIn'      => [
                'required' => false
            ],
            'file'       => [
                'required'   => false,
                'validators' => [
                    [
                        'name' => IsImage::class
                    ]
                ]
            ]
        ];
    }
}
