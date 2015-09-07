<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c] 2004-2014 ITEA Office (http://itea3.org]
 */

use Contact\Controller;

return [
    'router' => [
        'routes' => [
            'zfcadmin' => [
                'child_routes' => [
                    'contact-admin'     => [
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
                            'list'        => [
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => [
                                    'route'    => '/list[/f-:encodedFilter][/page-:page].html',
                                    'defaults' => [
                                        'action' => 'list',
                                    ],
                                ],
                            ],
                            'view'        => [
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => [
                                    'route'    => '/view/[:id].html',
                                    'defaults' => [
                                        'action' => 'view',
                                    ],
                                ],
                            ],
                            'impersonate' => [
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => [
                                    'route'    => '/impersonate/[:id].html',
                                    'defaults' => [
                                        'action' => 'impersonate',
                                    ],
                                ],
                            ],
                            'edit'        => [
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => [
                                    'route'    => '/edit/[:id].html',
                                    'defaults' => [
                                        'action' => 'edit',
                                    ],
                                ],
                            ],
                            'permit'      => [
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => [
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
                            'statistics'  => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/statistics.html',
                                    'defaults' => [
                                        'action' => 'statistics',
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
                        ],
                    ],
                    'selection-manager' => [
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
                            'list' => [
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => [
                                    'route'    => '/list.html',
                                    'defaults' => [
                                        'action' => 'list',
                                    ],
                                ],
                                'query'    => [
                                    'search' => null,
                                    'page'   => null,
                                ]
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
                    'facebook-manager'  => [
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
                                ]
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
                    'address-manager'   => [
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
                    'phone-manager'   => [
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
                    'note-manager'   => [
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
        ]
    ]
];
