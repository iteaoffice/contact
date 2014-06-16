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
                'type'     => 'Segment',
                'priority' => -1000,
                'options'  => [
                    'route'       => 'c/:id',
                    'constraints' => [
                        'id' => '\d+',
                    ],
                    'defaults'    => [
                        'controller' => 'contact',
                        'action'     => 'contactRedirect',
                    ],
                ],
            ],
            'assets'           => [
                'type'          => 'Literal',
                'priority'      => 1000,
                'options'       => [
                    'route'    => '/assets/' . DEBRANOVA_HOST,
                    'defaults' => [
                        'controller' => 'contact-index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'contact-photo' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => "/contact-photo/[:hash].[:ext]",
                            'defaults' => [
                                'controller' => 'contact-index',
                                'action'     => 'display',
                            ],
                        ],
                    ],
                ],
            ],
            'contact'          => [
                'type'          => 'Literal',
                'priority'      => 1000,
                'options'       => [
                    'route'    => '/contact',
                    'defaults' => [
                        'namespace'  => 'contact',
                        'controller' => 'contact-index',
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'search'          => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/search',
                            'defaults' => [
                                'action' => 'search',
                            ],
                        ],
                    ],
                    'signature'       => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/signature.html',
                            'defaults' => [
                                'action' => 'signature',
                            ],
                        ],
                    ],
                    'photo'           => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/photo/[:contactHash].[:ext]',
                            'defaults' => [
                                'action' => 'photo',
                            ],
                        ],
                    ],
                    'profile'         => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/profile.html',
                            'defaults' => [
                                'action' => 'profile',
                            ],
                        ],
                    ],
                    'profile-edit'    => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/edit/profile.html',
                            'defaults' => [
                                'action' => 'profile-edit',
                            ],
                        ],
                    ],
                    'opt-in-update'   => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/update/opt-in.html',
                            'defaults' => [
                                'action' => 'opt-in-update',
                            ],
                        ],
                    ],
                    'change-password' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/edit/password.html',
                            'defaults' => [
                                'action' => 'change-password',
                            ],
                        ],
                    ],
                ],
            ],
            'zfcadmin'         => [
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
                    'contact-manager' => [
                        'type'          => 'Segment',
                        'options'       => [
                            'route'    => '/contact',
                            'defaults' => [
                                'controller' => 'contact-manager',
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
                                    'route'    => '/list[/:page].html',
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
                        ],
                    ],
                ],
            ],
        ]
    ]
];
