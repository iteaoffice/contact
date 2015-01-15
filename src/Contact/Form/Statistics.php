<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     Form
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Contact\Form;

use Zend\Form\Fieldset;
use Zend\Form\Form;

/**
 *
 */
class Statistics extends Form
{
    const IN = 1;
    const NOT_IN = 2;

    protected $inOptions = [
        self::IN     => 'in',
        self::NOT_IN => 'not-in',
    ];

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setAttribute('method', 'get');
        $this->setAttribute('class', 'form-horizontal');

        for ($i = 0; $i <= 2; $i++) {
            $filter = new Fieldset('selection'.$i);
            $filter->add(
                [
                    'type'       => 'Zend\Form\Element\Select',
                    'name'       => 'in',
                    'options'    => [
                        'value_options' => $this->inOptions,
                    ],
                    'attributes' => [
                        'label' => 'search',
                    ],
                ]
            );
            $filter->add(
                [
                    'type'       => 'Zend\Form\Element\Text',
                    'name'       => 'selection',
                    'attributes' => [
                        'label'       => 'search',
                        'class'       => 'form-control',
                        'id'          => "search",
                        'placeholder' => _("txt-site-search"),
                    ],
                ]
            );
            $this->add($filter);
        }

        $this->add(
            [
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'cancel',
                'attributes' => [
                    'class' => "btn btn-danger",
                    'value' => _("txt-cancel"),
                ],
            ]
        );
        $this->add(
            [
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'submit',
                'attributes' => [
                    'class' => "btn btn-primary",
                    'value' => _("txt-submit"),
                ],
            ]
        );
    }
}
