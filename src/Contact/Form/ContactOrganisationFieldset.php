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
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;

class ContactOrganisationFieldset extends Fieldset
{
    /**
     * @param EntityManager         $entityManager
     * @param Entity\EntityAbstract $object
     */
    public function __construct(EntityManager $entityManager, Entity\EntityAbstract $object)
    {
        parent::__construct($object->get('underscore_entity_name'));
        $contactOrganisation = new Entity\ContactOrganisation();
        $doctrineHydrator    = new DoctrineHydrator($entityManager, 'Contact\Entity\ContactOrganisation');
        $this->setHydrator($doctrineHydrator)->setObject($contactOrganisation);
        $this->add(
            array(
                'type'       => 'Zend\Form\Element\Text',
                'name'       => 'organisation',
                'attributes' => array(
                    'label' => _("txt-organisation")
                )
            )
        );
        $this->add(
            array(
                'type'       => 'DoctrineORMModule\Form\Element\EntitySelect',
                'name'       => 'country',
                'options'    => array(
                    'target_class'   => 'General\Entity\Country',
                    'object_manager' => $entityManager
                ),
                'attributes' => array(
                    'label' => _("txt-country")
                )
            )
        );
    }
}
