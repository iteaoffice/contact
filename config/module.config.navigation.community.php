<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */
return [
    'navigation' => [
        'community2' => [
            // And finally, here is where we define our page hierarchy
            'contact' => [
                'order' => 60,
                'label' => _("txt-contacts"),
                'id'    => 'community/contact',
                'route' => 'community/contact/profile/view',
                'pages' => [
                    'view-profile' => [
                        'label' => _("txt-account-information"),
                        'route' => 'community/contact/profile/view',
                        'pages' => [
                            'organisation'                => [
                                'label' => _("txt-profile-organisation"),
                                'route' => 'community/contact/profile/organisation',
                            ],
                            'events'                      => [
                                'label' => _("txt-profile-events"),
                                'route' => 'community/contact/profile/events',
                            ],
                            'privacy'                     => [
                                'label' => _("txt-profile-privacy"),
                                'route' => 'community/contact/profile/privacy',
                            ],
                            'edit'                        => [
                                'label' => _("txt-profile-edit"),
                                'route' => 'community/contact/profile/edit',
                            ],
                            'change-password'             => [
                                'label' => _("txt-change-password"),
                                'route' => 'community/contact/change-password',
                            ],
                            'community/mailing/subscribe' => [
                                'label' => _("txt-subscribe"),
                                'route' => 'community/mailing/subscribe',
                            ],
                            'manage-subscriptions'        => [
                                'label' => _("txt-manage-subscriptions"),
                                'route' => 'community/mailing/manage-subscriptions',
                            ],

                        ],
                    ],
                ],
            ],
        ],
    ],
];
