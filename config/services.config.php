<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Contact
 * @package     Config
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
use Contact\Form;

return array(
    'factories' => array(
        'contact_contact_form'      => function ($sm) {
            return new Form\CreateContact($sm);
        },
        'contact_facility_form'      => function ($sm) {
            return new Form\CreateFacility($sm);
        },
        'contact_area_form'          => function ($sm) {
            return new Form\CreateArea($sm);
        },
        'contact_area2_form'         => function ($sm) {
            return new Form\CreateArea2($sm);
        },
        'contact_sub_area_form'      => function ($sm) {
            return new Form\CreateSubArea($sm);
        },
        'contact_oper_area_form'     => function ($sm) {
            return new Form\CreateOperArea($sm);
        },
        'contact_oper_sub_area_form' => function ($sm) {
            return new Form\CreateOperSubArea($sm);
        },
        'contact_message_form'       => function ($sm) {
            return new Form\CreateMessage($sm);
        },
    ),
);
