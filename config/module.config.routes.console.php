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
                'cli-partner-search-update' => [
                    'options' => [
                        'route'    => 'partner-search update',
                        'defaults' => [
                            'controller' => ConsoleController::class,
                            'action'     => 'partner-search-update',
                        ],
                    ],
                ],
                'cli-partner-search-reset'  => [
                    'options' => [
                        'route'    => 'partner-search reset',
                        'defaults' => [
                            'controller' => ConsoleController::class,
                            'action'     => 'partner-search-reset',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
