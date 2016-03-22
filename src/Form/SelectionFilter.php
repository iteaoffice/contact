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

use Contact\Service\SelectionService;
use Zend\Form\Fieldset;
use Zend\Form\Form;

/**
 * Jield copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2004-2015 Jield (http://jield.nl)
 */
class SelectionFilter extends Form
{
    /**
     * SelectionFilter constructor.
     * @param SelectionService $selectionService
     */
    public function __construct(SelectionService $selectionService)
    {
        parent::__construct();
        $this->setAttribute('method', 'get');
        $this->setAttribute('action', '');

        $filterFieldset = new Fieldset('filter');

        $filterFieldset->add(
            [
                'type'       => 'Zend\Form\Element\Text',
                'name'       => 'search',
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _('txt-search'),
                ],
            ]
        );

        $tags = [];
        foreach ($selectionService->findTags() as $tag) {
            if (!empty($tag['tag'])) {
                $tags[$tag['tag']] = $tag['tag'];
            }
        }

        $filterFieldset->add(
            [
                'type'       => 'Zend\Form\Element\MultiCheckbox',
                'name'       => 'tags',
                'options'    => [
                    'value_options' => $tags,
                    'inline'        => true
                ],
                'attributes' => [
                    'label' => _("txt-filter-on-tags"),
                ],
            ]
        );


        $this->add($filterFieldset);

        $this->add(
            [
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'submit',
                'attributes' => [
                    'id'    => 'submit',
                    'class' => 'btn btn-primary',
                    'value' => _('txt-filter'),
                ],
            ]
        );

        $this->add(
            [
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'clear',
                'attributes' => [
                    'id'    => 'cancel',
                    'class' => 'btn btn-warning',
                    'value' => _('txt-cancel'),
                ],
            ]
        );
    }
}
