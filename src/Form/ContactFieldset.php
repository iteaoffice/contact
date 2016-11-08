<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * PHP Version 5
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2016 ITEA Office
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

namespace Contact\Form;

use Contact\Entity;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Element\EntityMultiCheckbox;
use DoctrineORMModule\Form\Element\EntitySelect;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Radio;
use Zend\Form\Fieldset;

/**
 * Class ContactFieldset
 *
 * @package Contact\Form
 */
class ContactFieldset extends Fieldset
{
    /**
     * @param EntityManager         $entityManager
     * @param Entity\EntityAbstract $object
     */
    public function __construct(EntityManager $entityManager, Entity\EntityAbstract $object)
    {
        parent::__construct($object->get('underscore_entity_name'));
        $doctrineHydrator = new DoctrineHydrator($entityManager);
        $this->setHydrator($doctrineHydrator)->setObject($object);
        $builder = new AnnotationBuilder();
        /**
         * Go over the different form elements and add them to the form
         */
        foreach ($builder->createForm($object)->getElements() as $element) {
            /**
             * Go over each element to add the objectManager to the EntitySelect
             */
            if ($element instanceof EntitySelect || $element instanceof EntityMultiCheckbox) {
                $element->setOptions(
                    array_merge_recursive(
                        $element->getOptions(),
                        [
                        'object_manager' => $entityManager,
                        ]
                    )
                );
            }
            if ($element instanceof Radio) {
                $attributes        = $element->getAttributes();
                $valueOptionsArray = 'get' . ucfirst($attributes['array']);
                $element->setOptions(
                    array_merge_recursive(
                        $element->getOptions(),
                        [
                        'value_options' => $object->$valueOptionsArray(),
                        ]
                    )
                );
            }
            //Add only when a type is provided
            if (array_key_exists('type', $element->getAttributes())) {
                $this->add($element);
            }
        }

        $this->add(
            [
                'type'    => '\Organisation\Form\Element\Organisation',
                'name'    => 'organisation',
                'options' => [
                    "label"      => _("txt-organisation"),
                    "help-block" => _("txt-organisation-help-block"),
                ],
            ]
        );

        $this->add(
            [
                'type'    => '\Zend\Form\Element\Text',
                'name'    => 'branch',
                'options' => [
                    "label"      => _("txt-branch"),
                    "help-block" => _("txt-branch-help-block"),
                ],
            ]
        );
    }
}