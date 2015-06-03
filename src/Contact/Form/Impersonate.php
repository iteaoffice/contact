<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */

namespace Contact\Form;

use Zend\Form\Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Class Impersonate.
 */
class Impersonate extends Form
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @param ServiceManager $sm
     */
    public function __construct(ServiceManager $sm)
    {
        parent::__construct();
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '');
        $this->setAttribute('class', 'form-horizontal');
        $this->add(
            [
                'type'       => 'DoctrineORMModule\Form\Element\EntitySelect',
                'name'       => 'target',
                'options'    => [
                    'target_class'   => 'Deeplink\Entity\Target',
                    'object_manager' => $sm->get('Doctrine\ORM\EntityManager'),
                    'find_method'    => [
                        'name'   => 'findTargetsWithRoute',
                        'params' => [
                            'criteria' => [],
                            'orderBy'  => [],
                        ],
                    ],
                    'help-block'     => _("txt-deeplink-target-form-element-explanation"),
                ],
                'attributes' => [
                    'label' => ucfirst(_("txt-target")),
                    'class' => 'form-control',
                    'id'    => "target",
                ],
            ]
        );
        $this->add(
            [
                'type'       => 'Zend\Form\Element\Text',
                'name'       => 'key',
                'options'    => [
                    'help-block' => _("txt-deeplink-key-form-element-explanation"),
                ],
                'attributes' => [
                    'label'       => ucfirst(_("txt-key")),
                    'class'       => 'form-control',
                    'id'          => "key",
                    'placeholder' => _("txt-key"),
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
