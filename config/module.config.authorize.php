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
                    'roles'     => ['office'],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/contact-admin/new',
                    'roles'     => ['office'],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/contact-admin/list',
                    'roles'     => ['office'],
                    'assertion' => ContactAssertion::class,
                ], [
                    'route' => 'zfcadmin/contact-admin/list-old',
                    'roles' => ['office']
                ],
                [
                    'route'     => 'zfcadmin/contact-admin/export',
                    'roles'     => ['office'],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/contact-admin/list-duplicate',
                    'roles'     => ['office'],
                    'assertion' => ContactAssertion::class,
                ], [
                    'route'     => 'zfcadmin/contact-admin/list-inactive',
                    'roles'     => ['office'],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/contact-admin/edit',
                    'roles'     => ['office'],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/contact-admin/permit',
                    'roles'     => ['office'],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/contact-admin/view',
                    'roles'     => ['office'],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/contact-admin/impersonate',
                    'roles'     => ['office'],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/contact-admin/statistics',
                    'roles'     => ['office'],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/contact-admin/import',
                    'roles'     => ['office'],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route'     => 'zfcadmin/contact-admin/search',
                    'roles'     => ['office'],
                    'assertion' => ContactAssertion::class,
                ],
                [
                    'route' => 'zfcadmin/contact-admin/merge',
                    'roles' => ['office'],
                ],
                [
                    'route' => 'zfcadmin/contact-admin/add-project',
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
                    'route' => 'zfcadmin/selection/generate-deeplinks',
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
                [
                    'route' => 'cli-partner-search-reset',
                    'roles' => [],
                ],
            ],
        ],
    ],
];
