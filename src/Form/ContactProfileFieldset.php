<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Content
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Form;

use Contact\Entity;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Element\EntityMultiCheckbox;
use DoctrineORMModule\Form\Element\EntitySelect;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Radio;
use Zend\Form\Fieldset;

class ContactProfileFieldset extends Fieldset
{
    public function __construct(EntityManager $entityManager, Entity\AbstractEntity $object)
    {
        parent::__construct($object->get('underscore_entity_name'));
        $profile = new Entity\Profile();
        $doctrineHydrator = new DoctrineHydrator($entityManager, Entity\Profile::class);
        $this->setHydrator($doctrineHydrator)->setObject($profile);
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
            if ($element instanceof Radio) {
                $attributes = $element->getAttributes();
                $valueOptionsArray = 'get' . ucfirst($attributes['array']);
                $element->setOptions(
                    [
                        'value_options' => $object->$valueOptionsArray(),
                    ]
                );
            }
            //Add only when a type is provided
            if (array_key_exists('type', $element->getAttributes())) {
                $this->add($element);
            }
        }
    }
}
