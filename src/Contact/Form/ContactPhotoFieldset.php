<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Content
 * @package     Form
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Contact\Form;

use Zend\Form\Fieldset;
use Zend\Form\Annotation\AnnotationBuilder;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Element\EntitySelect;
use DoctrineORMModule\Form\Element\EntityMultiCheckbox;

use Contact\Entity;

class ContactPhotoFieldset extends Fieldset
{
    /**
     * @param EntityManager         $entityManager
     * @param Entity\EntityAbstract $object
     */
    public function __construct(EntityManager $entityManager, Entity\EntityAbstract $object)
    {
        parent::__construct($object->get('underscore_entity_name'));

        $photo            = new Entity\Photo();
        $doctrineHydrator = new DoctrineHydrator($entityManager, 'Contact\Entity\Photo');
        $this->setHydrator($doctrineHydrator)->setObject($photo);

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

        $this->add(
            array(
                'type'    => '\Zend\Form\Element\File',
                'name'    => 'file',
                'options' => array(
                    "label"      => "txt-photo-file",
                    "help-block" => _("txt-photo-requirements")
                )
            )
        );
    }
}
