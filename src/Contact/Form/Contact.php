<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Content
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
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

        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '');

        $this->serviceManager = $serviceManager;
        /** @var \Doctrine\ORM\EntityManager $entityManager */
        $entityManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');
        $contactFieldset = new ContactFieldset($entityManager, new Entity\Contact());
        $contactFieldset->setUseAsBaseFieldset(true);
        $this->add($contactFieldset);



        $this->add([
            'type'       => 'Zend\Form\Element\Submit',
            'name'       => 'cancel',
            'attributes' => [
                'class' => "btn btn-warning",
                'value' => _("txt-cancel"),
            ],
        ]);

        $this->add([
            'type'       => 'Zend\Form\Element\Submit',
            'name'       => 'deactivate',
            'attributes' => [
                'class' => "btn btn-danger",
                'value' => _("txt-deactivate"),
            ],
        ]);

        $this->add([
            'type'       => 'Zend\Form\Element\Submit',
            'name'       => 'reactivate',
            'attributes' => [
                'class' => "btn btn-success",
                'value' => _("txt-reactivate"),
            ],
        ]);

        $this->add([
            'type'       => 'Zend\Form\Element\Submit',
            'name'       => 'submit',
            'attributes' => [
                'class' => "btn btn-primary",
                'value' => _("txt-submit"),
            ],
        ]);
    }
}
