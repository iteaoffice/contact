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

use Contact\Entity\PhoneType;
use Zend\Form\Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Form\Fieldset;

use DoctrineORMModule\Options\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

use Contact\Entity\Contact;
use Contact\Hydrator\Profile as ProfileHydrator;
use Contact\Entity\Profile as ProfileEntity;

/**
 *
 */
class Profile extends Form
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * Class constructor
     */
    public function __construct(ServiceLocatorInterface $serviceLocator, Contact $contact)
    {
        parent::__construct();
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $this->entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');

        $contactService = $serviceLocator->get('contact_contact_service');
        $generalService = $serviceLocator->get('general_general_service');

        $doctrineHydrator = new ProfileHydrator($this->entityManager);
        $this->setHydrator($doctrineHydrator)->setObject($contact);

        /**
         * Add a hidden form element for the id to allow a check on the uniqueness of some elements
         */
        $this->add(
            array(
                'type' => 'Zend\Form\Element\Hidden',
                'name' => 'id',
            )
        );

        $this->add(
            array(
                'type'       => 'DoctrineORMModule\Form\Element\EntitySelect',
                'name'       => 'gender',
                'options'    => array(
                    'label'          => _("txt-attention"),
                    'object_manager' => $this->entityManager,
                    'target_class'   => 'General\Entity\Gender',
                    'find_method'    => array(
                        'name' => 'findAll',
                    )
                ),
                'attributes' => array(
                    'required' => true,
                )
            )
        );

        $this->add(
            array(
                'type'       => 'DoctrineORMModule\Form\Element\EntitySelect',
                'name'       => 'title',
                'options'    => array(
                    'label'          => _("txt-title"),
                    'object_manager' => $this->entityManager,
                    'target_class'   => 'General\Entity\Title',
                    'find_method'    => array(
                        'name' => 'findAll',
                    )
                ),
                'attributes' => array(
                    'required' => true,
                )
            )
        );

        $this->add(
            array(
                'type'       => 'Zend\Form\Element\Text',
                'name'       => 'firstName',
                'options'    => array(
                    'label' => _("txt-first-name"),
                ),
                'attributes' => array(
                    'class'       => 'form-control',
                    'required'    => true,
                    'placeholder' => _("txt-give-your-first-name"),
                )
            )
        );

        $this->add(
            array(
                'type'       => 'Zend\Form\Element\Text',
                'name'       => 'middleName',
                'options'    => array(
                    'label' => _("txt-middle-name"),
                ),
                'attributes' => array(
                    'class'       => 'form-control',
                    'placeholder' => _("txt-give-your-middle-name"),
                )
            )
        );

        $this->add(
            array(
                'type'       => 'Zend\Form\Element\Text',
                'name'       => 'lastName',
                'options'    => array(
                    'label' => _("txt-last-name"),
                ),
                'attributes' => array(
                    'class'       => 'form-control',
                    'required'    => true,
                    'placeholder' => _("txt-give-your-last-name"),
                )
            )
        );

        /**
         * Produce a list of all phone numbers
         */
        $communityFieldSet = new Fieldset('community');

        foreach ($generalService->findAll('communityType') as $communityType) {
            $fieldSet = new Fieldset($communityType->getId());
            $fieldSet->add(
                array(
                    'type'       => 'Zend\Form\Element\Text',
                    'name'       => 'community',
                    'options'    => array(
                        'label' => sprintf(_(" %s Profile"), $communityType->getType())
                    ),
                    'attributes' => array(
                        'class'       => 'form-control',
                        'placeholder' => _(sprintf(_("Give %s profile"), $communityType->getType()))
                    )
                )
            );

            $communityFieldSet->add($fieldSet);
        }

        $this->add($communityFieldSet);

        /**
         * Produce a list of all phone numbers
         */
        $phoneFieldSet = new Fieldset('phone');

        foreach ($contactService->findAll('phoneType') as $phoneType) {
            if (in_array($phoneType->getId(), array(PhoneType::PHONE_TYPE_DIRECT, PhoneType::PHONE_TYPE_MOBILE))) {
                $fieldSet = new Fieldset($phoneType->getId());
                $fieldSet->add(
                    array(
                        'type'       => 'Zend\Form\Element\Text',
                        'name'       => 'phone',
                        'options'    => array(
                            'label' => sprintf(_("%s Phone number"), $phoneType->getType())
                        ),
                        'attributes' => array(
                            'class'       => 'form-control',
                            'placeholder' => sprintf(_("Give %s phone number"), $phoneType->getType())
                        )
                    )
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
            array(
                'type'       => 'Zend\Form\Element\Text',
                'name'       => 'address',
                'options'    => array(
                    'label' => _("txt-address")
                ),
                'attributes' => array(
                    'class'       => 'form-control',
                    'placeholder' => _("txt-address")
                )
            )
        );

        $addressFieldSet->add(
            array(
                'type'       => 'Zend\Form\Element\Text',
                'name'       => 'zipCode',
                'options'    => array(
                    'label' => _("txt-zip-code")
                ),
                'attributes' => array(
                    'class'       => 'form-control',
                    'placeholder' => _("txt-zip-code")
                )
            )
        );

        $addressFieldSet->add(
            array(
                'type'       => 'Zend\Form\Element\Text',
                'name'       => 'city',
                'options'    => array(
                    'label' => _("txt-city")
                ),
                'attributes' => array(
                    'class'       => 'form-control',
                    'placeholder' => _("txt-city")
                )
            )
        );

        $addressFieldSet->add(
            array(
                'type'       => 'DoctrineORMModule\Form\Element\EntitySelect',
                'name'       => 'country',
                'options'    => array(
                    'label'          => _("txt-country"),
                    'object_manager' => $this->entityManager,
                    'target_class'   => 'General\Entity\Country',
                    'find_method'    => array(
                        'name' => 'findAll',
                    )
                ),
                'attributes' => array(
                    'required' => true,
                )
            )
        );

        $this->add($addressFieldSet);

        /**
         * Produce a list of all phone numbers
         */
        $contactOrganisationFieldSet = new Fieldset('contact_organisation');

        $contactOrganisationFieldSet->add(
            array(
                'type'       => 'Zend\Form\Element\Text',
                'name'       => 'organisation',
                'options'    => array(
                    'label'      => _("txt-organisation"),
                    'help-block' => _("txt-organisation-form-element-description"),
                ),
                'attributes' => array(
                    'class'       => 'form-control',
                    'placeholder' => _("txt-give-your-organisation")
                )
            )
        );

        $contactOrganisationFieldSet->add(
            array(
                'type'       => 'DoctrineORMModule\Form\Element\EntitySelect',
                'name'       => 'country',
                'options'    => array(
                    'label'          => _("txt-country"),
                    'object_manager' => $this->entityManager,
                    'target_class'   => 'General\Entity\Country',
                    'find_method'    => array(
                        'name' => 'findAll',
                    )
                ),
                'attributes' => array(
                    'required' => true,
                )
            )
        );

        $this->add($contactOrganisationFieldSet);

        $this->add(
            array(
                'type'       => 'Zend\Form\Element\Text',
                'name'       => 'department',
                'options'    => array(
                    'label' => _("txt-department"),
                ),
                'attributes' => array(
                    'class'       => 'form-control',
                    'placeholder' => _("txt-give-your-department"),
                )
            )
        );

        $this->add(
            array(
                'type'       => 'Zend\Form\Element\Text',
                'name'       => 'position',
                'options'    => array(
                    'label' => _("txt-position"),
                ),
                'attributes' => array(
                    'class'       => 'form-control',
                    'placeholder' => _("txt-give-your-position"),
                )
            )
        );

        /**
         * Produce a list of all phone numbers
         */
        $profileFieldSet = new Fieldset('profile');
        $profileEntity   = new ProfileEntity();

        $profileFieldSet->add(
            array(
                'type'       => 'Zend\Form\Element\Radio',
                'name'       => 'visible',
                'options'    => array(
                    'label'         => _("txt-visibility"),
                    'value_options' => $profileEntity->getVisibleTemplates()
                ),
                'attributes' => array(
                    'class' => 'form-control',
                )
            )
        );

        $profileFieldSet->add(
            array(
                'type'       => 'Zend\Form\Element\Textarea',
                'name'       => 'description',
                'options'    => array(
                    'label' => _("txt-expertise"),
                ),
                'attributes' => array(
                    'class'       => 'form-control',
                    'placeholder' => _("txt-give-your-expertise"),
                )
            )
        );

        $this->add($profileFieldSet);

        $this->add(
            array(
                'type'       => '\Zend\Form\Element\File',
                'name'       => 'file',
                'attributes' => array(
                    "class" => "form-control",
                ),
                'options'    => array(
                    "label"      => "txt-photo-file",
                    "help-block" => _("txt-photo-requirements")
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Csrf',
                'name' => 'csrf',
            )
        );

        $this->add(
            array(
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'submit',
                'attributes' => array(
                    'class' => "btn btn-primary",
                    'value' => _("txt-update")
                )
            )
        );

        $this->add(
            array(
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'cancel',
                'attributes' => array(
                    'class' => "btn btn-warning",
                    'value' => _("txt-cancel")
                )
            )
        );
    }
}
