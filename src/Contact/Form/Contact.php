<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Content
 * @package     Form
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
namespace Contact\Form;

use Zend\Form\Form;
use Zend\ServiceManager\ServiceManager;

use Contact\Entity;
use Contact\Entity\EntityAbstract;

/**
 *
 */
class Contact extends Form
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * Class constructor
     */
    public function __construct(ServiceManager $serviceManager, EntityAbstract $object)
    {
        parent::__construct($object->get('underscore_entity_name'));

        $this->serviceManager = $serviceManager;

        $entityManager = $this->serviceManager->get('doctrine.entitymanager.orm_default');

        $contactFieldset = new \Contact\Form\ContactFieldset($entityManager, new Entity\Contact());
        $contactFieldset->setUseAsBaseFieldset(true);
        $this->add($contactFieldset);


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
