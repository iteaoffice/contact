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
            'image'     => [
                'child_routes' => [
                    'contact-photo' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/c/[:id]-[:last-update].[:ext]',
                            'defaults' => [
                                'controller' => Controller\ImageController::class,
                                'action'     => 'contact-photo',
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
                                'controller' => Controller\ContactController::class,
                                'action'     => 'index',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'search'              => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/search[/page-:page].html',
                                    'defaults' => [
                                        'action' => 'search',
                                    ],
                                ],
                            ],
                            'facebook'            => [
                                'type'         => 'Segment',
                                'options'      => [
                                    'route'    => '/facebook',
                                    'defaults' => [
                                        'action'     => 'facebook',
                                        'controller' => Controller\FacebookController::class,
                                    ],
                                ],
                                'child_routes' => [
                                    'view'         => [
                                        'type'     => 'Segment',
                                        'priority' => 1000,
                                        'options'  => [
                                            'route'    => '/facebook-[:facebook].html',
                                            //No id, because it gives issues with the dynamic menu
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
                            'profile'             => [
                                'type'         => 'Segment',
                                'options'      => [
                                    'route'    => '/profile',
                                    'defaults' => [
                                        'action'     => 'profile',
                                        'controller' => Controller\ProfileController::class,
                                    ],
                                ],
                                'child_routes' => [
                                    'edit'         => [
                                        'type'    => 'Literal',
                                        'options' => [
                                            'route'    => '/edit.html',
                                            'defaults' => [
                                                'action' => 'edit',
                                            ],
                                        ],
                                    ],
                                    'view'         => [
                                        'type'    => 'Literal',
                                        'options' => [
                                            'route'    => '/view.html',
                                            'defaults' => [
                                                'action' => 'view',
                                            ],
                                        ],
                                    ],
                                    'organisation' => [
                                        'type'    => 'Literal',
                                        'options' => [
                                            'route'    => '/organisation.html',
                                            'defaults' => [
                                                'action' => 'organisation',
                                            ],
                                        ],
                                    ],
                                    'events'       => [
                                        'type'    => 'Literal',
                                        'options' => [
                                            'route'    => '/events.html',
                                            'defaults' => [
                                                'action' => 'events',
                                            ],
                                        ],
                                    ],
                                    'privacy'      => [
                                        'type'    => 'Literal',
                                        'options' => [
                                            'route'    => '/privacy.html',
                                            'defaults' => [
                                                'action' => 'privacy',
                                            ],
                                        ],
                                    ],
                                    'contact'      => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/contact/[:hash].html',
                                            'defaults' => [
                                                'action' => 'contact',
                                            ],
                                        ],
                                    ],
                                    'create'       => [
                                        'type'    => 'Literal',
                                        'options' => [
                                            'route'    => '/create.html',
                                            'defaults' => [
                                                'action' => 'create',
                                            ],
                                        ],
                                    ],
                                    'activate'     => [
                                        'type'    => 'Literal',
                                        'options' => [
                                            'route'    => '/activate.html',
                                            'defaults' => [
                                                'action' => 'activate',
                                            ],
                                        ],
                                    ],
                                    'activate-optin'     => [
                                        'type'    => 'Literal',
                                        'options' => [
                                            'route'    => '/activate/optin.html',
                                            'defaults' => [
                                                'action' => 'activate-optin',
                                            ],
                                        ],
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
                ],
            ],
        ],
    ],
];
