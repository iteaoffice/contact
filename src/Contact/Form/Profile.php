<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Project
 * @package     Form
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Contact\Form;

use Contact\Entity\Contact;
use Contact\Entity\PhoneType;
use Contact\Entity\Profile as ProfileEntity;
use Contact\Hydrator\Profile as ProfileHydrator;
use DoctrineORMModule\Options\EntityManager;
use General\Service\GeneralService;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 */
class Profile extends Form
{

    /**
     * Class constructor
     */
    public function __construct(ServiceLocatorInterface $serviceLocator, Contact $contact)
    {
        parent::__construct();
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
        /**
         * @var $entityManager EntityManager
         */
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $contactService = $serviceLocator->get('contact_contact_service');
        $generalService = $serviceLocator->get(GeneralService::class);
        $doctrineHydrator = new ProfileHydrator($entityManager);
        $this->setHydrator($doctrineHydrator)->setObject($contact);
        /**
         * Add a hidden form element for the id to allow a check on the uniqueness of some elements
         */
        $this->add(
            [
                'type' => 'Zend\Form\Element\Hidden',
                'name' => 'id',
            ]
        );
        $this->add(
            [
                'type'       => 'DoctrineORMModule\Form\Element\EntitySelect',
                'name'       => 'gender',
                'options'    => [
                    'label'          => _("txt-attention"),
                    'object_manager' => $entityManager,
                    'target_class'   => 'General\Entity\Gender',
                    'find_method'    => [
                        'name' => 'findAll',
                    ]
                ],
                'attributes' => [
                    'required' => true,
                ]
            ]
        );
        $this->add(
            [
                'type'       => 'DoctrineORMModule\Form\Element\EntitySelect',
                'name'       => 'title',
                'options'    => [
                    'label'          => _("txt-title"),
                    'object_manager' => $entityManager,
                    'target_class'   => 'General\Entity\Title',
                    'find_method'    => [
                        'name' => 'findAll',
                    ]
                ],
                'attributes' => [
                    'required' => true,
                ]
            ]
        );
        $this->add(
            [
                'type'       => 'Zend\Form\Element\Text',
                'name'       => 'firstName',
                'options'    => [
                    'label' => _("txt-first-name"),
                ],
                'attributes' => [
                    'class'       => 'form-control',
                    'required'    => true,
                    'placeholder' => _("txt-give-your-first-name"),
                ]
            ]
        );
        $this->add(
            [
                'type'       => 'Zend\Form\Element\Text',
                'name'       => 'middleName',
                'options'    => [
                    'label' => _("txt-middle-name"),
                ],
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _("txt-give-your-middle-name"),
                ]
            ]
        );
        $this->add(
            [
                'type'       => 'Zend\Form\Element\Text',
                'name'       => 'lastName',
                'options'    => [
                    'label' => _("txt-last-name"),
                ],
                'attributes' => [
                    'class'       => 'form-control',
                    'required'    => true,
                    'placeholder' => _("txt-give-your-last-name"),
                ]
            ]
        );
        /**
         * Produce a list of all phone numbers
         */
        $communityFieldSet = new Fieldset('community');
        foreach ($generalService->findAll('communityType') as $communityType) {
            $fieldSet = new Fieldset($communityType->getId());
            $fieldSet->add(
                [
                    'type'       => 'Zend\Form\Element\Text',
                    'name'       => 'community',
                    'options'    => [
                        'label' => sprintf(_(" %s Profile"), $communityType->getType())
                    ],
                    'attributes' => [
                        'class'       => 'form-control',
                        'placeholder' => _(sprintf(_("Give %s profile"), $communityType->getType()))
                    ]
                ]
            );
            $communityFieldSet->add($fieldSet);
        }
        $this->add($communityFieldSet);
        /**
         * Produce a list of all phone numbers
         */
        $phoneFieldSet = new Fieldset('phone');
        foreach ($contactService->findAll('phoneType') as $phoneType) {
            if (in_array($phoneType->getId(), [PhoneType::PHONE_TYPE_DIRECT, PhoneType::PHONE_TYPE_MOBILE])) {
                $fieldSet = new Fieldset($phoneType->getId());
                $fieldSet->add(
                    [
                        'type'       => 'Zend\Form\Element\Text',
                        'name'       => 'phone',
                        'options'    => [
                            'label' => sprintf(_("%s Phone number"), $phoneType->getType())
                        ],
                        'attributes' => [
                            'class'       => 'form-control',
                            'placeholder' => sprintf(_("Give %s phone number"), $phoneType->getType())
                        ]
                    ]
                );
                $phoneFieldSet->add($fieldSet);
            }
        }
        $this->add($phoneFieldSet);
        /**
         * Add the form field for the address
         */
        $addressFieldSet = new Fieldset('address');
        $addressFieldSet->add(
            [
                'type'       => 'Zend\Form\Element\Text',
                'name'       => 'address',
                'options'    => [
                    'label' => _("txt-address")
                ],
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _("txt-address")
                ]
            ]
        );
        $addressFieldSet->add(
            [
                'type'       => 'Zend\Form\Element\Text',
                'name'       => 'zipCode',
                'options'    => [
                    'label' => _("txt-zip-code")
                ],
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _("txt-zip-code")
                ]
            ]
        );
        $addressFieldSet->add(
            [
                'type'       => 'Zend\Form\Element\Text',
                'name'       => 'city',
                'options'    => [
                    'label' => _("txt-city")
                ],
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _("txt-city")
                ]
            ]
        );
        $addressFieldSet->add(
            [
                'type'       => 'DoctrineORMModule\Form\Element\EntitySelect',
                'name'       => 'country',
                'options'    => [
                    'label'          => _("txt-country"),
                    'object_manager' => $entityManager,
                    'target_class'   => 'General\Entity\Country',
                    'find_method'    => [
                        'name' => 'findAll',
                    ]
                ],
                'attributes' => [
                    'required' => true,
                ]
            ]
        );
        $this->add($addressFieldSet);

        $contactOrganisationFieldSet = new Fieldset('contact_organisation');
        $contactOrganisationFieldSet->add(
            [
                'type'       => 'Zend\Form\Element\Text',
                'name'       => 'organisation',
                'options'    => [
                    'label'      => _("txt-organisation"),
                    'help-block' => _("txt-organisation-form-element-description"),
                ],
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _("txt-give-your-organisation")
                ]
            ]
        );
        $contactOrganisationFieldSet->add(
            [
                'type'       => 'DoctrineORMModule\Form\Element\EntitySelect',
                'name'       => 'country',
                'options'    => [
                    'label'          => _("txt-country"),
                    'object_manager' => $entityManager,
                    'target_class'   => 'General\Entity\Country',
                    'find_method'    => [
                        'name' => 'findAll',
                    ]
                ],
                'attributes' => [
                    'required' => true,
                ]
            ]
        );
        $this->add($contactOrganisationFieldSet);
        $this->add(
            [
                'type'       => 'Zend\Form\Element\Text',
                'name'       => 'department',
                'options'    => [
                    'label' => _("txt-department"),
                ],
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _("txt-give-your-department"),
                ]
            ]
        );
        $this->add(
            [
                'type'       => 'Zend\Form\Element\Text',
                'name'       => 'position',
                'options'    => [
                    'label' => _("txt-position"),
                ],
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _("txt-give-your-position"),
                ]
            ]
        );
        /**
         * Produce a list of all phone numbers
         */
        $profileFieldSet = new Fieldset('profile');
        $profileEntity = new ProfileEntity();
        $profileFieldSet->add(
            [
                'type'       => 'Zend\Form\Element\Radio',
                'name'       => 'visible',
                'options'    => [
                    'label'         => _("txt-visibility"),
                    'value_options' => $profileEntity->getVisibleTemplates()
                ],
                'attributes' => [
                    'class' => 'form-control',
                ]
            ]
        );
        $profileFieldSet->add(
            [
                'type'       => 'Zend\Form\Element\Textarea',
                'name'       => 'description',
                'options'    => [
                    'label' => _("txt-expertise"),
                ],
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _("txt-give-your-expertise"),
                ]
            ]
        );
        $this->add($profileFieldSet);
        $this->add(
            [
                'type'       => '\Zend\Form\Element\File',
                'name'       => 'file',
                'attributes' => [
                    "class" => "form-control",
                ],
                'options'    => [
                    "label"      => "txt-photo-file",
                    "help-block" => _("txt-photo-requirements")
                ]
            ]
        );

        $this->add(
            [
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'submit',
                'attributes' => [
                    'class' => "btn btn-primary",
                    'value' => _("txt-update")
                ]
            ]
        );
        $this->add(
            [
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'cancel',
                'attributes' => [
                    'class' => "btn btn-warning",
                    'value' => _("txt-cancel")
                ]
            ]
        );
    }
}
