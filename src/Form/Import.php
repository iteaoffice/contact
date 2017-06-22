<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
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
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\File\MimeType;
use Zend\Validator\File\Size;

/**
 * Create a link to an contact.
 *
 * @category   Contact
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    https://itea3.org/licence.txt proprietary
 *
 * @link       https://itea3.org
 */
class Import extends Form implements InputFilterProviderInterface
{
    /**
     * Import constructor.
     *
     * @param ContactService $contactService
     * @param SelectionService $selectionService
     */
    public function __construct(ContactService $contactService, SelectionService $selectionService)
    {
        parent::__construct();
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '');
        $this->setAttribute('class', 'form-horizontal');

        $selections = [];
        foreach ($selectionService->findAll(Selection::class) as $selection) {
            /** @var $selection Selection */
            if (is_null($selection->getSql())) {
                $selections[$selection->getId()] = $selection->getSelection();
            }
        }

        asort($selections);
        $this->add(
            [
                'type'    => '\Zend\Form\Element\Select',
                'name'    => 'selection_id',
                'options' => [
                    "value_options" => $selections,
                    'empty_option'  => '-- Append to existing selection',
                    "label"         => "txt-append-to-selection",
                    "help-block"    => _("txt-contact-import-append-to-selection-name-help-block"),
                ],
            ]
        );

        $optins = [];
        foreach ($contactService->findAll(OptIn::class) as $optin) {
            /** @var $optin OptIn */
            $optins[$optin->getId()] = $optin->getOptIn();
        }

        asort($optins);

        $this->add(
            [
                'type'    => '\Zend\Form\Element\MultiCheckbox',
                'name'    => 'optIn',
                'options' => [
                    "value_options" => $optins,
                    "label"         => "txt-select-opt-in",
                    "help-block"    => _("txt-contact-import-select-opt-in-help-block"),
                ],
            ]
        );

        $this->add(
            [
                'type'    => '\Zend\Form\Element\Text',
                'name'    => 'selection',
                'options' => [
                    "label"      => "txt-selection",
                    "help-block" => _("txt-contact-import-selection-name-help-block"),
                ],
            ]
        );
        $this->add(
            [
                'type'    => '\Zend\Form\Element\File',
                'name'    => 'file',
                'options' => [
                    "label"      => "txt-file",
                    "help-block" => _("txt-contact-import-file-requirements"),
                ],
            ]
        );
        $this->add(
            [
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'upload',
                'attributes' => [
                    'class' => "btn btn-primary",
                    'value' => _("txt-verify-data"),
                ],
            ]
        );
        $this->add(
            [
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'import',
                'attributes' => [
                    'class' => "btn btn-primary",
                    'value' => _("txt-import"),
                ],
            ]
        );
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return [
            'file'         => [
                'required'   => true,
                'validators' => [
                    new Size(
                        [
                            'min' => '0kB',
                            'max' => '8MB',
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
