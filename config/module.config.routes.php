<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Contact
 * @package     Config
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
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
                    'route'    => '/assets',
                    'defaults' => array(
                        'controller' => 'index',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes'  => array(
                    'contact-photo' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => "/contact-photo/[:hash].[:ext]",
                            'defaults' => array(
                                'action' => 'display',
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
                        'controller' => 'contact',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes'  => array(
                    'photo'                   => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/photo/[:contactHash].[:ext]',
                            'defaults' => array(
                                'action' => 'photo',
                            ),
                        ),
                    ),
                    'profile'                 => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/profile.html',
                            'defaults' => array(
                                'action' => 'profile',
                            ),
                        ),
                    ),
                    'profile-edit'            => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/edit/profile.html',
                            'defaults' => array(
                                'action' => 'profile-edit',
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
        )
    )
);
