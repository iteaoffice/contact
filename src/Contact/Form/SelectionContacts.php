<?php

/**
 * Jield copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2004-2015 Jield (http://jield.nl)
 */

namespace Contact\Form;

use Contact\Entity\Selection;
use Contact\Service\SelectionService;
use Zend\Form\Form;

/**
 * Jield copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2004-2015 Jield (http://jield.nl)
 */
class SelectionContacts extends Form
{
    /**
     * SelectionFilter constructor.
     * @param SelectionService $selectionService
     */
    public function __construct(SelectionService $selectionService)
    {
        parent::__construct();
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '');
        $this->setAttribute("onsubmit", "return storeChanges();");

        $this->add(
            [
                'type'       => 'Zend\Form\Element\Hidden',
                'name'       => 'added',
                'attributes' => [
                    'id' => 'added',
                ],
            ]
        );

        $this->add(
            [
                'type'       => 'Zend\Form\Element\Hidden',
                'name'       => 'removed',
                'attributes' => [
                    'id' => 'removed',
                ],
            ]
        );


        $this->add(
            [
                'type'       => 'Zend\Form\Element\Radio',
                'name'       => 'type',
                'options'    => [
                    'value_options' => [
                        Selection::TYPE_SQL => 'SQL',
                        Selection::TYPE_FIXED => 'Fixed selection'
                    ],
                ],
                'attributes' => [
                    'label' => _("txt-selection-type"),
                ],
            ]
        );

        $this->add(
            [
                'type'       => 'Zend\Form\Element\Textarea',
                'name'       => 'sql',
                'options'    => [

                ],
                'attributes' => [
                    'label' => _("txt-sql-query"),
                    'rows'  => 20
                ],
            ]
        );


        $this->add(
            [
                'type'       => 'Zend\Form\Element\Submit',
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
                'type'       => 'Zend\Form\Element\Submit',
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
