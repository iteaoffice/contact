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
            'assets'    => [
                'type'          => 'Literal',
                'priority'      => 1000,
                'options'       => [
                    'route'    => '/assets/' . (defined("DEBRANOVA_HOST") ? DEBRANOVA_HOST : 'test'),
                    'defaults' => [
                        'controller' => Controller\ContactController::class,
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'contact-photo' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => "/contact-photo/[:id]-[:hash].[:ext]",
                            'defaults' => [
                                'controller' => Controller\ContactController::class,
                                'action'     => 'photo',
                            ],
                        ],
                    ],
                ],
            ],
            'contact'   => [
                'type'          => 'Literal',
                'priority'      => 1000,
                'options'       => [
                    'route'    => '/contact',
                    'defaults' => [
                        'namespace'  => 'contact',
                        'controller' => Controller\ContactController::class,
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [

                    'photo'               => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/photo/[:contactHash].[:ext]',
                            'defaults' => [
                                'action' => 'photo',
                            ],
                        ],
                    ],
                    'opt-in-update'       => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/update/opt-in.html',
                            'defaults' => [
                                'action' => 'opt-in-update',
                            ],
                        ],
                    ],
                    'has-session'         => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/has-session.html',
                            'defaults' => [
                                'action' => 'has-session',
                            ],
                        ],
                    ],
                    'change-password'     => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/edit/password.html',
                            'defaults' => [
                                'action' => 'change-password',
                            ],
                        ],
                    ],
                    'get-address-by-type' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/get-address.html',
                            'defaults' => [
                                'action' => 'get-address-by-type',
                            ],
                        ],
                    ],
                ],
            ],
            'community' => [
                'child_routes' => [
                    'contact' => [
                        'type'          => 'Segment',
                        'priority'      => 1000,
                        'options'       => [
                            'route'    => '/contact',
                            'defaults' => [
                                'namespace'  => 'contact',
                                'controller' => Controller\ContactController::class,
                                'action'     => 'index',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'search'    => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/search.html',
                                    'defaults' => [
                                        'action' => 'search',
                                    ],
                                    'query'    => [
                                        'search' => null,
                                        'page'   => null,
                                    ]
                                ],
                            ],
                            'signature' => [
                                'type'    => 'Literal',
                                'options' => [
                                    'route'    => '/signature.html',
                                    'defaults' => [
                                        'action' => 'signature',
                                    ],
                                ],
                            ],
                            'facebook'  => [
                                'type'         => 'Segment',
                                'options'      => [
                                    'route'    => '/facebook',
                                    'defaults' => [
                                        'action'     => 'facebook',
                                        'controller' => Controller\FacebookController::class,
                                    ],
                                ],
                                'child_routes' => [
                                    'facebook'     => [
                                        'type'     => 'Segment',
                                        'priority' => 1000,
                                        'options'  => [
                                            'route'    => '/[:id].html',
                                            'defaults' => [
                                                'action' => 'facebook',
                                            ],
                                        ],
                                    ],
                                    'send-message' => [
                                        'type'     => 'Segment',
                                        'priority' => 1000,
                                        'options'  => [
                                            'route'    => '/send-message/[:id].html',
                                            'defaults' => [
                                                'action' => 'send-message',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'profile'   => [
                                'type'         => 'Segment',
                                'options'      => [
                                    'route'    => '/profile',
                                    'defaults' => [
                                        'action'     => 'profile',
                                        'controller' => Controller\ProfileController::class,
                                    ],
                                ],
                                'child_routes' => [
                                    'edit'    => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/edit.html',
                                            'defaults' => [
                                                'action' => 'edit',
                                            ],
                                        ],
                                    ],
                                    'view'    => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/view.html',
                                            'defaults' => [
                                                'action' => 'view',
                                            ],
                                        ],
                                    ],
                                    'contact' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/contact/[:id]-[:hash].html',
                                            'defaults' => [
                                                'action' => 'contact',
                                            ],
                                        ],
                                    ],
                                ],
                            ],

                        ]
                    ]
                ]
            ],
            'zfcadmin'  => [
                'type'          => 'Literal',
                'priority'      => 1000,
                'options'       => [
                    'route'    => '/admin',
                    'defaults' => [
                        'controller' => 'admin',
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'contact-manager'   => [
                        'type'          => 'Segment',
                        'options'       => [
                            'route'    => '/contact',
                            'defaults' => [
                                'controller' => Controller\ContactManagerController::class,
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
                ],
            ],
        ]
    ]
];
