<?php

/**
 * Jield copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Form;

use Contact\Entity\Selection;
use Contact\Service\SelectionService;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;

/**
 * Class SelectionContacts
 *
 * @package Contact\Form
 */
final class AddContactToSelection extends Form
{
    public function __construct(SelectionService $selectionService)
    {
        parent::__construct();
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '');
        $this->setAttribute('class', 'form-horizontal');

        $selections = [];
        /** @var Selection $selection */
        foreach ($selectionService->findNonSqlSelections() as $selection) {
            $selections[$selection->getId()] = (string)$selection;
        }


        $this->add(
            [
                'type'       => Select::class,
                'name'       => 'selection',
                'options'    => [
                    'value_options' => $selections,
                ],
                'attributes' => [
                    'label' => _('txt-choose-selection'),
                    'class' => 'form-control',
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
