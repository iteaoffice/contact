<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Content
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Contact\Form;

use Contact\Entity;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Element\EntityMultiCheckbox;
use DoctrineORMModule\Form\Element\EntitySelect;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Fieldset;

class ContactPhotoFieldset extends Fieldset
{
    /**
     * @param EntityManager         $entityManager
     * @param Entity\EntityAbstract $object
     */
    public function __construct(EntityManager $entityManager, Entity\EntityAbstract $object)
    {
        $photo = new Entity\Photo();

        parent::__construct($photo->get('underscore_entity_name'));

        $doctrineHydrator = new DoctrineHydrator($entityManager, Entity\Photo::class);

        $this->setHydrator($doctrineHydrator)->setObject($photo);
        $builder = new AnnotationBuilder();
        /*
         * Go over the different form elements and add them to the form
         */
        foreach ($builder->createForm($object)->getElements() as $element) {
            /*
             * Go over each element to add the objectManager to the EntitySelect
             */
            if ($element instanceof EntitySelect || $element instanceof EntityMultiCheckbox) {
                $element->setOptions(
                    [
                        'object_manager' => $entityManager,
                    ]
                );
            }
            //Add only when a type is provided
            if (array_key_exists('type', $element->getAttributes())) {
                $this->add($element);
            }
        }
        $this->add(
            [
                'type'    => '\Zend\Form\Element\File',
                'name'    => 'file',
                'options' => [
                    "label"      => "txt-photo-file",
                    "help-block" => _("txt-photo-requirements"),
                ],
            ]
        );
    }
}
