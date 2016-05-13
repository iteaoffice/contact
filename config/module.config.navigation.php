<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */
return [
    'navigation' => [
        'community' => [
            // And finally, here is where we define our page hierarchy
            'contact' => [
                'order' => 60,
                'label' => _("txt-contacts"),
                'id'    => 'community/contact',
                'route' => 'community/contact/profile/view',
                'pages' => [
                    'view-profile' => [
                        'label' => _("txt-account-information"),
                        'route' => 'community/contact/profile/view',
                        'pages' => [
                            'edit-profile'    => [
                                'label' => _("txt-profile-edit"),
                                'route' => 'community/contact/profile/edit',
                            ],
                            'view-signature'  => [
                                'label' => _("txt-view-signature"),
                                'route' => 'community/contact/signature',
                            ],
                            'change-password' => [
                                'label' => _("txt-change-password"),
                                'route' => 'community/contact/change-password',
                            ],
                        ]
                    ],
                ]

            ],
            'idea'    => [
                'pages' => [
                    'partner-search' => [
                        'order' => 60,
                        'label' => _("txt-partner-search"),
                        'route' => 'community/contact/search',
                        'pages' => [
                            'search-result' => [
                                'label' => _("txt-contact-profile"),
                                'route' => 'community/contact/profile/contact',

                            ],
                        ]

                    ]
                ],
            ],
        ],
        'admin'     => [
            // And finally, here is where we define our page hierarchy
            'contact'    => [
                'label'    => _("txt-nav-contact"),
                'route'    => 'zfcadmin/contact-admin/list',
                'order'    => 10,
                'resource' => 'zfcadmin',
                'pages'    => [
                    'contacts'   => [
                        'label' => _("txt-nav-contact-list"),
                        'order' => 10,
                        'route' => 'zfcadmin/contact-admin/list',
                        'pages' => [
                            'view-contact' => [
                                'label'   => _('txt-view'),
                                'route'   => 'zfcadmin/contact-admin/view',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => \Contact\Entity\Contact::class
                                    ],
                                    'invokables' => [
                                        Contact\Navigation\Invokable\ContactLabel::class
                                    ]
                                ],
                                'pages'   => [
                                    'edit-contact'        => [
                                        'label'   => _('txt-edit-contact'),
                                        'route'   => 'zfcadmin/contact-admin/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => \Contact\Entity\Contact::class
                                            ],
                                        ],
                                    ],
                                    'permisssions'        => [
                                        'label'   => _('txt-permissions'),
                                        'route'   => 'zfcadmin/contact-admin/permit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => \Contact\Entity\Contact::class
                                            ],
                                        ],
                                    ],
                                    'impersonate-contact' => [
                                        'label'   => _('txt-impersonate'),
                                        'route'   => 'zfcadmin/contact-admin/impersonate',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => \Contact\Entity\Contact::class
                                            ],
                                        ],
                                    ],
                                    'edit-address'        => [
                                        'label'   => _('txt-edit-address'),
                                        'route'   => 'zfcadmin/address-manager/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities'   => [
                                                'id' => \Contact\Entity\Address::class
                                            ],
                                            'invokables' => [
                                                Contact\Navigation\Invokable\AddressLabel::class
                                            ]
                                        ],
                                    ],
                                    'new-address'         => [
                                        'label'   => _('txt-new-address'),
                                        'route'   => 'zfcadmin/address-manager/new',
                                        'visible' => false,
                                        'params'  => [
                                            'entities'   => [
                                                'id' => \Contact\Entity\Contact::class
                                            ],
                                            'routeParam' => [
                                                'id' => 'contact'
                                            ]
                                        ],
                                    ],
                                    'edit-phone'          => [
                                        'label'   => _('txt-edit-phone'),
                                        'route'   => 'zfcadmin/phone-manager/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities'   => [
                                                'id' => \Contact\Entity\Phone::class
                                            ],
                                            'invokables' => [
                                                Contact\Navigation\Invokable\PhoneLabel::class
                                            ]
                                        ],
                                    ],
                                    'new-phone'           => [
                                        'label'   => _('txt-new-phone'),
                                        'route'   => 'zfcadmin/phone-manager/new',
                                        'visible' => false,
                                        'params'  => [
                                            'entities'   => [
                                                'id' => \Contact\Entity\Contact::class
                                            ],
                                            'routeParam' => [
                                                'id' => 'contact'
                                            ]
                                        ],
                                    ],
                                ]
                            ],

                        ]
                    ],
                    'selections' => [
                        'label' => _("txt-nav-selection-list"),
                        'order' => 20,
                        'route' => 'zfcadmin/selection-manager/list',
                        'pages' => [
                            'view'          => [
                                'label'   => _('txt-view'),
                                'route'   => 'zfcadmin/selection-manager/view',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => \Contact\Entity\Selection::class
                                    ],
                                    'invokables' => [
                                        Contact\Navigation\Invokable\SelectionLabel::class
                                    ]
                                ],
                                'pages'   => [
                                    'edit-selection' => [
                                        'label'   => _('txt-edit-selection'),
                                        'route'   => 'zfcadmin/selection-manager/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => \Contact\Entity\Selection::class
                                            ],
                                        ],
                                    ],
                                    'edit-contacts'  => [
                                        'label'   => _('txt-edit-contacts'),
                                        'route'   => 'zfcadmin/selection-manager/edit-contacts',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => \Contact\Entity\Selection::class
                                            ],
                                        ],
                                    ],
                                ]
                            ],
                            'edit-contacts' => [
                                'label' => _('txt-new-selection'),
                                'route' => 'zfcadmin/selection-manager/new',
                            ],
                        ]
                    ],
                    'facebook'   => [
                        'label' => _("txt-nav-facebook-list"),
                        'route' => 'zfcadmin/facebook-manager/list',
                        'order' => 40,
                        'pages' => [
                            'view-facebook' => [
                                'label'   => _('txt-view'),
                                'route'   => 'zfcadmin/facebook-manager/view',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => \Contact\Entity\Facebook::class
                                    ],
                                    'invokables' => [
                                        Contact\Navigation\Invokable\FacebookLabel::class
                                    ]
                                ],
                                'pages'   => [
                                    'edit-facebook' => [
                                        'label'   => _('txt-edit-facebook'),
                                        'route'   => 'zfcadmin/facebook-manager/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => \Contact\Entity\Facebook::class
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'import'     => [
                        'label' => _("txt-nav-contact-import"),
                        'order' => 50,
                        'route' => 'zfcadmin/contact-admin/import',
                    ],
                ],
            ],
            'management' => [
                'pages' => [
                    'permit' => [
                        'label' => _("txt-nav-permission-list"),
                        'route' => 'zfcadmin/permit-manager/entity/list',
                    ],

                ]
            ]
        ],
    ],
];
