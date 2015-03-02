<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c] 2004-2014 ITEA Office (http://itea3.org]
 */
return [
    'router' => [
        'routes' => [
            'contact_shortcut' => [
                'type' => 'Segment',
                'priority' => -1000,
                'options' => [
                    'route' => 'c/:id',
                    'constraints' => [
                        'id' => '\d+',
                    ],
                    'defaults' => [
                        'controller' => 'contact',
                        'action' => 'contactRedirect',
                    ],
                ],
            ],
            'assets' => [
                'type' => 'Literal',
                'priority' => 1000,
                'options' => [
                    'route' => '/assets/' . (defined("DEBRANOVA_HOST") ? DEBRANOVA_HOST : 'test'),
                    'defaults' => [
                        'controller' => 'contact-index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'contact-photo' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => "/contact-photo/[:id]-[:hash].[:ext]",
                            'defaults' => [
                                'controller' => 'contact-index',
                                'action' => 'photo',
                            ],
                        ],
                    ],
                ],
            ],
            'contact' => [
                'type' => 'Literal',
                'priority' => 1000,
                'options' => [
                    'route' => '/contact',
                    'defaults' => [
                        'namespace' => 'contact',
                        'controller' => 'contact-index',
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'signature' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/signature.html',
                            'defaults' => [
                                'action' => 'signature',
                            ],
                        ],
                    ],
                    'photo' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/photo/[:contactHash].[:ext]',
                            'defaults' => [
                                'action' => 'photo',
                            ],
                        ],
                    ],
                    'profile' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/profile.html',
                            'defaults' => [
                                'action' => 'profile',
                            ],
                        ],
                    ],
                    'profile-edit' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/edit/profile.html',
                            'defaults' => [
                                'action' => 'profile-edit',
                            ],
                        ],
                    ],
                    'opt-in-update' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/update/opt-in.html',
                            'defaults' => [
                                'action' => 'opt-in-update',
                            ],
                        ],
                    ],
                    'has-session' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/has-session.html',
                            'defaults' => [
                                'action' => 'has-session',
                            ],
                        ],
                    ],
                    'change-password' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/edit/password.html',
                            'defaults' => [
                                'action' => 'change-password',
                            ],
                        ],
                    ],
                    'get-address-by-type' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/get-address.html',
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
                        'type' => 'Segment',
                        'priority' => 1000,
                        'options' => [
                            'route' => '/contact',
                            'defaults' => [
                                'namespace' => 'contact',
                                'controller' => 'contact-index',
                                'action' => 'index',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'facebook' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/facebook',
                                    'defaults' => [
                                        'action' => 'facebook',
                                        'controller' => 'contact-facebook',
                                    ],
                                ],
                                'child_routes' => [
                                    'facebook' => [
                                        'type' => 'Segment',
                                        'priority' => 1000,
                                        'options' => [
                                            'route' => '/[:id].html',
                                            'defaults' => [
                                                'namespace' => 'contact',
                                                'action' => 'facebook',
                                            ],
                                        ],
                                    ],
                                    'send-message' => [
                                        'type' => 'Segment',
                                        'priority' => 1000,
                                        'options' => [
                                            'route' => '/send-message/[:id].html',
                                            'defaults' => [
                                                'namespace' => 'contact',
                                                'action' => 'send-message',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'profile-edit' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/edit/profile.html',
                                    'defaults' => [
                                        'action' => 'profile-edit',
                                    ],
                                ],
                            ],
                            'profile' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/profile.html',
                                    'defaults' => [
                                        'action' => 'profile',
                                    ],
                                ],
                            ],
                        ]
                    ]
                ]
            ],
            'zfcadmin' => [
                'type' => 'Literal',
                'priority' => 1000,
                'options' => [
                    'route' => '/admin',
                    'defaults' => [
                        'controller' => 'admin',
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'contact-manager' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/contact',
                            'defaults' => [
                                'controller' => 'contact-manager',
                                'action' => 'list',
                                'page' => 1,
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'list' => [
                                'type' => 'Segment',
                                'priority' => 1000,
                                'options' => [
                                    'route' => '/list.html',
                                    'defaults' => [
                                        'action' => 'list',
                                    ],
                                ],
                                'query' => [
                                    'search' => null,
                                    'page' => null,
                                ]
                            ],
                            'view' => [
                                'type' => 'Segment',
                                'priority' => 1000,
                                'options' => [
                                    'route' => '/view/[:id].html',
                                    'defaults' => [
                                        'action' => 'view',
                                    ],
                                ],
                            ],
                            'impersonate' => [
                                'type' => 'Segment',
                                'priority' => 1000,
                                'options' => [
                                    'route' => '/impersonate/[:id].html',
                                    'defaults' => [
                                        'action' => 'impersonate',
                                    ],
                                ],
                            ],
                            'edit' => [
                                'type' => 'Segment',
                                'priority' => 1000,
                                'options' => [
                                    'route' => '/edit/[:id].html',
                                    'defaults' => [
                                        'action' => 'edit',
                                    ],
                                ],
                            ],
                            'permit' => [
                                'type' => 'Segment',
                                'priority' => 1000,
                                'options' => [
                                    'route' => '/permissions/[:id].html',
                                    'defaults' => [
                                        'action' => 'permit',
                                    ],
                                ],
                            ],
                            'search' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/search.html',
                                    'defaults' => [
                                        'action' => 'search',
                                    ],
                                ],
                            ],
                            'statistics' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/statistics.html',
                                    'defaults' => [
                                        'action' => 'statistics',
                                    ],
                                ],
                            ],
                            'import' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/import.html',
                                    'defaults' => [
                                        'action' => 'import',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'selection-manager' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/selection',
                            'defaults' => [
                                'controller' => 'contact-selection',
                                'action' => 'list',
                                'page' => 1,
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'list' => [
                                'type' => 'Segment',
                                'priority' => 1000,
                                'options' => [
                                    'route' => '/list.html',
                                    'defaults' => [
                                        'action' => 'list',
                                    ],
                                ],
                                'query' => [
                                    'search' => null,
                                    'page' => null,
                                ]
                            ],
                            'new' => [
                                'type' => 'Literal',
                                'priority' => 1000,
                                'options' => [
                                    'route' => '/new.html',
                                    'defaults' => [
                                        'action' => 'new',
                                    ],
                                ],
                            ],
                            'view' => [
                                'type' => 'Segment',
                                'priority' => 1000,
                                'options' => [
                                    'route' => '/view/[:id].html',
                                    'defaults' => [
                                        'action' => 'view',
                                    ],
                                ],
                            ],
                            'edit' => [
                                'type' => 'Segment',
                                'priority' => 1000,
                                'options' => [
                                    'route' => '/edit/[:id].html',
                                    'defaults' => [
                                        'action' => 'edit',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'facebook-manager' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/facebook',
                            'defaults' => [
                                'controller' => 'contact-facebook-manager',
                                'action' => 'list',
                                'page' => 1,
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'list' => [
                                'type' => 'Segment',
                                'priority' => 1000,
                                'options' => [
                                    'route' => '/list.html',
                                    'defaults' => [
                                        'action' => 'list',
                                    ],
                                ]
                            ],
                            'new' => [
                                'type' => 'Literal',
                                'priority' => 1000,
                                'options' => [
                                    'route' => '/new.html',
                                    'defaults' => [
                                        'action' => 'new',
                                    ],
                                ],
                            ],
                            'view' => [
                                'type' => 'Segment',
                                'priority' => 1000,
                                'options' => [
                                    'route' => '/view/[:id].html',
                                    'defaults' => [
                                        'action' => 'view',
                                    ],
                                ],
                            ],
                            'edit' => [
                                'type' => 'Segment',
                                'priority' => 1000,
                                'options' => [
                                    'route' => '/edit/[:id].html',
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
