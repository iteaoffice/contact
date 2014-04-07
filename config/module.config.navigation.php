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
    'navigation' => array(
        'admin' => array(
            // And finally, here is where we define our page hierarchy
            'contact' => array(
                'label'    => _("txt-contact-admin"),
                'route'    => 'zfcadmin',
                'resource' => 'zfcadmin',
                'pages'    => array(
                    'contacts' => array(
                        'label' => "txt-contacts",
                        'route' => 'zfcadmin/contact-manager',
                    ),
                ),
            ),
        ),
    ),
);
