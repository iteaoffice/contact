<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
return array(
    'router' => array(
        'routes' => array(
            'contact_shortcut' => array(
                'type'     => 'Segment',
                'priority' => -1000,
                'options'  => array(
                    'route'       => 'c/:id',
                    'constraints' => array(
                        'id' => '\d+',
                    ),
                    'defaults'    => array(
                        'controller' => 'contact',
                        'action'     => 'contactRedirect',
                    ),
                ),
            ),
            'assets'           => array(
                'type'          => 'Literal',
                'priority'      => 1000,
                'options'       => array(
                    'route'    => '/assets/' . DEBRANOVA_HOST,
                    'defaults' => array(
                        'controller' => 'contact-index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes'  => array(
                    'contact-photo' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => "/contact-photo/[:hash].[:ext]",
                            'defaults' => array(
                                'controller' => 'contact-index',
                                'action'     => 'display',
                            ),
                        ),
                    ),
                ),
            ),
            'contact'          => array(
                'type'          => 'Literal',
                'priority'      => 1000,
                'options'       => array(
                    'route'    => '/contact',
                    'defaults' => array(
                        'controller' => 'contact-index',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes'  => array(
                    'search'          => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/search',
                            'defaults' => array(
                                'action' => 'search',
                            ),
                        ),
                    ),
                    'signature'       => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/signature.html',
                            'defaults' => array(
                                'action' => 'signature',
                            ),
                        ),
                    ),
                    'photo'           => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/photo/[:contactHash].[:ext]',
                            'defaults' => array(
                                'action' => 'photo',
                            ),
                        ),
                    ),
                    'profile'         => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/profile.html',
                            'defaults' => array(
                                'action' => 'profile',
                            ),
                        ),
                    ),
                    'profile-edit'    => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/edit/profile.html',
                            'defaults' => array(
                                'action' => 'profile-edit',
                            ),
                        ),
                    ),
                    'opt-in-update'   => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/update/opt-in.html',
                            'defaults' => array(
                                'action' => 'opt-in-update',
                            ),
                        ),
                    ),
                    'change-password' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/edit/password.html',
                            'defaults' => array(
                                'action' => 'change-password',
                            ),
                        ),
                    ),
                ),
            ),
            'zfcadmin'         => array(
                'type'          => 'Literal',
                'priority'      => 1000,
                'options'       => array(
                    'route'    => '/admin',
                    'defaults' => array(
                        'controller' => 'admin',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes'  => array(
                    'contact-manager' => array(
                        'type'          => 'Segment',
                        'options'       => array(
                            'route'    => '/contact',
                            'defaults' => array(
                                'controller' => 'contact-manager',
                                'action'     => 'list',
                                'page'       => 1,
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes'  => array(
                            'list'        => array(
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => array(
                                    'route'    => '/list[/:page].html',
                                    'defaults' => array(
                                        'action' => 'list',
                                    ),
                                ),
                            ),
                            'view'        => array(
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => array(
                                    'route'    => '/view/[:id].html',
                                    'defaults' => array(
                                        'action' => 'view',
                                    ),
                                ),
                            ),
                            'impersonate' => array(
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => array(
                                    'route'    => '/impersonate/[:id].html',
                                    'defaults' => array(
                                        'action' => 'impersonate',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'access-manager'  => array(
                        'type'          => 'Segment',
                        'options'       => array(
                            'route'    => '/access',
                            'defaults' => array(
                                'controller' => 'access-manager',
                                'action'     => 'list'
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes'  => array(
                            'list' => array(
                                'type'     => 'Literal',
                                'priority' => 1000,
                                'options'  => array(
                                    'route'    => '/list.html',
                                    'defaults' => array(
                                        'action' => 'list',
                                    ),
                                ),
                            ),

                            'view' => array(
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => array(
                                    'route'    => '/view/[:id].html',
                                    'defaults' => array(
                                        'action' => 'view',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        )
    )
);
