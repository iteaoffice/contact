<?php
/**
 * ITEA Office all rights reserved
 *
 * @category  Application
 *
 * @author    Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */
return [
    'solr' => [
        'connection' => [
            'contact_contact' => [
                'endpoint' => [
                    'server' => [
                        'host'     => '10.213.157.15',
                        'port'     => '8983',
                        'path'     => '/solr/contact_contact',
                        'username' => 'jvdheide',
                        'password' => 'jvdheide1',
                    ],
                ],
            ],
            'contact_profile' => [
                'endpoint' => [
                    'server' => [
                        'host'     => '10.213.157.15',
                        'port'     => '8983',
                        'path'     => '/solr/contact_profile',
                        'username' => 'jvdheide',
                        'password' => 'jvdheide1',
                    ],
                ],
            ],
        ],
    ],
];
