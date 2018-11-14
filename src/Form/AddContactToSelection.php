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

use Contact\Service\SelectionService;
use Zend\Form\Form;

/**
 * Class SelectionContacts
 * @package Contact\Form
 */
class AddContactToSelection extends Form
{
    /**
     * SelectionFilter constructor.
     *
     * @param SelectionService $selectionService
     */
    public function __construct(SelectionService $selectionService)
    {
        parent::__construct();
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '');
        $this->setAttribute('class', 'form-horizontal');

        $selections = [];
        foreach ($selectionService->findNonSqlSelections() as $selection) {
            $selections[$selection->getId()] = (string)$selection;
        }


        $this->add(
            [
                'type'       => 'Zend\Form\Element\Select',
                'name'       => 'selection',
                'options'    => [
                    'value_options' => $selections,
                ],
                'attributes' => [
                    'label' => _("txt-choose-selection"),
                    'class' => 'form-control',
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
