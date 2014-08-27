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

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\ServiceManager\ServiceManager;

/**
 *
 */
class Password extends Form
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        //        $this->add(array(
        //            'name'       => 'currentPassword',
        //            'options'    => array(
        //                'label'      => _("txt-current-password"),
        //                'help-block' => _("txt-current-password-form-help")
        //            ),
        //            'attributes' => array(
        //                'type' => 'password'
        //            ),
        //        ));
        $this->add(
            [
                'name'       => 'password',
                'options'    => [
                    'label'      => _("txt-new-password"),
                    'help-block' => _("txt-new-password-form-help")
                ],
                'attributes' => [
                    'type' => 'password'
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
                    'type' => 'password'
                ],
            ]
        );
        $submitElement = new Element\Button('submit');
        $submitElement
            ->setLabel(_("txt-submit"))
            ->setAttributes(
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
                    'value' => _("txt-submit")
                ]
            ]
        );
    }
}
