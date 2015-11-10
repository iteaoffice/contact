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
                'uri'   => '#',
            ],
            'idea'    => [
                'pages' => [
                    'partner-search' => [
                        'order' => 60,
                        'label' => _("txt-partner-search"),
                        'route' => 'community/contact/search'
                    ]
                ],
            ],
        ],
        'admin'     => [
            // And finally, here is where we define our page hierarchy
            'contact' => [
                'label'    => _("txt-contact-admin"),
                'route'    => 'zfcadmin/contact-admin/list',
                'resource' => 'zfcadmin',
                'pages'    => [
                    'contacts'   => [
                        'label' => _("txt-contacts"),
                        'route' => 'zfcadmin/contact-admin/list',
                        'pages' => [
                            'view-contact'        => [
                                'label'   => _('txt-view'),
                                'route'   => 'zfcadmin/contact-admin/view',
                                'visible' => false,
                            ],
                            'edit-contact'        => [
                                'label'   => _('txt-edit'),
                                'route'   => 'zfcadmin/contact-admin/edit',
                                'visible' => false,
                            ],
                            'impersonate-contact' => [
                                'label'   => _('txt-impersonate'),
                                'route'   => 'zfcadmin/contact-admin/impersonate',
                                'visible' => false,
                            ],
                        ]
                    ],
                    'selections' => [
                        'label' => _("txt-selections"),
                        'route' => 'zfcadmin/selection-manager/list',
                    ],
                    'facebook'   => [
                        'label' => _("txt-facebook"),
                        'route' => 'zfcadmin/facebook-manager/list',
                        'pages' => [
                            'view-facebook' => [
                                'label'   => _('txt-view'),
                                'route'   => 'zfcadmin/facebook-manager/view',
                                'visible' => false,
                            ],
                            'edit-facebook' => [
                                'label'   => _('txt-edit'),
                                'route'   => 'zfcadmin/facebook-manager/view',
                                'visible' => false,
                            ],
                        ]
                    ],
                    'permit'     => [
                        'label' => _("txt-permissions"),
                        'route' => 'zfcadmin/permit-manager/entity/list',
                    ],
                    'import'     => [
                        'label' => _("txt-import"),
                        'route' => 'zfcadmin/contact-admin/import',
                    ],
                    'access'     => [
                        'label' => _("txt-access"),
                        'route' => 'zfcadmin/access-manager/list',
                    ],
                    'statistics' => [
                        'label' => _("txt-contact-statistics"),
                        'route' => 'zfcadmin/contact-admin/statistics',
                    ],
                ],
            ],
        ],
    ],
];
