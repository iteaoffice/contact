<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Contact
 * @package     Form
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
namespace Contact\Form;

use Zend\Form\Form;
use Zend\Form\Element;
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

        $this->add(array(
            'name'       => 'currentPassword',
            'options'    => array(
                'label' => _("txt-current-password"),
            ),
            'attributes' => array(
                'type' => 'password'
            ),
        ));

        $this->add(array(
            'name'       => 'password',
            'options'    => array(
                'label' => _("txt-new-password"),
            ),
            'attributes' => array(
                'type' => 'password'
            ),
        ));

        $this->add(array(
            'name'       => 'passwordVerify',
            'options'    => array(
                'label' => _("txt-new-password-verify"),
            ),
            'attributes' => array(
                'type' => 'password'
            ),
        ));

        $submitElement = new Element\Button('submit');
        $submitElement
            ->setLabel(_("txt-submit"))
            ->setAttributes(array(
                'type' => 'submit',
            ));

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Csrf',
                'name' => 'csrf'
            )
        );

        $this->add(
            array(
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'submit',
                'attributes' => array(
                    'class' => "btn btn-primary",
                    'value' => _("txt-submit")
                )
            )
        );
    }
}
