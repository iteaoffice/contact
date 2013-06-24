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
    'bjyauthorize' => array(
        // resource providers provide a list of resources that will be tracked
        // in the ACL. like roles, they can be hierarchical
        'resource_providers' => array(
            'BjyAuthorize\Provider\Resource\Config' => array(
                'contact' => array(),
            ),
        ),
        /* rules can be specified here with the format:
         * array(roles (array) , resource, [privilege (array|string), assertion])
         * assertions will be loaded using the service manager and must implement
         * Zend\Acl\Assertion\AssertionInterface.
         * *if you use assertions, define them using the service manager!*
         */
        'rule_providers'     => array(
            'BjyAuthorize\Provider\Rule\Config' => array(
                'allow' => array(
                    // allow guests and users (and admins, through inheritance)
                    // the "wear" privilege on the resource "pants"d
                    array(array(4, 2, 3), 'contact', array('listings', 'view')),
                    array(array(1), 'contact', array('edit', 'new', 'delete'))
                ),
                // Don't mix allow/deny rules if you are using role inheritance.
                // There are some weird bugs.
                'deny'  => array( // ...
                ),
            ),
        ),
        /* Currently, only controller and route guards exist
         */
        'guards'             => array(
            /* If this guard is specified here (i.e. it is enabled), it will block
             * access to all routes unless they are specified here.
             */
            'BjyAuthorize\Guard\Route' => array(
                array('route' => 'contact/contacts', 'roles' => array(4, 2, 3)),
                array('route' => 'contact/contact', 'roles' => array(4, 2, 3)),
                array('route' => 'contact/facilities', 'roles' => array(4, 2, 3)),
                array('route' => 'contact/facility', 'roles' => array(4, 2, 3)),
                array('route' => 'contact/areas', 'roles' => array(4, 2, 3)),
                array('route' => 'contact/area', 'roles' => array(4, 2, 3)),
                array('route' => 'contact/area2s', 'roles' => array(4, 2, 3)),
                array('route' => 'contact/area2', 'roles' => array(4, 2, 3)),
                array('route' => 'contact/sub-areas', 'roles' => array(4, 2, 3)),
                array('route' => 'contact/sub-area', 'roles' => array(4, 2, 3)),
                array('route' => 'contact/oper-areas', 'roles' => array(4, 2, 3)),
                array('route' => 'contact/oper-area', 'roles' => array(4, 2, 3)),
                array('route' => 'contact/oper-sub-areas', 'roles' => array(4, 2, 3)),
                array('route' => 'contact/oper-sub-area', 'roles' => array(4, 2, 3)),
                array('route' => 'contact/edit', 'roles' => array(2)),
                array('route' => 'zfcadmin/contact-manager/messages', 'roles' => array(1)),
                array('route' => 'zfcadmin/contact-manager/message', 'roles' => array(1)),
                array('route' => 'zfcadmin/contact-manager/edit', 'roles' => array(1)),
                array('route' => 'zfcadmin/contact-manager/new', 'roles' => array(1)),
                array('route' => 'zfcadmin/contact-manager/delete', 'roles' => array(1)),
            ),
        ),
    ),
);
