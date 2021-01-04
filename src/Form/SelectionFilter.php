<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\Form;

use Contact\Service\SelectionService;
use Laminas\Form\Element\MultiCheckbox;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;

/**
 * Class SelectionFilter
 *
 * @package Contact\Form
 */
final class SelectionFilter extends Form
{
    public function __construct(SelectionService $selectionService)
    {
        parent::__construct();
        $this->setAttribute('method', 'get');
        $this->setAttribute('action', '');

        $filterFieldset = new Fieldset('filter');

        $filterFieldset->add(
            [
                'type'       => Text::class,
                'name'       => 'search',
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _('txt-search'),
                ],
            ]
        );

        $filterFieldset->add(
            [
                'type'       => MultiCheckbox::class,
                'name'       => 'includeDeleted',
                'options'    => [
                    'value_options' => [1 => _('txt-include-deleted')],
                    'inline'        => true,
                ],
                'attributes' => [
                    'label' => _('txt-include-deleted'),
                ],
            ]
        );

        $tags = [];
        foreach ($selectionService->findTags() as $tag) {
            if (! empty($tag['tag'])) {
                $tags[$tag['tag']] = $tag['tag'];
            }
        }

        $filterFieldset->add(
            [
                'type'       => MultiCheckbox::class,
                'name'       => 'tags',
                'options'    => [
                    'value_options' => $tags,
                    'inline'        => true,
                ],
                'attributes' => [
                    'label' => _('txt-filter-on-tags'),
                ],
            ]
        );

        $types = [];
        foreach ($selectionService->findTypes() as $type) {
            $types[$type->getId()] = $type->getName();
        }

        $filterFieldset->add(
            [
                'type'       => MultiCheckbox::class,
                'name'       => 'type',
                'options'    => [
                    'value_options' => $types,
                    'inline'        => true,
                ],
                'attributes' => [
                    'label' => _('txt-filter-on-type'),
                ],
            ]
        );


        $this->add($filterFieldset);

        $this->add(
            [
                'type'       => Submit::class,
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
                'type'       => Submit::class,
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
