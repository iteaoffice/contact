<?php

/**
 * Jield copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Form;

use Contact\Entity\Selection;
use Doctrine\ORM\EntityManager;
use DoctrineORMModule\Form\Element\EntitySelect;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Radio;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Textarea;
use Zend\Form\Form;

/**
 * Class SelectionContacts
 *
 * @package Contact\Form
 */
class SelectionContacts extends Form
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct();
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '');
        $this->setAttribute('class', 'form-horizontal');
        $this->setAttribute('onsubmit', 'return storeChanges();');

        $this->add(
            [
                'type'       => EntitySelect::class,
                'name'       => 'selection',
                'options'    => [
                    'target_class'   => Selection::class,
                    'object_manager' => $entityManager,
                    'help-block'     => _('txt-form-calendar-contacts-selection-help-block'),
                    'find_method'    => [
                        'name'   => 'findActive',
                        'params' => [
                            'criteria' => [],
                            'orderBy'  => [],
                        ],
                    ],
                ],
                'attributes' => [
                    'id'    => 'selection',
                    'class' => 'form-control'
                ]
            ]
        );

        $this->add(
            [
                'type'       => Hidden::class,
                'name'       => 'added',
                'attributes' => [
                    'id' => 'added',
                ],
            ]
        );

        $this->add(
            [
                'type'       => Hidden::class,
                'name'       => 'removed',
                'attributes' => [
                    'id' => 'removed',
                ],
            ]
        );


        $this->add(
            [
                'type'       => Radio::class,
                'name'       => 'type',
                'options'    => [
                    'value_options' => [
                        Selection::TYPE_SQL   => 'SQL',
                        Selection::TYPE_FIXED => 'Fixed selection',
                    ],
                ],
                'attributes' => [
                    'label' => _('txt-selection-type'),
                ],
            ]
        );

        $this->add(
            [
                'type'       => Textarea::class,
                'name'       => 'sql',
                'options'    => [

                ],
                'attributes' => [
                    'label' => _('txt-sql-query'),
                    'rows'  => 20,
                ],
            ]
        );


        $this->add(
            [
                'type'       => Submit::class,
                'name'       => 'submit',
                'attributes' => [
                    'id'    => 'submit',
                    'class' => 'btn btn-primary',
                    'value' => _('txt-submit'),
                ],
            ]
        );

        $this->add(
            [
                'type'       => Submit::class,
                'name'       => 'cancel',
                'attributes' => [
                    'id'    => 'cancel',
                    'class' => 'btn btn-warning',
                    'value' => _('txt-cancel'),
                ],
            ]
        );
    }
}
