<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

use Admin\Entity\Access;
use Contact\Acl\Assertion\Address as AddressAssertion;
use Contact\Acl\Assertion\Contact as ContactAssertion;
use Contact\Acl\Assertion\Facebook as FacebookAssertion;
use Contact\Acl\Assertion\Note as NoteAssertion;
use Contact\Acl\Assertion\Phone as PhoneAssertion;

return [
    'bjyauthorize' => [
        /* Currently, only controller and route guards exist
         */
        'guards' => [
            /* If this guard is specified here (i.e. it is enabled], it will block
             * access to all routes unless they are specified here.
             */
            'BjyAuthorize\Guard\Route' => [
                [
                    'route'     => 'community/contact/facebook/view',
                    'roles'     => [],
                    'assertion' => FacebookAssertion::class,
                ],
                [
                    'route'     => 'community/contact/facebook/send-message',
                    'roles'     => [],
                    'assertion' => FacebookAssertion::class,
                ],
                [
                    'route'     => 'community/contact/profile/edit',
                    'roles'     => [],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'community/contact/profile/view',
                    'roles'     => [],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'community/contact/profile/contact',
                    'roles'     => [],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'community/contact/signature',
                    'roles'     => [],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'community/contact/search',
                    'roles'     => [],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'community/contact/get-address-by-type',
                    'roles'     => [],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'community/contact/opt-in-update',
                    'roles'     => [],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route' => 'contact/has-session',
                    'roles' => [
                        strtolower(Access::ACCESS_PUBLIC),
                        strtolower(Access::ACCESS_USER),
                    ],
                ],
                [
                    'route'     => 'community/contact/change-password',
                    'roles'     => [],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'community/contact/photo',
                    'roles'     => [],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route' => 'assets/contact-photo',
                    'roles' => [],
                ],
                [
                    'route'     => 'zfcadmin/contact-admin',
                    'roles'     => [Access::ACCESS_OFFICE],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/contact-admin/new',
                    'roles'     => [Access::ACCESS_OFFICE],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/contact-admin/list',
                    'roles'     => [Access::ACCESS_OFFICE],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/contact-admin/export',
                    'roles'     => [Access::ACCESS_OFFICE],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/contact-admin/edit',
                    'roles'     => [Access::ACCESS_OFFICE],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/contact-admin/permit',
                    'roles'     => [Access::ACCESS_OFFICE],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/contact-admin/view',
                    'roles'     => [Access::ACCESS_OFFICE],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/contact-admin/impersonate',
                    'roles'     => [Access::ACCESS_OFFICE],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/contact-admin/statistics',
                    'roles'     => [Access::ACCESS_OFFICE],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/contact-admin/import',
                    'roles'     => [Access::ACCESS_OFFICE],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/contact-admin/search',
                    'roles'     => [Access::ACCESS_OFFICE],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route' => 'zfcadmin/selection/list',
                    'roles' => [Access::ACCESS_OFFICE],
                ],
                [
                    'route' => 'zfcadmin/selection/new',
                    'roles' => [Access::ACCESS_OFFICE],
                ],
                [
                    'route' => 'zfcadmin/selection/edit',
                    'roles' => [Access::ACCESS_OFFICE],
                ],
                [
                    'route' => 'zfcadmin/selection/add-contact',
                    'roles' => [Access::ACCESS_OFFICE],
                ],
                [
                    'route' => 'zfcadmin/selection/edit-contacts',
                    'roles' => [Access::ACCESS_OFFICE],
                ],
                [
                    'route' => 'zfcadmin/selection/get-contacts',
                    'roles' => [Access::ACCESS_OFFICE],
                ],
                [
                    'route' => 'zfcadmin/selection/export/csv',
                    'roles' => [Access::ACCESS_OFFICE],
                ],
                [
                    'route' => 'zfcadmin/selection/export/excel',
                    'roles' => [Access::ACCESS_OFFICE],
                ],
                [
                    'route' => 'zfcadmin/selection/view',
                    'roles' => [Access::ACCESS_OFFICE],
                ],
                [
                    'route' => 'zfcadmin/facebook/list',
                    'roles' => [Access::ACCESS_OFFICE],
                ],
                [
                    'route' => 'zfcadmin/facebook/new',
                    'roles' => [Access::ACCESS_OFFICE],
                ],
                [
                    'route' => 'zfcadmin/facebook/edit',
                    'roles' => [Access::ACCESS_OFFICE],
                ],
                [
                    'route' => 'zfcadmin/facebook/view',
                    'roles' => [Access::ACCESS_OFFICE],
                ],
                [
                    'route'     => 'zfcadmin/address/edit',
                    'roles'     => [],
                    'assertion' => AddressAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/address/new',
                    'roles'     => [],
                    'assertion' => AddressAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/phone/edit',
                    'roles'     => [],
                    'assertion' => PhoneAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/phone/new',
                    'roles'     => [],
                    'assertion' => PhoneAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/note/edit',
                    'roles'     => [],
                    'assertion' => NoteAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/note/new',
                    'roles'     => [],
                    'assertion' => NoteAssertion::class,
                ],
                [
                    'route' => 'cli-partner-search-reset',
                    'roles' => [],
                ],
            ],
        ],
    ],
];
