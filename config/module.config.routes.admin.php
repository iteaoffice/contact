<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

use Contact\Controller;

return [
    'router' => [
        'routes' => [
            'zfcadmin' => [
                'child_routes' => [
                    'contact'   => [
                        'type'          => 'Segment',
                        'options'       => [
                            'route'    => '/contact',
                            'defaults' => [
                                'controller' => Controller\ContactAdminController::class,
                                'action'     => 'list',
                                'page'       => 1,
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'list'           => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/list[/f-:encodedFilter][/page-:page].html',
                                    'defaults' => [
                                        'action' => 'list',
                                    ],
                                ],
                            ],
                            'list-old'       => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/list-old/[/f-:encodedFilter][/page-:page].html',
                                    'defaults' => [
                                        'action' => 'list-old',
                                    ],
                                ],
                            ],
                            'export'         => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/export[/q-:encodedFilter].csv',
                                    'defaults' => [
                                        'action'    => 'export',
                                        'privilege' => 'export',
                                    ],
                                ],
                            ],
                            'list-duplicate' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/list/duplicate[/f-:encodedFilter][/page-:page].html',
                                    'defaults' => [
                                        'action' => 'list-duplicate',
                                    ],
                                ],
                            ],
                            'list-inactive'  => [
                                'type'    => 'Literal',
                                'options' => [
                                    'route'    => '/list/inactive.html',
                                    'defaults' => [
                                        'action' => 'list-inactive',
                                    ],
                                ],
                            ],
                            'new'            => [
                                'type'    => 'Literal',
                                'options' => [
                                    'route'    => '/new.html',
                                    'defaults' => [
                                        'action' => 'new',
                                    ],
                                ],
                            ],
                            'view'           => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/view/[:id].html',
                                    'defaults' => [
                                        'action' => 'view',
                                    ],
                                ],
                            ],
                            'dnd'            => [
                                'type'         => 'Literal',
                                'options'      => [
                                    'route'    => '/dnd',
                                    'defaults' => [
                                        'action'     => 'dnd',
                                        'controller' => Controller\DndController::class,
                                    ],
                                ],
                                'child_routes' => [
                                    'new'      => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/new/contact-[:contactId].html',
                                            'defaults' => [
                                                'action' => 'new',
                                            ],
                                        ],
                                    ],
                                    'edit'     => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/edit/[:id].html',
                                            'defaults' => [
                                                'action' => 'edit',
                                            ],
                                        ],
                                    ],
                                    'download' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/download/[:id].html',
                                            'defaults' => [
                                                'action' => 'download',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'impersonate' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/impersonate/[:id].html',
                                    'defaults' => [
                                        'action' => 'impersonate',
                                    ],
                                ],
                            ],
                            'edit'        => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/edit/[:id].html',
                                    'defaults' => [
                                        'action' => 'edit',
                                    ],
                                ],
                            ],
                            'permit'      => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/permissions/[:id].html',
                                    'defaults' => [
                                        'action' => 'permit',
                                    ],
                                ],
                            ],
                            'search'      => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/search.html',
                                    'defaults' => [
                                        'action' => 'search',
                                    ],
                                ],
                            ],
                            'import'      => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/import.html',
                                    'defaults' => [
                                        'action' => 'import',
                                    ],
                                ],
                            ],
                            'merge'       => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/merge/[:sourceId]/into/[:targetId].html',
                                    'defaults' => [
                                        'action' => 'merge',
                                    ],
                                ],
                            ],
                            'add-project' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/add-project/[:id].html',
                                    'defaults' => [
                                        'action' => 'add-project',
                                    ],
                                ],
                            ],
                            'office'            => [
                                'type'         => 'Literal',
                                'options'      => [
                                    'route'    => '/office',
                                    'defaults' => [
                                        'controller' => Controller\Office\ContactController::class,
                                    ],
                                ],
                                'may_terminate' => false,
                                'child_routes' => [
                                    'list'      => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/list.html',
                                            'defaults' => [
                                                'action' => 'list',
                                            ],
                                        ],
                                    ],
                                    'view'      => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/view/[:id].html',
                                            'defaults' => [
                                                'action' => 'new',
                                            ],
                                        ],
                                    ],
                                    'new'      => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/new.html',
                                            'defaults' => [
                                                'action' => 'new',
                                            ],
                                        ],
                                    ],
                                    'edit'     => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/edit/[:id].html',
                                            'defaults' => [
                                                'action' => 'edit',
                                            ],
                                        ],
                                    ],
                                    'leave'     => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/leave',
                                            'defaults' => [
                                                'controller' => Controller\Office\LeaveController::class,
                                            ],
                                        ],
                                        'may_terminate' => false,
                                        'child_routes' => [
                                            'manage'      => [
                                                'type'    => 'Segment',
                                                'options' => [
                                                    'route'    => '/manage.html',
                                                    'defaults' => [
                                                        'action' => 'manage',
                                                    ],
                                                ],
                                            ],
                                            'update'      => [
                                                'type'    => 'Segment',
                                                'options' => [
                                                    'route'    => '/update.json',
                                                    'defaults' => [
                                                        'action' => 'update',
                                                    ],
                                                ],
                                            ],
                                            'delete'      => [
                                                'type'    => 'Segment',
                                                'options' => [
                                                    'route'    => '/delete.json',
                                                    'defaults' => [
                                                        'action' => 'delete',
                                                    ],
                                                ],
                                            ],
                                            'fetch'      => [
                                                'type'    => 'Segment',
                                                'options' => [
                                                    'route'    => '/fetch.json',
                                                    'defaults' => [
                                                        'action' => 'fetch',
                                                    ],
                                                ],
                                            ],
                                            'move'      => [
                                                'type'    => 'Segment',
                                                'options' => [
                                                    'route'    => '/move.json',
                                                    'defaults' => [
                                                        'action' => 'move',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'selection' => [
                        'type'          => 'Segment',
                        'options'       => [
                            'route'    => '/selection',
                            'defaults' => [
                                'controller' => Controller\SelectionManagerController::class,
                                'action'     => 'list',
                                'page'       => 1,
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'list'               => [
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => [
                                    'route'    => '/list[/page-:page].html',
                                    'defaults' => [
                                        'action' => 'list',
                                    ],
                                ],
                                'query'    => [
                                    'search' => null,
                                    'page'   => null,
                                ],
                            ],
                            'new'                => [
                                'type'    => 'Literal',
                                'options' => [
                                    'route'    => '/new.html',
                                    'defaults' => [
                                        'action' => 'new',
                                    ],
                                ],
                            ],
                            'view'               => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/view/[:id].html',
                                    'defaults' => [
                                        'action' => 'view',
                                    ],
                                ],
                            ],
                            'generate-deeplinks' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/generate-deeplinks/[:id].html',
                                    'defaults' => [
                                        'action' => 'generate-deeplinks',
                                    ],
                                ],
                            ],
                            'edit'               => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/edit/[:id].html',
                                    'defaults' => [
                                        'action' => 'edit',
                                    ],
                                ],
                            ],
                            'add-contact'        => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/add-contact/contact-[:contactId].html',
                                    'defaults' => [
                                        'action' => 'add-contact',
                                    ],
                                ],
                            ],
                            'edit-contacts'      => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/edit-contacts/[:id].html',
                                    'defaults' => [
                                        'action' => 'edit-contacts',
                                    ],
                                ],
                            ],
                            'get-contacts'       => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/get-contacts.html',
                                    'defaults' => [
                                        'action' => 'get-contacts',
                                    ],
                                ],
                            ],
                            'export'             => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/export/[:type]/[:id].html',
                                    'defaults' => [
                                        'action' => 'export',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'facebook'  => [
                        'type'          => 'Segment',
                        'options'       => [
                            'route'    => '/facebook',
                            'defaults' => [
                                'controller' => Controller\FacebookManagerController::class,
                                'action'     => 'list',
                                'page'       => 1,
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'list' => [
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => [
                                    'route'    => '/list.html',
                                    'defaults' => [
                                        'action' => 'list',
                                    ],
                                ],
                            ],
                            'new'  => [
                                'type'     => 'Literal',
                                'priority' => 1000,
                                'options'  => [
                                    'route'    => '/new.html',
                                    'defaults' => [
                                        'action' => 'new',
                                    ],
                                ],
                            ],
                            'view' => [
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => [
                                    'route'    => '/view/[:id].html',
                                    'defaults' => [
                                        'action' => 'view',
                                    ],
                                ],
                            ],
                            'edit' => [
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => [
                                    'route'    => '/edit/[:id].html',
                                    'defaults' => [
                                        'action' => 'edit',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'opt-in'    => [
                        'type'          => 'Segment',
                        'options'       => [
                            'route'    => '/opt-in',
                            'defaults' => [
                                'controller' => Controller\OptInManagerController::class,
                                'action'     => 'list',
                                'page'       => 1,
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'list' => [
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => [
                                    'route'    => '/list[/f-:encodedFilter][/page-:page].html',
                                    'defaults' => [
                                        'action' => 'list',
                                    ],
                                ],
                            ],
                            'new'  => [
                                'type'     => 'Literal',
                                'priority' => 1000,
                                'options'  => [
                                    'route'    => '/new.html',
                                    'defaults' => [
                                        'action' => 'new',
                                    ],
                                ],
                            ],
                            'view' => [
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => [
                                    'route'    => '/view/[:id].html',
                                    'defaults' => [
                                        'action' => 'view',
                                    ],
                                ],
                            ],
                            'edit' => [
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => [
                                    'route'    => '/edit/[:id].html',
                                    'defaults' => [
                                        'action' => 'edit',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'address'   => [
                        'type'          => 'Segment',
                        'options'       => [
                            'route'    => '/address',
                            'defaults' => [
                                'controller' => Controller\AddressManagerController::class,
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'new'  => [
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => [
                                    'route'    => '/new/contact-[:contact].html',
                                    'defaults' => [
                                        'action' => 'new',
                                    ],
                                ],
                            ],
                            'edit' => [
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => [
                                    'route'    => '/edit/[:id].html',
                                    'defaults' => [
                                        'action' => 'edit',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'phone'     => [
                        'type'          => 'Segment',
                        'options'       => [
                            'route'    => '/phone',
                            'defaults' => [
                                'controller' => Controller\PhoneManagerController::class,
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'new'  => [
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => [
                                    'route'    => '/new/contact-[:contact].html',
                                    'defaults' => [
                                        'action' => 'new',
                                    ],
                                ],
                            ],
                            'edit' => [
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => [
                                    'route'    => '/edit/[:id].html',
                                    'defaults' => [
                                        'action' => 'edit',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'note'      => [
                        'type'          => 'Segment',
                        'options'       => [
                            'route'    => '/note',
                            'defaults' => [
                                'controller' => Controller\NoteManagerController::class,
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'new'  => [
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => [
                                    'route'    => '/new/contact-[:contact].html',
                                    'defaults' => [
                                        'action' => 'new',
                                    ],
                                ],
                            ],
                            'edit' => [
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => [
                                    'route'    => '/edit/[:id].html',
                                    'defaults' => [
                                        'action' => 'edit',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
