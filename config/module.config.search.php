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
                        'host'     => 'search',
                        'port'     => '8983',
                        'path'     => '/solr/contact_contact'
                    ],
                ],
            ],
            'contact_profile' => [
                'endpoint' => [
                    'server' => [
                        'host'     => 'search',
                        'port'     => '8983',
                        'path'     => '/solr/contact_profile'
                    ],
                ],
            ],
        ],
    ],
];
