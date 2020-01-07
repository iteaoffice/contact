<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Form;

use Laminas\Form\Element\Submit;
use Laminas\Form\Form;

final class Password extends Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setAttribute('action', '');

        $this->add(
            [
                'name'       => 'password',
                'options'    => [
                    'label'      => _('txt-new-password'),
                    'help-block' => _('txt-new-password-form-help'),
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
                    'label' => _('txt-new-password-verify'),
                ],
                'attributes' => [
                    'type' => 'password',
                ],
            ]
        );
        $this->add(
            [
                'type'       => Submit::class,
                'name'       => 'submit',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => _('txt-submit'),
                ],
            ]
        );
    }
}
