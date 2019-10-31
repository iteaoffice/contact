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
use Zend\Form\Element\MultiCheckbox;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Fieldset;
use Zend\Form\Form;

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
            if (!empty($tag['tag'])) {
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

        $filterFieldset->add(
            [
                'type'       => MultiCheckbox::class,
                'name'       => 'core',
                'options'    => [
                    'value_options' => Selection::getCoreTemplates(),
                    'inline'        => true,
                ],
                'attributes' => [
                    'label' => _('txt-filter-on-core-selections'),
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
