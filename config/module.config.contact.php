<?php
/**
 * Project Configuration
 *
 * If you have a ./config/autoload/ directory set up for your project, you can
 * drop this config file in it and change the values as you wish.
 */
$settings = array(
    // cache options have to be compatible with Zend\Cache\StorageFactory::factory
    'cache_options' => array(
        'adapter' => array(
            'name' => 'filesystem',
        ),
        'plugins' => array(
            'serializer',
        )
    ),
    'cache_key'     => 'contact-cache-' . DEBRANOVA_HOST
);

/**
 * You do not need to edit below this line
 */
return array(
    'contact-config' => $settings,
);
