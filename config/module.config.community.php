<?php
/**
 * Project Configuration
 *
 * If you have a ./config/autoload/ directory set up for your project, you can
 * drop this config file in it and change the values as you wish.
 */
$settings = array(
    /**
     * Indicate here if a project has versions
     */
    'community_via_members'               => (DEBRANOVA_HOST === 'artemisia'),
    'community_via_project_participation' => true,

);

/**
 * You do not need to edit below this line
 */
return array(
    'community' => $settings,
);