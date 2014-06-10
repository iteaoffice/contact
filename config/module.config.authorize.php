<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c] 2004-2014 ITEA Office (http://itea3.org]
 */
return [
    'bjyauthorize' => [
        // resource providers provide a list of resources that will be tracked
        // in the ACL. like roles, they can be hierarchical
        'resource_providers' => [
            'BjyAuthorize\Provider\Resource\Config' => [
                'contact' => [],
            ],
        ],
        /* rules can be specified here with the format:
         * [roles (array] , resource, [privilege (array|string], assertion]]
         * assertions will be loaded using the service manager and must implement
         * Zend\Acl\Assertion\AssertionInterface.
         * *if you use assertions, define them using the service manager!*
         */
        'rule_providers'     => [
            'BjyAuthorize\Provider\Rule\Config' => [
                'allow' => [
                    // allow guests and users (and admins, through inheritance]
                    // the "wear" privilege on the resource "pants"d
                    [['user'], 'contact', ['edit-profile', 'change-password', 'profile']],
                    [['office'], 'contact', ['view-admin', 'edit-admin', 'impersonate', 'list']],
                ],
                // Don't mix allow/deny rules if you are using role inheritance.
                // There are some weird bugs.
                'deny'  => [ // ...
                ],
            ],
        ],
        /* Currently, only controller and route guards exist
         */
        'guards'             => [
            /* If this guard is specified here (i.e. it is enabled], it will block
             * access to all routes unless they are specified here.
             */
            'BjyAuthorize\Guard\Route' => [
                ['route' => 'contact/edit', 'roles' => ['user']],
                ['route' => 'contact/profile', 'roles' => ['user']],
                ['route' => 'contact/profile-edit', 'roles' => ['user']],
                ['route' => 'contact/opt-in-update', 'roles' => ['user']],
                ['route' => 'contact/change-password', 'roles' => ['user']],
                ['route' => 'contact/signature', 'roles' => ['office']],
                ['route' => 'contact/photo', 'roles' => []],
                ['route' => 'contact/search', 'roles' => ['office']],
                ['route' => 'zfcadmin/contact-manager', 'roles' => ['office']],
                ['route' => 'zfcadmin/contact-manager/list', 'roles' => ['office']],
                ['route' => 'zfcadmin/contact-manager/view', 'roles' => ['office']],
                ['route' => 'zfcadmin/contact-manager/impersonate', 'roles' => ['office']],

            ],
        ],
    ],
];
