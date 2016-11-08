<?php

/**
 * Jield copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2004-2015 Jield (http://jield.nl)
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
