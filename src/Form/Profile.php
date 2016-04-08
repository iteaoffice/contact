<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Contact\Form;

use Contact\Entity\Contact;
use Contact\Entity\PhoneType;
use Contact\Entity\Photo;
use Contact\Entity\Profile as ProfileEntity;
use Contact\Hydrator\Profile as ProfileHydrator;
use Contact\Service\ContactService;
use Doctrine\ORM\EntityManager;
use DoctrineORMModule\Form\Element\EntityRadio;
use DoctrineORMModule\Form\Element\EntitySelect;
use General\Entity\Country;
use Organisation\Entity\Organisation;
use Zend\Form\Element\Text;
use Zend\Form\Fieldset;
use Zend\Form\Form;

/**
 *
 */
class Profile extends Form
{
    /**
     * Profile constructor.
     *
     * @param EntityManager  $entityManager
     * @param ContactService $contactService
     * @param Contact        $contact
     */
    public function __construct(EntityManager $entityManager, ContactService $contactService, Contact $contact)
    {
        parent::__construct();
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
        $this->setAttribute('action', '');

        /** @var ContactService $contactService */
        $doctrineHydrator = new ProfileHydrator($entityManager);
        $this->setHydrator($doctrineHydrator)->setObject($contact);
        /*
         * Add a hidden form element for the id to allow a check on the uniqueness of some elements
         */
        $this->add([
                'type' => 'Zend\Form\Element\Hidden',
                'name' => 'id',
            ]);
        $this->add([
                'type'       => EntitySelect::class,
                'name'       => 'gender',
                'options'    => [
                    'label'          => _("txt-attention"),
                    'object_manager' => $entityManager,
                    'target_class'   => 'General\Entity\Gender',
                    'find_method'    => [
                        'name' => 'findAll',
                    ],
                ],
                'attributes' => [
                    'required' => true,
                ],
            ]);
        $this->add([
                'type'       => EntitySelect::class,
                'name'       => 'title',
                'options'    => [
                    'label'          => _("txt-title"),
                    'object_manager' => $entityManager,
                    'target_class'   => 'General\Entity\Title',
                    'find_method'    => [
                        'name' => 'findAll',
                    ],
                ],
                'attributes' => [
                    'required' => true,
                ],
            ]);
        $this->add([
                'type'       => Text::class,
                'name'       => 'firstName',
                'options'    => [
                    'label' => _("txt-first-name"),
                ],
                'attributes' => [
                    'class'       => 'form-control',
                    'required'    => true,
                    'placeholder' => _("txt-give-your-first-name"),
                ],
            ]);
        $this->add([
                'type'       => Text::class,
                'name'       => 'middleName',
                'options'    => [
                    'label' => _("txt-middle-name"),
                ],
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _("txt-give-your-middle-name"),
                ],
            ]);
        $this->add([
                'type'       => Text::class,
                'name'       => 'lastName',
                'options'    => [
                    'label' => _("txt-last-name"),
                ],
                'attributes' => [
                    'class'       => 'form-control',
                    'required'    => true,
                    'placeholder' => _("txt-give-your-last-name"),
                ],
            ]);
        /**
         * Produce a list of all phone numbers
         */
        $phoneFieldSet = new Fieldset('phone');
        /** @var PhoneType $phoneType */
        foreach ($contactService->findAll(PhoneType::class) as $phoneType) {
            if (in_array($phoneType->getId(), [PhoneType::PHONE_TYPE_DIRECT, PhoneType::PHONE_TYPE_MOBILE])) {
                $fieldSet = new Fieldset($phoneType->getId());
                $fieldSet->add([
                        'type'       => Text::class,
                        'name'       => 'phone',
                        'options'    => [
                            'label' => sprintf(_("%s Phone number"), $phoneType->getType()),
                        ],
                        'attributes' => [
                            'class'       => 'form-control',
                            'placeholder' => sprintf(_("Give %s phone number"), $phoneType->getType()),
                        ],
                    ]);
                $phoneFieldSet->add($fieldSet);
            }
        }
        $this->add($phoneFieldSet);
        /*
         * Add the form field for the address
         */
        $addressFieldSet = new Fieldset('address');
        $addressFieldSet->add([
                'type'       => Text::class,
                'name'       => 'address',
                'options'    => [
                    'label' => _("txt-address"),
                ],
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _("txt-address"),
                ],
            ]);
        $addressFieldSet->add([
                'type'       => Text::class,
                'name'       => 'zipCode',
                'options'    => [
                    'label' => _("txt-zip-code"),
                ],
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _("txt-zip-code"),
                ],
            ]);
        $addressFieldSet->add([
                'type'       => Text::class,
                'name'       => 'city',
                'options'    => [
                    'label' => _("txt-city"),
                ],
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _("txt-city"),
                ],
            ]);
        $addressFieldSet->add([
                'type'       => EntitySelect::class,
                'name'       => 'country',
                'options'    => [
                    'label'          => _("txt-country"),
                    'object_manager' => $entityManager,
                    'target_class'   => Country::class,
                    'find_method'    => [
                        'name'   => 'findBy',
                        'params' => [
                            'criteria' => [],
                            'orderBy'  => ['country' => 'ASC'],
                        ],
                    ],
                ],
                'attributes' => [
                    'required' => true,
                ],
            ]);
        $this->add($addressFieldSet);

        $contactOrganisationFieldSet = new Fieldset('contact_organisation');

        $contactOrganisationFieldSet->add([
                'type'       => EntityRadio::class,
                'name'       => 'organisation_id',
                'options'    => [
                    'label'                     => _("txt-organisation"),
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
                    'label_generator'           => function (Organisation $organisation) {
                        if (!is_null($organisation->getCountry())) {
                            return sprintf(
                                "%s (%s)",
                                $organisation->getOrganisation(),
                                $organisation->getCountry()->getCountry()
                            );
                        } else {
                            return sprintf("%s", $organisation->getOrganisation());
                        }

                    },
                ],
                'attributes' => [
                    'required' => !is_null($contact->getContactOrganisation()),
                    //Only required when a contact has an organisation
                    'id'       => 'organisation',
                ],
            ]);

        $contactOrganisationFieldSet->add([
                'type'       => Text::class,
                'name'       => 'organisation',
                'options'    => [
                    'label'      => _("txt-organisation"),
                    'help-block' => _("txt-organisation-form-element-description"),
                ],
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _("txt-give-your-organisation"),
                ],
            ]);
        $contactOrganisationFieldSet->add([
                'type'       => EntitySelect::class,
                'name'       => 'country',
                'options'    => [
                    'label'          => _("txt-country"),
                    'object_manager' => $entityManager,
                    'target_class'   => Country::class,
                    'find_method'    => [
                        'name'   => 'findBy',
                        'params' => [
                            'criteria' => [],
                            'orderBy'  => ['country' => 'ASC'],
                        ],
                    ],
                ],
                'attributes' => [
                    'required' => true,
                ],
            ]);
        $this->add($contactOrganisationFieldSet);
        $this->add([
                'type'       => Text::class,
                'name'       => 'department',
                'options'    => [
                    'label' => _("txt-department"),
                ],
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _("txt-give-your-department"),
                ],
            ]);
        $this->add([
                'type'       => Text::class,
                'name'       => 'position',
                'options'    => [
                    'label' => _("txt-position"),
                ],
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _("txt-give-your-position"),
                ],
            ]);
        /*
         * Produce a list of all phone numbers
         */
        $profileFieldSet = new Fieldset('profile');
        $profileEntity = new ProfileEntity();
        $profileFieldSet->add([
                'type'    => 'Zend\Form\Element\Radio',
                'name'    => 'visible',
                'options' => [
                    'label'         => _("txt-visibility"),
                    'value_options' => $profileEntity->getVisibleTemplates(),
                ],
            ]);
        $profileFieldSet->add([
                'type'       => 'Zend\Form\Element\Textarea',
                'name'       => 'description',
                'options'    => [
                    'label' => _("txt-expertise"),
                ],
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _("txt-give-your-expertise"),
                ],
            ]);
        $this->add($profileFieldSet);
        $this->add([
                'type'       => '\Zend\Form\Element\File',
                'name'       => 'file',
                'attributes' => [
                    "class" => "form-control",
                ],
                'options'    => [
                    "label"      => "txt-photo-file",
                    "help-block" => _("txt-photo-requirements"),
                ],
            ]);

        $this->add([
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'submit',
                'attributes' => [
                    'class' => "btn btn-primary",
                    'value' => _("txt-update"),
                ],
            ]);
        $this->add([
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'cancel',
                'attributes' => [
                    'class' => "btn btn-warning",
                    'value' => _("txt-cancel"),
                ],
            ]);
    }
}
