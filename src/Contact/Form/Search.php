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

use Zend\Form\Form;

/**
 *
 */
class Search extends Form
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setAttribute('method', 'get');
        $this->setAttribute('class', 'form-horizontal');
        $this->add(
            [
                'type'       => 'Zend\Form\Element\Text',
                'name'       => 'q',
                'attributes' => [
                    'label'       => 'search',
                    'class'       => 'form-control',
                    'id'          => "search",
                    'placeholder' => _("txt-site-search"),
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
