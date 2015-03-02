<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c] 2004-2014 ITEA Office (http://itea3.org]
 */
use Admin\Entity\Access;
use Contact\Acl\Assertion\Contact as ContactAssertion;
use Contact\Acl\Assertion\Facebook as FacebookAssertion;

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
                [
                    'route'     => 'contact/edit',
                    'roles'     => [strtolower(Access::ACCESS_USER)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route'     => 'community/contact/profile',
                    'roles'     => [strtolower(Access::ACCESS_USER)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route' => 'community/contact',
                    'roles' => [strtolower(Access::ACCESS_USER)]
                ],
                [
                    'route'     => 'community/contact/facebook/facebook',
                    'roles'     => [strtolower(Access::ACCESS_USER)],
                    'assertion' => FacebookAssertion::class
                ],
                [
                    'route'     => 'community/contact/facebook/send-message',
                    'roles'     => [strtolower(Access::ACCESS_USER)],
                    'assertion' => FacebookAssertion::class
                ],
                [
                    'route'     => 'contact/profile',
                    'roles'     => [strtolower(Access::ACCESS_USER)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route'     => 'contact/profile-edit',
                    'roles'     => [strtolower(Access::ACCESS_USER)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route'     => 'contact/get-address-by-type',
                    'roles'     => [strtolower(Access::ACCESS_USER)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route'     => 'community/contact/profile-edit',
                    'roles'     => [strtolower(Access::ACCESS_USER)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route'     => 'contact/opt-in-update',
                    'roles'     => [strtolower(Access::ACCESS_USER)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route' => 'contact/has-session',
                    'roles' => [
                        strtolower(Access::ACCESS_PUBLIC),
                        strtolower(Access::ACCESS_USER)
                    ]
                ],
                [
                    'route'     => 'contact/change-password',
                    'roles'     => [strtolower(Access::ACCESS_USER)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route'     => 'contact/signature',
                    'roles'     => [strtolower(Access::ACCESS_USER)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route'     => 'contact/photo',
                    'roles'     => [strtolower(Access::ACCESS_USER)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route'     => 'contact/search',
                    'roles'     => [strtolower(Access::ACCESS_USER)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route' => 'assets/contact-photo',
                    'roles' => []
                ],
                [
                    'route'     => 'zfcadmin/contact-manager',
                    'roles'     => [strtolower(Access::ACCESS_OFFICE)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route'     => 'zfcadmin/contact-manager/list',
                    'roles'     => [strtolower(Access::ACCESS_OFFICE)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route'     => 'zfcadmin/contact-manager/edit',
                    'roles'     => [strtolower(Access::ACCESS_OFFICE)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route'     => 'zfcadmin/contact-manager/permit',
                    'roles'     => [strtolower(Access::ACCESS_OFFICE)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route'     => 'zfcadmin/contact-manager/view',
                    'roles'     => [strtolower(Access::ACCESS_OFFICE)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route'     => 'zfcadmin/contact-manager/impersonate',
                    'roles'     => [strtolower(Access::ACCESS_OFFICE)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route'     => 'zfcadmin/contact-manager/statistics',
                    'roles'     => [strtolower(Access::ACCESS_OFFICE)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route'     => 'zfcadmin/contact-manager/import',
                    'roles'     => [strtolower(Access::ACCESS_OFFICE)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route'     => 'zfcadmin/contact-manager/search',
                    'roles'     => [strtolower(Access::ACCESS_OFFICE)],
                    'assertion' => ContactAssertion::class
                ],
                ['route' => 'zfcadmin/selection-manager/list',
                 'roles' => [strtolower(Access::ACCESS_OFFICE)]],
                ['route' => 'zfcadmin/selection-manager/new',
                 'roles' => [strtolower(Access::ACCESS_OFFICE)]],
                ['route' => 'zfcadmin/selection-manager/edit',
                 'roles' => [strtolower(Access::ACCESS_OFFICE)]],
                ['route' => 'zfcadmin/selection-manager/view',
                 'roles' => [strtolower(Access::ACCESS_OFFICE)]],
                ['route' => 'zfcadmin/facebook-manager/list',
                 'roles' => [strtolower(Access::ACCESS_OFFICE)]],
                ['route' => 'zfcadmin/facebook-manager/new',
                 'roles' => [strtolower(Access::ACCESS_OFFICE)]],
                ['route' => 'zfcadmin/facebook-manager/edit',
                 'roles' => [strtolower(Access::ACCESS_OFFICE)]],
                ['route' => 'zfcadmin/facebook-manager/view',
                 'roles' => [strtolower(Access::ACCESS_OFFICE)]],
            ],
        ],
    ],
];
