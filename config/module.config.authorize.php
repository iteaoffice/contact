<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

use BjyAuthorize\Guard\Route;
use Contact\Acl\Assertion\Address as AddressAssertion;
use Contact\Acl\Assertion\Contact as ContactAssertion;
use Contact\Acl\Assertion\Facebook as FacebookAssertion;
use Contact\Acl\Assertion\Note as NoteAssertion;
use Contact\Acl\Assertion\Phone as PhoneAssertion;
use Contact\Acl\Assertion\Profile as ProfileAssertion;

return [
    'bjyauthorize' => [
        'guards' => [
            Route::class => [
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
                    'route'     => 'community/contact/profile/my',
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
                    'assertion' => ProfileAssertion::class, //This is about YOUR profile, for someone else, we use a different ACL here (for now)
                ],
                [
                    'route'     => 'community/contact/profile/send-message',
                    'roles'     => [],
                    'assertion' => ProfileAssertion::class,//This is about YOUR profile, for someone else, we use a different ACL here (for now)
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
                    'route'     => 'community/contact/profile/manage-body',
                    'roles'     => [],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'community/contact/profile/manage-hlg',
                    'roles'     => [],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'community/contact/profile/manage-external',
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
                    'route'     => 'community/contact/change-password',
                    'roles'     => [],
                    'assertion' => ContactAssertion::class,
                ],
                ['route' => 'zfcadmin/contact/dnd/new', 'roles' => ['office']],
                ['route' => 'zfcadmin/contact/dnd/edit', 'roles' => ['office']],
                ['route' => 'zfcadmin/contact/dnd/download', 'roles' => ['office']],
                [
                    'route'     => 'zfcadmin/contact',
                    'roles'     => ['office'],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/contact/new',
                    'roles'     => ['office'],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/contact/list',
                    'roles'     => ['office'],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route' => 'zfcadmin/contact/list-old',
                    'roles' => ['office']
                ],
                [
                    'route'     => 'zfcadmin/contact/export',
                    'roles'     => ['office'],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/contact/list-duplicate',
                    'roles'     => ['office'],
                    'assertion' => ContactAssertion::class,
                ], [
                    'route'     => 'zfcadmin/contact/list-inactive',
                    'roles'     => ['office'],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/contact/edit',
                    'roles'     => ['office'],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/contact/permit',
                    'roles'     => ['office'],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route' => 'zfcadmin/contact/view/general',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/contact/view/notes',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/contact/view/address',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/contact/view/phone',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/contact/view/selection',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/contact/view/mailing',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/contact/view/idea',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/contact/view/project',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/contact/view/legal',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/contact/view/event',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/contact/view/calendar',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/contact/view/merge',
                    'roles' => ['office'],
                ],
                [
                    'route'     => 'zfcadmin/contact/impersonate',
                    'roles'     => ['office'],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/contact/statistics',
                    'roles'     => ['office'],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/contact/import',
                    'roles'     => ['office'],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/contact/search',
                    'roles'     => ['office'],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route' => 'zfcadmin/contact/office/list',
                    'roles' => ['management_assistant'],
                ],
                [
                    'route' => 'zfcadmin/contact/office/view',
                    'roles' => ['management_assistant'],
                ],
                [
                    'route' => 'zfcadmin/contact/office/new',
                    'roles' => ['management_assistant'],
                ],
                [
                    'route' => 'zfcadmin/contact/office/edit',
                    'roles' => ['management_assistant'],
                ],
                [
                    'route' => 'zfcadmin/contact/office/new-leave',
                    'roles' => ['management_assistant'],
                ],
                [
                    'route' => 'zfcadmin/contact/office/edit-leave',
                    'roles' => ['management_assistant'],
                ],
                [
                    'route' => 'zfcadmin/contact/office/leave/calendar',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/contact/office/leave/office-calendar',
                    'roles' => ['management_assistant'],
                ],
                [
                    'route' => 'zfcadmin/contact/office/leave/list',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/contact/office/leave/new',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/contact/office/leave/edit',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/contact/office/leave/update',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/contact/office/leave/delete',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/contact/office/leave/fetch',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/contact/office/leave/fetch-all',
                    'roles' => ['management_assistant'],
                ],
                [
                    'route' => 'zfcadmin/contact/office/leave/move',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/contact/merge',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/contact/add-project',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/selection/list',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/selection/new',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/selection/copy',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/selection/generate-deeplinks',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/selection/copy',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/selection/edit',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/selection/add-contact',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/selection/edit-contacts',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/selection/get-contacts',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/selection/export',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/selection/view',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/selection/type/list',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/selection/type/new',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/selection/type/edit',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/selection/type/view',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/facebook/list',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/facebook/new',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/facebook/edit',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/facebook/view',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/opt-in/list',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/opt-in/new',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/opt-in/edit',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/opt-in/view',
                    'roles' => ['office'],
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
            ],
        ],
    ],
];
