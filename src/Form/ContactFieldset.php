<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Contact\Form;

use Contact\Entity;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Element\EntityMultiCheckbox;
use DoctrineORMModule\Form\Element\EntitySelect;
use Organisation\Form\Element\Organisation;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\File;
use Zend\Form\Element\Radio;
use Zend\Form\Element\Text;
use Zend\Form\Fieldset;

/**
 * Class ContactFieldset
 *
 * @package Contact\Form
 */
class ContactFieldset extends Fieldset
{
    /**
     * @param EntityManager $entityManager
     * @param Entity\AbstractEntity $object
     */
    public function __construct(EntityManager $entityManager, Entity\AbstractEntity $object)
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
                $attributes = $element->getAttributes();
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
                'type'    => Organisation::class,
                'name'    => 'organisation',
                'options' => [
                    "label"      => _("txt-organisation"),
                    "help-block" => _("txt-organisation-help-block"),
                ],
            ]
        );

        $this->add(
            [
                'type'    => Text::class,
                'name'    => 'branch',
                'options' => [
                    "label"      => _("txt-branch"),
                    "help-block" => _("txt-branch-help-block"),
                ],
            ]
        );

        $this->add(
            [
                'type'    => File::class,
                'name'    => 'file',
                'options' => [
                    "label"      => _("txt-contact-photo-label"),
                    "help-block" => _("txt-contact-photo-help-block"),
                ],
            ]
        );
    }
}
