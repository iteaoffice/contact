<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c] 2004-2014 ITEA Office (http://itea3.org]
 */
use Contact\Acl\Assertion\Contact as ContactAssertion;

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
                'allow' => [],
                // Don't mix allow/deny rules if you are using role inheritance.
                // There are some weird bugs.
                'deny'  => [],
            ],
        ],
        /* Currently, only controller and route guards exist
         */
        'guards'             => [
            /* If this guard is specified here (i.e. it is enabled], it will block
             * access to all routes unless they are specified here.
             */
            'BjyAuthorize\Guard\Route' => [
                ['route' => 'contact/edit', 'roles' => [], 'assertion' => ContactAssertion::class],
                ['route' => 'contact/profile', 'roles' => [], 'assertion' => ContactAssertion::class],
                ['route' => 'contact/profile-edit', 'roles' => [], 'assertion' => ContactAssertion::class],
                ['route' => 'contact/opt-in-update', 'roles' => [], 'assertion' => ContactAssertion::class],
                ['route' => 'contact/change-password', 'roles' => [], 'assertion' => ContactAssertion::class],
                ['route' => 'contact/signature', 'roles' => [], 'assertion' => ContactAssertion::class],
                ['route' => 'contact/photo', 'roles' => [], 'assertion' => ContactAssertion::class],
                ['route' => 'contact/search', 'roles' => [], 'assertion' => ContactAssertion::class],
                ['route' => 'assets/contact-photo', 'roles' => []],
                ['route' => 'zfcadmin/contact-manager', 'roles' => [], 'assertion' => ContactAssertion::class],
                ['route' => 'zfcadmin/contact-manager/list', 'roles' => [], 'assertion' => ContactAssertion::class],
                ['route' => 'zfcadmin/contact-manager/edit', 'roles' => [], 'assertion' => ContactAssertion::class],
                ['route' => 'zfcadmin/contact-manager/permit', 'roles' => [], 'assertion' => ContactAssertion::class],
                ['route' => 'zfcadmin/contact-manager/view', 'roles' => [], 'assertion' => ContactAssertion::class],
                [
                    'route'     => 'zfcadmin/contact-manager/impersonate',
                    'roles'     => [],
                    'assertion' => ContactAssertion::class
                ],
            ],
        ],
    ],
];
