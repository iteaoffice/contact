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
        'admin' => [
            // And finally, here is where we define our page hierarchy
            'contact' => [
                'label'    => _("txt-contact-admin"),
                'route'    => 'zfcadmin/contact-manager',
                'resource' => 'zfcadmin',
                'pages'    => [
                    'contacts' => [
                        'label' => _("txt-contacts"),
                        'route' => 'zfcadmin/contact-manager',
                    ],
                    'permit'   => [
                        'label' => _("txt-permissions"),
                        'route' => 'zfcadmin/permit-manager/entity/list',
                    ],
                    'access'   => [
                        'label' => _("txt-access"),
                        'route' => 'zfcadmin/access-manager/list',
                    ],
                ],
            ],
        ],
    ],
];
