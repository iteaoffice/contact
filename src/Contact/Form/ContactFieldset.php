<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Content
 * @package     Form
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
namespace Contact\Form;

use Zend\Form\Fieldset;
use Zend\Form\Annotation\AnnotationBuilder;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Element\EntitySelect;
use DoctrineORMModule\Form\Element\EntityMultiCheckbox;

use Contact\Entity;

class ContactFieldset extends Fieldset
{
    /**
     * @param EntityManager         $entityManager
     * @param Entity\EntityAbstract $object
     */
    public function __construct(EntityManager $entityManager, Entity\EntityAbstract $object)
    {
        parent::__construct($object->get('underscore_entity_name'));

        $contact          = new Entity\Contact();
        $doctrineHydrator = new DoctrineHydrator($entityManager, 'Contact\Entity\Contact');
        $this->setHydrator($doctrineHydrator)->setObject($contact);

        $builder = new AnnotationBuilder();

        /**
         * Go over the different form elements and add them to the form
         */
        foreach ($builder->createForm($object)->getElements() AS $element) {
            /**
             * Go over each element to add the objectManager to the EntitySelect
             */
            if ($element instanceof EntitySelect or $element instanceof EntityMultiCheckbox) {
                $element->setOptions(
                    array(
                        'object_manager' => $entityManager
                    )
                );
            }

            //Add only when a type is provided
            if (array_key_exists('type', $element->getAttributes())) {
                $this->add($element);
            }
        }

        $contactPhone = new ContactPhoneFieldset($entityManager, new Entity\Phone());
        $contactPhone->setObject(new Entity\Phone());

        $this->add(
            array(
                'type'    => 'Zend\Form\Element\Collection',
                'name'    => 'phone',
                'options' => array(
                    'label'                  => _("txt-phone-information"),
                    'count'                  => 1,
                    'should_create_template' => true,
                    'template_placeholder'   => '__placeholder__',
                    'allow_add'              => true,
                    'target_element'         => $contactPhone

                )
            )
        );

        $contactProfileFieldset = new \Contact\Form\ContactProfileFieldset($entityManager, new Entity\Profile());
        $this->add($contactProfileFieldset);

        $contactPhoto = new \Contact\Form\ContactPhotoFieldset($entityManager, new Entity\Photo());
        $contactPhoto->setObject(new Entity\Photo());

        $this->add(
            array(
                'type'    => 'Zend\Form\Element\Collection',
                'name'    => 'photo',
                'options' => array(
                    'label'                  => _("txt-profile-photo"),
                    'count'                  => 1,
                    'should_create_template' => true,
                    'template_placeholder'   => '__placeholder__',
                    'allow_add'              => false,
                    'target_element'         => $contactPhoto

                )
            )
        );

        $contactAddress = new ContactAddressFieldset($entityManager, new Entity\Address());
        $contactAddress->setObject(new Entity\Address());

        $this->add(
            array(
                'type'    => 'Zend\Form\Element\Collection',
                'name'    => 'address',
                'options' => array(
                    'label'                  => _("txt-address-information"),
                    'count'                  => 1,
                    'should_create_template' => true,
                    'template_placeholder'   => '__placeholder__',
                    'allow_add'              => true,
                    'target_element'         => $contactAddress

                )
            )
        );

        $contactCommunity = new ContactCommunityFieldset($entityManager, new Entity\Community());
        $contactCommunity->setObject(new Entity\Community());

        $this->add(
            array(
                'type'    => 'Zend\Form\Element\Collection',
                'name'    => 'community',
                'options' => array(
                    'label'                  => _("txt-community-information"),
                    'count'                  => 1,
                    'should_create_template' => true,
                    'template_placeholder'   => '__placeholder__',
                    'allow_add'              => true,
                    'target_element'         => $contactCommunity

                )
            )
        );

        $contactOrganisationFieldset = new \Contact\Form\ContactOrganisationFieldset($entityManager, new Entity\ContactOrganisation());
        $this->add($contactOrganisationFieldset);
    }
}
