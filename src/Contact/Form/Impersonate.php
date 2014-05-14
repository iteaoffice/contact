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
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

/**
 *
 */
class Impersonate extends Form implements ServiceLocatorAwareInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Class constructor
     */
    public function __construct(ServiceManager $sm)
    {
        parent::__construct();
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $this->add(
            array(
                'type'       => 'DoctrineORMModule\Form\Element\EntitySelect',
                'name'       => 'target',
                'options'    => array(
                    'target_class'   => 'Deeplink\Entity\Target',
                    'object_manager' => $this->getServiceLocator()->get('doctrine.entitymanager.orm_default'),
                    'find_method'    => array(
                        'name'   => 'findTargetsWithRoute',
                        'params' => array(
                            'criteria' => array(),
                            'orderBy'  => array(),
                        ),
                    ),
                    'help-block'     => _("txt-deeplink-target-form-element-explanation"),
                ),
                'attributes' => array(
                    'label' => ucfirst(_("txt-target")),
                    'class' => 'form-control',
                    'id'    => "target",
                )

            )
        );

        $this->add(
            array(
                'type'       => 'Zend\Form\Element\Text',
                'name'       => 'key',
                'options'    => array(
                    'help-block' => _("txt-deeplink-key-form-element-explanation"),
                ),
                'attributes' => array(
                    'label'       => ucfirst(_("txt-key")),
                    'class'       => 'form-control',
                    'id'          => "key",
                    'placeholder' => _("txt-key")
                )
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

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
}
