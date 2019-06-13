<?php

/**
 * Jield copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
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
