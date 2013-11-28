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
                    array(array('user'), 'contact', array('edit-profile', 'change-password')),
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
                array('route' => 'contact/edit', 'roles' => array('user')),
                array('route' => 'contact/profile', 'roles' => array('user')),
                array('route' => 'contact/profile-edit', 'roles' => array('user')),
                array('route' => 'contact/change-password', 'roles' => array('user')),
                array('route' => 'contact/photo', 'roles' => array()),
            ),
        ),
    ),
);
