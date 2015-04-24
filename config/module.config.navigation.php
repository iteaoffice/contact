<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
return [
    'navigation' => [
        'community' => [
            // And finally, here is where we define our page hierarchy
            'contact' => [
                'order' => 60,
                'label' => _("txt-contacts"),
                'route' => 'community/contact',
            ],
            'project' => [
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
                'route'    => 'zfcadmin/contact-manager',
                'resource' => 'zfcadmin',
                'pages'    => [
                    'contacts'   => [
                        'label' => _("txt-contacts"),
                        'route' => 'zfcadmin/contact-manager/list',
                    ],
                    'selections' => [
                        'label' => _("txt-selections"),
                        'route' => 'zfcadmin/selection-manager/list',
                    ],
                    'facebook'   => [
                        'label' => _("txt-facebook"),
                        'route' => 'zfcadmin/facebook-manager/list',
                    ],
                    'permit'     => [
                        'label' => _("txt-permissions"),
                        'route' => 'zfcadmin/permit-manager/entity/list',
                    ],
                    'import'     => [
                        'label' => _("txt-import"),
                        'route' => 'zfcadmin/contact-manager/import',
                    ],
                    'access'     => [
                        'label' => _("txt-access"),
                        'route' => 'zfcadmin/access-manager/list',
                    ],
                    'statistics' => [
                        'label' => _("txt-contact-statistics"),
                        'route' => 'zfcadmin/contact-manager/statistics',
                    ],
                ],
            ],
        ],
    ],
];
