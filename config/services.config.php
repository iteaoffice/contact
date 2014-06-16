<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
use Contact\Entity;
use Contact\Form;
use Contact\Options;

return array(
    'factories' => array(
        'contact_community_options' => function ($sm) {
            $config = $sm->get('Config');

            return new Options\ModuleOptions(isset($config['community']) ? $config['community'] : []);
        },
        'contact_contact_form'      => function ($sm) {
            return new Form\Contact($sm, new Entity\Contact());
        },
        'contact_impersonate_form'  => function ($sm) {
            return new Form\Impersonate($sm);
        },
    ),
);
