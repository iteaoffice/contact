<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Content
 * @package     Form
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Contact\Form;

use Contact\Entity;
use Contact\Entity\EntityAbstract;
use Zend\Form\Form;
use Zend\ServiceManager\ServiceManager;

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
     * @param ServiceManager $serviceManager
     * @param EntityAbstract $object
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
