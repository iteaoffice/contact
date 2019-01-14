<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Form;

use Zend\Form\Element;
use Zend\Form\Form;

/**
 * Class Password
 *
 * @package Contact\Form
 */
class Password extends Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setAttribute('action', '');

        $this->add(
            [
                'name'       => 'password',
                'options'    => [
                    'label'      => _("txt-new-password"),
                    'help-block' => _("txt-new-password-form-help"),
                ],
                'attributes' => [
                    'type' => 'password',
                ],
            ]
        );
        $this->add(
            [
                'name'       => 'passwordVerify',
                'options'    => [
                    'label' => _("txt-new-password-verify"),
                ],
                'attributes' => [
                    'type' => 'password',
                ],
            ]
        );
        $submitElement = new Element\Button('submit');
        $submitElement->setLabel(_("txt-submit"))->setAttributes(
            [
                'type' => 'submit',
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
