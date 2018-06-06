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
use Contact\Service\SelectionService;
use Zend\Form\Fieldset;
use Zend\Form\Form;

/**
 * Class SelectionFilter
 *
 * @package Contact\Form
 */
class SelectionFilter extends Form
{
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

        $filterFieldset->add(
            [
                'type'       => 'Zend\Form\Element\MultiCheckbox',
                'name'       => 'includeDeleted',
                'options'    => [
                    'value_options' => [1 => _("txt-include-deleted")],
                    'inline'        => true,
                ],
                'attributes' => [
                    'label' => _("txt-include-deleted"),
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
                    'inline'        => true,
                ],
                'attributes' => [
                    'label' => _("txt-filter-on-tags"),
                ],
            ]
        );

        $filterFieldset->add(
            [
                'type'       => 'Zend\Form\Element\MultiCheckbox',
                'name'       => 'core',
                'options'    => [
                    'value_options' => Selection::getCoreTemplates(),
                    'inline'        => true,
                ],
                'attributes' => [
                    'label' => _("txt-filter-on-core-selections"),
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
