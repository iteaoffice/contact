<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Content
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Form;

use Contact\Entity;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;

class ContactOrganisationFieldset extends Fieldset
{
    /**
     * @param EntityManager $entityManager
     * @param Entity\EntityAbstract $object
     */
    public function __construct(EntityManager $entityManager, Entity\EntityAbstract $object)
    {
        parent::__construct($object->get('underscore_entity_name'));
        $contactOrganisation = new Entity\ContactOrganisation();
        $doctrineHydrator = new DoctrineHydrator($entityManager, 'Contact\Entity\ContactOrganisation');
        $this->setHydrator($doctrineHydrator)->setObject($contactOrganisation);
        $this->add(
            [
                'type'       => 'Zend\Form\Element\Text',
                'name'       => 'organisation',
                'attributes' => [
                    'label' => _("txt-organisation"),
                ],
            ]
        );
        $this->add(
            [
                'type'       => 'DoctrineORMModule\Form\Element\EntitySelect',
                'name'       => 'country',
                'options'    => [
                    'target_class'   => 'General\Entity\Country',
                    'object_manager' => $entityManager,
                ],
                'attributes' => [
                    'label' => _("txt-country"),
                ],
            ]
        );
    }
}
