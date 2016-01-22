<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */
use Contact\Entity;
use Contact\Form;
use Contact\InputFilter;
use Contact\Options;
use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\ServiceManager;

return [
    'factories' => [
        'contact_community_options'   => function (ServiceManager $sm) {
            $config = $sm->get('Config');

            return new Options\ModuleOptions(isset($config['community']) ? $config['community'] : []);
        },
        'contact_contact_form'        => function (ServiceManager $sm) {
            return new Form\Contact($sm, new Entity\Contact());
        },
        'contact_facebook_form'       => function (ServiceManager $sm) {
            return new Form\CreateObject($sm, new Entity\Facebook());
        },
        'contact_address_form'        => function (ServiceManager $sm) {
            return new Form\CreateObject($sm, new Entity\Address());
        },
        'contact_note_form'           => function (ServiceManager $sm) {
            return new Form\CreateObject($sm, new Entity\Note());
        },
        'contact_phone_form'          => function (ServiceManager $sm) {
            return new Form\CreateObject($sm, new Entity\Phone());
        },
        'contact_selection_form'      => function (ServiceManager $sm) {
            return new Form\CreateObject($sm, new Entity\Selection());
        },
        'contact_impersonate_form'    => function (ServiceManager $sm) {
            return new Form\Impersonate($sm);
        },
        'contact_contact_form_filter' => function (ServiceManager $sm) {
            return new InputFilter\ContactFilter($sm->get(EntityManager::class));

        },
    ],
];
