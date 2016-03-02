<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c] 2004-2015 ITEA Office (https://itea3.org]
 */
use Admin\Entity\Access;
use Contact\Acl\Assertion\Address as AddressAssertion;
use Contact\Acl\Assertion\Contact as ContactAssertion;
use Contact\Acl\Assertion\Facebook as FacebookAssertion;
use Contact\Acl\Assertion\Note as NoteAssertion;
use Contact\Acl\Assertion\Phone as PhoneAssertion;

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
                    'route'     => 'community/contact/profile/edit',
                    'roles'     => [strtolower(Access::ACCESS_USER)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route'     => 'community/contact/profile/view',
                    'roles'     => [strtolower(Access::ACCESS_USER)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route'     => 'community/contact/profile/contact',
                    'roles'     => [strtolower(Access::ACCESS_USER)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route'     => 'community/contact/signature',
                    'roles'     => [strtolower(Access::ACCESS_USER)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route'     => 'community/contact/search',
                    'roles'     => [],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route'     => 'contact/get-address-by-type',
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
                    'route'     => 'contact/photo',
                    'roles'     => [strtolower(Access::ACCESS_USER)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route' => 'assets/contact-photo',
                    'roles' => []
                ],
                [
                    'route'     => 'zfcadmin/contact-admin',
                    'roles'     => [strtolower(Access::ACCESS_OFFICE)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route'     => 'zfcadmin/contact-admin/new',
                    'roles'     => [strtolower(Access::ACCESS_OFFICE)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route'     => 'zfcadmin/contact-admin/list',
                    'roles'     => [strtolower(Access::ACCESS_OFFICE)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route'     => 'zfcadmin/contact-admin/edit',
                    'roles'     => [strtolower(Access::ACCESS_OFFICE)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route'     => 'zfcadmin/contact-admin/permit',
                    'roles'     => [strtolower(Access::ACCESS_OFFICE)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route'     => 'zfcadmin/contact-admin/view',
                    'roles'     => [strtolower(Access::ACCESS_OFFICE)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route'     => 'zfcadmin/contact-admin/impersonate',
                    'roles'     => [strtolower(Access::ACCESS_OFFICE)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route'     => 'zfcadmin/contact-admin/statistics',
                    'roles'     => [strtolower(Access::ACCESS_OFFICE)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route'     => 'zfcadmin/contact-admin/import',
                    'roles'     => [strtolower(Access::ACCESS_OFFICE)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route'     => 'zfcadmin/contact-admin/search',
                    'roles'     => [strtolower(Access::ACCESS_OFFICE)],
                    'assertion' => ContactAssertion::class
                ],
                [
                    'route' => 'zfcadmin/selection-manager/list',
                    'roles' => [strtolower(Access::ACCESS_OFFICE)]
                ],
                [
                    'route' => 'zfcadmin/selection-manager/new',
                    'roles' => [strtolower(Access::ACCESS_OFFICE)]
                ],
                [
                    'route' => 'zfcadmin/selection-manager/edit',
                    'roles' => [strtolower(Access::ACCESS_OFFICE)]
                ],
                [
                    'route' => 'zfcadmin/selection-manager/edit-contacts',
                    'roles' => [strtolower(Access::ACCESS_OFFICE)]
                ],
                [
                    'route' => 'zfcadmin/selection-manager/get-contacts',
                    'roles' => [strtolower(Access::ACCESS_OFFICE)]
                ],
                [
                    'route' => 'zfcadmin/selection-manager/export/csv',
                    'roles' => [strtolower(Access::ACCESS_OFFICE)]
                ],
                [
                    'route' => 'zfcadmin/selection-manager/export/excel',
                    'roles' => [strtolower(Access::ACCESS_OFFICE)]
                ],
                [
                    'route' => 'zfcadmin/selection-manager/view',
                    'roles' => [strtolower(Access::ACCESS_OFFICE)]
                ],
                [
                    'route' => 'zfcadmin/facebook-manager/list',
                    'roles' => [strtolower(Access::ACCESS_OFFICE)]
                ],
                [
                    'route' => 'zfcadmin/facebook-manager/new',
                    'roles' => [strtolower(Access::ACCESS_OFFICE)]
                ],
                [
                    'route' => 'zfcadmin/facebook-manager/edit',
                    'roles' => [strtolower(Access::ACCESS_OFFICE)]
                ],
                [
                    'route' => 'zfcadmin/facebook-manager/view',
                    'roles' => [strtolower(Access::ACCESS_OFFICE)]
                ],
                [
                    'route'     => 'zfcadmin/address-manager/edit',
                    'roles'     => [],
                    'assertion' => AddressAssertion::class
                ],
                [
                    'route'     => 'zfcadmin/address-manager/new',
                    'roles'     => [],
                    'assertion' => AddressAssertion::class
                ],
                [
                    'route'     => 'zfcadmin/phone-manager/edit',
                    'roles'     => [],
                    'assertion' => PhoneAssertion::class
                ],
                [
                    'route'     => 'zfcadmin/phone-manager/new',
                    'roles'     => [],
                    'assertion' => PhoneAssertion::class
                ],
                [
                    'route'     => 'zfcadmin/note-manager/edit',
                    'roles'     => [],
                    'assertion' => NoteAssertion::class
                ],
                [
                    'route'     => 'zfcadmin/note-manager/new',
                    'roles'     => [],
                    'assertion' => NoteAssertion::class
                ],
                [
                    'route' => 'cli-partner-search-reset',
                    'roles' => []
                ],
            ],
        ],
    ],
];
