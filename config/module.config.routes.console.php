<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

use Contact\Controller\ConsoleController;

return [
    'console' => [
        'router' => [
            'routes' => [
                'cli-contact-reset-access' => [
                    'options' => [
                        'route'    => 'contact reset-access',
                        'defaults' => [
                            'controller' => ConsoleController::class,
                            'action'     => 'reset-access-roles',
                        ],
                    ],
                ],
                'cli-contact-cleanup'      => [
                    'options' => [
                        'route'    => 'contact cleanup',
                        'defaults' => [
                            'controller' => ConsoleController::class,
                            'action'     => 'contact-cleanup',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
