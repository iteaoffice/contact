<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Contact
 * @package     Config
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
return array(
    'navigation' => array(
        'default' => array(
            'contact' => array(
                'label' => _("txt-contact"),
                'route' => 'contact',
                'pages' => array(
                    'contacts'      => array(
                        'label'     => _("txt-list-contacts"),
                        'route'     => 'contact/contacts',
                        'resource'  => 'contact',
                        'privilege' => 'listings',
                    ),
                    'facilities'     => array(
                        'label'     => _("txt-list-facilities"),
                        'route'     => 'contact/facilities',
                        'resource'  => 'contact',
                        'privilege' => 'listings',
                    ),
                    'areas'          => array(
                        'label'     => _("txt-list-areas"),
                        'route'     => 'contact/areas',
                        'resource'  => 'contact',
                        'privilege' => 'listings',
                    ),
                    'sub-areas'      => array(
                        'label'     => _("txt-list-sub-areas"),
                        'route'     => 'contact/sub-areas',
                        'resource'  => 'contact',
                        'privilege' => 'listings',
                    ),
                    'area2s'         => array(
                        'label'     => _("txt-list-area2s"),
                        'route'     => 'contact/area2s',
                        'resource'  => 'contact',
                        'privilege' => 'listings',
                    ),
                    'oper-areas'     => array(
                        'label'     => _("txt-list-operation-areas"),
                        'route'     => 'contact/oper-areas',
                        'resource'  => 'contact',
                        'privilege' => 'listings',
                    ),
                    'oper-sub-areas' => array(
                        'label'     => _("txt-list-operation-sub-areas"),
                        'route'     => 'contact/oper-sub-areas',
                        'resource'  => 'contact',
                        'privilege' => 'listings',
                    ),
                ),
            ),
            'admin'    => array(
                'pages' => array(
                    'messages' => array(
                        'label' => _('txt-messages'),
                        'route' => 'zfcadmin/contact-manager/messages',
                    ),
                ),
            ),
        ),
    ),
);
