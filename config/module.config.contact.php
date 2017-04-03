<?php
/**
 * Project Configuration
 *
 * If you have a ./config/autoload/ directory set up for your project, you can
 * drop this config file in it and change the values as you wish.
 */
$settings = [
    // cache options have to be compatible with Zend\Cache\StorageFactory::factory
    'cache_options' => [
        'adapter' => [
            'name' => 'apc',
        ],
        'plugins' => [
            'serializer',
        ],
    ],
    'cache_key'     => 'contact-cache-' . (defined("ITEAOFFICE_HOST") ? ITEAOFFICE_HOST : 'test'),
];
/**
 * You do not need to edit below this line
 */
return [
    'contact-config' => $settings,
];
