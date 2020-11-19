<?php

/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        https://itea3.org
 */

declare(strict_types=1);

namespace Contact\Form;

use Contact\Entity\OptIn;
use Contact\Entity\Selection;
use Contact\Service\ContactService;
use Contact\Service\SelectionService;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\File\MimeType;
use Laminas\Validator\File\Size;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\File;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\MultiCheckbox;
use Laminas\Form\Element\Select;

/**
 * Class Import
 *
 * @package Contact\Form
 */
final class Import extends Form implements InputFilterProviderInterface
{
    public function __construct(ContactService $contactService, SelectionService $selectionService)
    {
        parent::__construct();
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '');
        $this->setAttribute('class', 'form-horizontal');

        $selections = [];
        /** @var Selection $selection */
        foreach ($selectionService->findAll(Selection::class) as $selection) {
            /** @var $selection Selection */
            if ($selection->isActive() && null === $selection->getSql()) {
                $selections[$selection->getId()] = $selection->getSelection();
            }
        }

        asort($selections);
        $this->add(
            [
                'type'    => Select::class,
                'name'    => 'selection_id',
                'options' => [
                    'value_options' => $selections,
                    'empty_option'  => '-- Append to existing selection',
                    'label'         => _('txt-append-to-selection'),
                    'help-block'    => _('txt-contact-import-append-to-selection-name-help-block'),
                ],
            ]
        );

        $optins = [];
        /** @var OptIn $optin */
        foreach ($contactService->findAll(OptIn::class) as $optin) {
            $optins[$optin->getId()] = $optin->getOptIn();
        }

        asort($optins);

        $this->add(
            [
                'type'    => MultiCheckbox::class,
                'name'    => 'optIn',
                'options' => [
                    'value_options' => $optins,
                    'label'         => 'txt-select-opt-in',
                    'help-block'    => _('txt-contact-import-select-opt-in-help-block'),
                ],
            ]
        );

        $this->add(
            [
                'type'    => Text::class,
                'name'    => 'selection',
                'options' => [
                    'label'      => 'txt-selection',
                    'help-block' => _('txt-contact-import-selection-name-help-block'),
                ],
            ]
        );
        $this->add(
            [
                'type'    => File::class,
                'name'    => 'file',
                'options' => [
                    'label'      => 'txt-file',
                    'help-block' => _('txt-contact-import-file-requirements'),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Submit::class,
                'name'       => 'upload',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => _('txt-verify-data'),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Submit::class,
                'name'       => 'import',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => _('txt-import'),
                ],
            ]
        );
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'file'         => [
                'required'   => true,
                'validators' => [
                    new Size(
                        [
                            'min' => '1kB',
                            'max' => '16MB',
                        ]
                    ),
                    new MimeType(
                        [
                            'text/plain',
                        ]
                    ),
                ],
            ],
            'selection_id' => [
                'required' => false,
            ],
            'optIn'        => [
                'required' => false,
            ],
        ];
    }
}
