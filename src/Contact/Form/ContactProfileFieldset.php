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
use Zend\Form\Element\Radio;
use DoctrineORMModule\Form\Element\EntityMultiCheckbox;

use Contact\Entity;

class ContactProfileFieldset extends Fieldset
{
    /**
     * @param EntityManager         $entityManager
     * @param Entity\EntityAbstract $object
     */
    public function __construct(EntityManager $entityManager, Entity\EntityAbstract $object)
    {
        parent::__construct('profile');

        $profile          = new Entity\Profile();
        $doctrineHydrator = new DoctrineHydrator($entityManager, 'Contact\Entity\Profile');
        $this->setHydrator($doctrineHydrator)->setObject($profile);

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

            if ($element instanceof Radio) {
                $attributes        = $element->getAttributes();
                $valueOptionsArray = 'get' . ucfirst($attributes['array']);

                $element->setOptions(
                    array(
                        'value_options' => $object->$valueOptionsArray()
                    )
                );
            }

            //Add only when a type is provided
            if (array_key_exists('type', $element->getAttributes())) {
                $this->add($element);
            }
        }
    }
}