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
            'assets'    => [
                'type'          => 'Literal',
                'priority'      => 1000,
                'options'       => [
                    'route'    => '/assets/' . (defined("ITEAOFFICE_HOST") ? ITEAOFFICE_HOST : 'test'),
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
                            'search'              => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/search[/page-:page].html',
                                    'defaults' => [
                                        'action' => 'search',
                                    ],
                                ],
                            ],
                            'signature'           => [
                                'type'    => 'Literal',
                                'options' => [
                                    'route'    => '/signature.html',
                                    'defaults' => [
                                        'action' => 'signature',
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
                ],
            ],
        ],
    ],
];
