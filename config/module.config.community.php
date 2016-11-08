<?php
/**
 * Project Configuration
 *
 * If you have a ./config/autoload/ directory set up for your project, you can
 * drop this config file in it and change the values as you wish.
 */
if ( ! defined("ITEAOFFICE_HOST")) {
    define('ITEAOFFICE_HOST', 'test');
}
$settings = [
    /**
     * Indicate here if a project has versions
     */
    'community_via_members'               => (ITEAOFFICE_HOST === 'artemisia'),
    'community_via_project_participation' => true,
    'facebook_template'                   => 'contact/facebook/facebook',
];
/**
 * You do not need to edit below this line
 */
return [
    'community' => $settings,
];
