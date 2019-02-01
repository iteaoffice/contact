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
        'guards' => [
            \BjyAuthorize\Guard\Route::class => [
                [
                    'route' => 'image/contact-photo',
                    'roles' => [],
                ],
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
                    'route'     => 'community/contact/profile/organisation',
                    'roles'     => [],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'community/contact/profile/events',
                    'roles'     => [],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'community/contact/profile/privacy',
                    'roles'     => [],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'community/contact/profile/contact',
                    'roles'     => [],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'community/contact/profile/create',
                    'roles'     => [],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'community/contact/profile/activate',
                    'roles'     => [],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'community/contact/profile/activate-optin',
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
                    'route'     => 'community/contact/change-password',
                    'roles'     => [],
                    'assertion' => ContactAssertion::class,
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
                    'route'     => 'zfcadmin/contact-admin/list-duplicate',
                    'roles'     => [Access::ACCESS_OFFICE],
                    'assertion' => ContactAssertion::class,
                ], [
                    'route'     => 'zfcadmin/contact-admin/list-inactive',
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
                    'route' => 'zfcadmin/contact-admin/merge',
                    'roles' => [Access::ACCESS_OFFICE],
                ],
                [
                    'route' => 'zfcadmin/contact-admin/add-project',
                    'roles' => [Access::ACCESS_OFFICE],
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
                    'route' => 'zfcadmin/selection/generate-deeplinks',
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
                    'route' => 'zfcadmin/selection/export',
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
                    'route' => 'zfcadmin/opt-in/list',
                    'roles' => [Access::ACCESS_OFFICE],
                ],
                [
                    'route' => 'zfcadmin/opt-in/new',
                    'roles' => [Access::ACCESS_OFFICE],
                ],
                [
                    'route' => 'zfcadmin/opt-in/edit',
                    'roles' => [Access::ACCESS_OFFICE],
                ],
                [
                    'route' => 'zfcadmin/opt-in/view',
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
