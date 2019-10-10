<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

namespace Contact;

use Contact\Entity\Address;
use Contact\Entity\Contact;
use Contact\Entity\Dnd;
use Contact\Entity\Facebook;
use Contact\Entity\Note;
use Contact\Entity\Office\Contact as OfficeContact;
use Contact\Entity\OptIn;
use Contact\Entity\Phone;
use Contact\Entity\Selection;
use Contact\Navigation;

return [
    'navigation' => [
        'community' => [
            // And finally, here is where we define our page hierarchy
            'contact' => [
                'order' => 60,
                'label' => _('txt-contacts'),
                'id'    => 'community/contact',
                'route' => 'community/contact/profile/view',
                'pages' => [
                    'view-profile' => [
                        'label' => _('txt-account-information'),
                        'route' => 'community/contact/profile/view',
                        'pages' => [
                            'organisation'                => [
                                'label' => _('txt-profile-organisation'),
                                'route' => 'community/contact/profile/organisation',
                            ],
                            'events'                      => [
                                'label' => _('txt-profile-events'),
                                'route' => 'community/contact/profile/events',
                            ],
                            'privacy'                     => [
                                'label' => _('txt-profile-privacy'),
                                'route' => 'community/contact/profile/privacy',
                            ],
                            'edit'                        => [
                                'label' => _('txt-profile-edit'),
                                'route' => 'community/contact/profile/edit',
                            ],
                            'change-password'             => [
                                'label' => _('txt-change-password'),
                                'route' => 'community/contact/change-password',
                            ],
                            'community/mailing/subscribe' => [
                                'label' => _('txt-subscribe'),
                                'route' => 'community/mailing/subscribe',
                            ],
                            'manage-subscriptions'        => [
                                'label' => _('txt-manage-subscriptions'),
                                'route' => 'community/mailing/manage-subscriptions',
                            ],

                        ],
                    ],
                ],
            ],
            'idea'    => [
                'pages' => [
                    'partner-search' => [
                        'order' => 60,
                        'label' => _('txt-partner-search'),
                        'route' => 'community/contact/search',
                        'pages' => [
                            'search-result' => [
                                'label' => _('txt-contact-profile'),
                                'route' => 'community/contact/profile/contact',

                            ],
                        ],

                    ],
                ],
            ],
        ],
        'admin'     => [
            'contact' => [
                'label'    => _('txt-nav-contact'),
                'route'    => 'zfcadmin/contact/list',
                'order'    => 10,
                'resource' => 'zfcadmin',
                'pages'    => [
                    'contacts'       => [
                        'label' => _('txt-nav-contact-list'),
                        'order' => 10,
                        'route' => 'zfcadmin/contact/list',
                        'pages' => [
                            'view-contact' => [
                                'route'   => 'zfcadmin/contact/view/general',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => Contact::class,
                                    ],
                                    'invokables' => [
                                        Navigation\Invokable\ContactLabel::class,
                                    ],
                                ],
                                'pages'   => [
                                    'notes'               => [
                                        'label'   => _('txt-notes'),
                                        'route'   => 'zfcadmin/contact/view/notes',
                                        'visible' => false,
                                        'params'  => [
                                            'entities'   => [
                                                'id' => Contact::class,
                                            ],
                                            'invokables' => [
                                                Navigation\Invokable\ContactLabel::class,
                                            ],
                                        ],
                                        'pages'   => [
                                            'edit-note' => [
                                                'label'   => _('txt-edit-note'),
                                                'route'   => 'zfcadmin/note/edit',
                                                'visible' => false,
                                                'params'  => [
                                                    'entities'   => [
                                                        'id' => Note::class,
                                                    ],
                                                    'invokables' => [
                                                        Navigation\Invokable\NoteLabel::class,
                                                    ],
                                                ],
                                            ],
                                            'new-note'  => [
                                                'label'   => _('txt-new-note'),
                                                'route'   => 'zfcadmin/note/new',
                                                'visible' => false,
                                                'params'  => [
                                                    'entities'   => [
                                                        'id' => Contact::class,
                                                    ],
                                                    'routeParam' => [
                                                        'id' => 'contact',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                    'address'             => [
                                        'label'   => _('txt-addresses'),
                                        'route'   => 'zfcadmin/contact/view/address',
                                        'visible' => false,
                                        'params'  => [
                                            'entities'   => [
                                                'id' => Contact::class,
                                            ],
                                            'invokables' => [
                                                Navigation\Invokable\ContactLabel::class,
                                            ],
                                        ],
                                        'pages'   => [
                                            'edit-address' => [
                                                'label'   => _('txt-edit-address'),
                                                'route'   => 'zfcadmin/address/edit',
                                                'visible' => false,
                                                'params'  => [
                                                    'entities'   => [
                                                        'id' => Address::class,
                                                    ],
                                                    'invokables' => [
                                                        Navigation\Invokable\AddressLabel::class,
                                                    ],
                                                ],
                                            ],
                                            'new-address'  => [
                                                'label'   => _('txt-new-address'),
                                                'route'   => 'zfcadmin/address/new',
                                                'visible' => false,
                                                'params'  => [
                                                    'entities'   => [
                                                        'id' => Contact::class,
                                                    ],
                                                    'routeParam' => [
                                                        'id' => 'contact',
                                                    ],
                                                ],
                                            ],
                                        ]
                                    ],
                                    'phone'               => [
                                        'label'   => _('txt-phone-numbers'),
                                        'route'   => 'zfcadmin/contact/view/phone',
                                        'visible' => false,
                                        'params'  => [
                                            'entities'   => [
                                                'id' => Contact::class,
                                            ],
                                            'invokables' => [
                                                Navigation\Invokable\ContactLabel::class,
                                            ],
                                        ],
                                        'pages'   => [
                                            'edit-phone' => [
                                                'label'   => _('txt-edit-phone'),
                                                'route'   => 'zfcadmin/phone/edit',
                                                'visible' => false,
                                                'params'  => [
                                                    'entities'   => [
                                                        'id' => Phone::class,
                                                    ],
                                                    'invokables' => [
                                                        Navigation\Invokable\PhoneLabel::class,
                                                    ],
                                                ],
                                            ],
                                            'new-phone'  => [
                                                'label'   => _('txt-new-phone'),
                                                'route'   => 'zfcadmin/phone/new',
                                                'visible' => false,
                                                'params'  => [
                                                    'entities'   => [
                                                        'id' => Contact::class,
                                                    ],
                                                    'routeParam' => [
                                                        'id' => 'contact',
                                                    ],
                                                ],
                                            ],
                                        ]
                                    ],
                                    'selection'           => [
                                        'label'   => _('txt-selections'),
                                        'route'   => 'zfcadmin/contact/view/selection',
                                        'visible' => false,
                                        'params'  => [
                                            'entities'   => [
                                                'id' => Contact::class,
                                            ],
                                            'invokables' => [
                                                Navigation\Invokable\ContactLabel::class,
                                            ],
                                        ],
                                        'pages'   => [
                                            'add-contact' => [
                                                'label'  => _('txt-add-contact-to-selection'),
                                                'route'  => 'zfcadmin/selection/add-contact',
                                                'params' => [
                                                    'entities'   => [
                                                        'id' => Contact::class,
                                                    ],
                                                    'routeParam' => [
                                                        'id' => 'contactId',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                    'mailing'             => [
                                        'label'   => _('txt-mailing'),
                                        'route'   => 'zfcadmin/contact/view/mailing',
                                        'visible' => false,
                                        'params'  => [
                                            'entities'   => [
                                                'id' => Contact::class,
                                            ],
                                            'invokables' => [
                                                Navigation\Invokable\ContactLabel::class,
                                            ],
                                        ],
                                    ],
                                    'idea'                => [
                                        'label'   => _('txt-project-ideas'),
                                        'route'   => 'zfcadmin/contact/view/idea',
                                        'visible' => false,
                                        'params'  => [
                                            'entities'   => [
                                                'id' => Contact::class,
                                            ],
                                            'invokables' => [
                                                Navigation\Invokable\ContactLabel::class,
                                            ],
                                        ],
                                    ],
                                    'project'             => [
                                        'label'   => _('txt-projects'),
                                        'route'   => 'zfcadmin/contact/view/project',
                                        'visible' => false,
                                        'params'  => [
                                            'entities'   => [
                                                'id' => Contact::class,
                                            ],
                                            'invokables' => [
                                                Navigation\Invokable\ContactLabel::class,
                                            ],
                                        ],
                                        'pages'   => [
                                            'add-project' => [
                                                'label'   => _('txt-add-project'),
                                                'route'   => 'zfcadmin/contact/add-project',
                                                'visible' => false,
                                                'params'  => [
                                                    'entities' => [
                                                        'id' => Contact::class,
                                                    ],
                                                ],
                                            ],
                                        ]
                                    ],
                                    'legal'               => [
                                        'label'   => _('txt-legal'),
                                        'route'   => 'zfcadmin/contact/view/legal',
                                        'visible' => false,
                                        'params'  => [
                                            'entities'   => [
                                                'id' => Contact::class,
                                            ],
                                            'invokables' => [
                                                Navigation\Invokable\ContactLabel::class,
                                            ],
                                        ],
                                        'pages'   => [
                                            'upload-nda' => [
                                                'label'   => _('txt-upload-nda'),
                                                'route'   => 'zfcadmin/nda/upload',
                                                'visible' => false,
                                                'params'  => [
                                                    'entities'   => [
                                                        'id' => Contact::class,
                                                    ],
                                                    'routeParam' => [
                                                        'id' => 'contactId',
                                                    ],
                                                ],
                                            ],

                                            'new-dnd'  => [
                                                'label'   => _('txt-new-dnd'),
                                                'route'   => 'zfcadmin/contact/dnd/new',
                                                'visible' => false,
                                                'params'  => [
                                                    'entities'   => [
                                                        'id' => Contact::class,
                                                    ],
                                                    'routeParam' => [
                                                        'id' => 'contactId',
                                                    ],
                                                ],
                                            ],
                                            'edit-dnd' => [
                                                'label'   => _('txt-edit-dnd'),
                                                'route'   => 'zfcadmin/contact/dnd/edit',
                                                'visible' => false,
                                                'params'  => [
                                                    'entities'   => [
                                                        'id' => Dnd::class,
                                                    ],
                                                    'invokables' => [
                                                        Navigation\Invokable\DndLabel::class,
                                                    ],
                                                ],
                                            ],
                                        ]
                                    ],
                                    'event'               => [
                                        'label'   => _('txt-events'),
                                        'route'   => 'zfcadmin/contact/view/event',
                                        'visible' => false,
                                        'params'  => [
                                            'entities'   => [
                                                'id' => Contact::class,
                                            ],
                                            'invokables' => [
                                                Navigation\Invokable\ContactLabel::class,
                                            ],
                                        ],
                                        'pages'   => [
                                            'add-contact-to-meeting' => [
                                                'label'  => _('txt-add-contact-to-meeting'),
                                                'route'  => 'zfcadmin/registration/add-contact',
                                                'params' => [
                                                    'entities'   => [
                                                        'id' => Contact::class,
                                                    ],
                                                    'routeParam' => [
                                                        'id' => 'contactId',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                    'merge'               => [
                                        'label'   => _('txt-merge'),
                                        'route'   => 'zfcadmin/contact/view/merge',
                                        'visible' => false,
                                        'params'  => [
                                            'entities'   => [
                                                'id' => Contact::class,
                                            ],
                                            'invokables' => [
                                                Navigation\Invokable\ContactLabel::class,
                                            ],
                                        ],
                                        'pages'   => [
                                            'preview' => [
                                                'label'   => _('txt-nav-merge-preview'),
                                                'route'   => 'zfcadmin/contact/merge',
                                                'visible' => false,
                                                'params'  => [
                                                    'entities'   => [
                                                        'id' => Contact::class,
                                                    ],
                                                    'routeParam' => [
                                                        'id' => 'targetId',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                    'edit-contact'        => [
                                        'label'   => _('txt-edit-contact'),
                                        'route'   => 'zfcadmin/contact/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Contact::class,
                                            ],
                                        ],
                                    ],
                                    'permisssions'        => [
                                        'label'   => _('txt-permissions'),
                                        'route'   => 'zfcadmin/contact/permit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Contact::class,
                                            ],
                                        ],
                                    ],
                                    'impersonate-contact' => [
                                        'label'   => _('txt-impersonate'),
                                        'route'   => 'zfcadmin/contact/impersonate',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Contact::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'selections'     => [
                        'label' => _('txt-nav-selection-list'),
                        'order' => 20,
                        'route' => 'zfcadmin/selection/list',
                        'pages' => [
                            'view'          => [
                                'label'   => _('txt-view'),
                                'route'   => 'zfcadmin/selection/view',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => Selection::class,
                                    ],
                                    'invokables' => [
                                        Navigation\Invokable\SelectionLabel::class,
                                    ],
                                ],
                                'pages'   => [
                                    'edit-selection'     => [
                                        'label'   => _('txt-edit-selection'),
                                        'route'   => 'zfcadmin/selection/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Selection::class,
                                            ],
                                        ],
                                    ],
                                    'edit-contacts'      => [
                                        'label'   => _('txt-edit-contacts'),
                                        'route'   => 'zfcadmin/selection/edit-contacts',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Selection::class,
                                            ],
                                        ],
                                    ],
                                    'generate-deeplinks' => [
                                        'label'   => _('txt-generate-deeplinks'),
                                        'route'   => 'zfcadmin/selection/generate-deeplinks',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Selection::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'new-selection' => [
                                'label' => _('txt-new-selection'),
                                'route' => 'zfcadmin/selection/new',
                            ],
                        ],
                    ],
                    'facebook'       => [
                        'label' => _('txt-nav-facebook-list'),
                        'route' => 'zfcadmin/facebook/list',
                        'order' => 40,
                        'pages' => [
                            'view-facebook' => [
                                'label'   => _('txt-view'),
                                'route'   => 'zfcadmin/facebook/view',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => Facebook::class,
                                    ],
                                    'invokables' => [
                                        Navigation\Invokable\FacebookLabel::class,
                                    ],
                                ],
                                'pages'   => [
                                    'edit-facebook' => [
                                        'label'   => _('txt-edit-facebook'),
                                        'route'   => 'zfcadmin/facebook/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Facebook::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'opt-in'         => [
                        'label' => _('txt-nav-opt-in-list'),
                        'route' => 'zfcadmin/opt-in/list',
                        'order' => 40,
                        'pages' => [
                            'view-opt-in' => [
                                'label'   => _('txt-view'),
                                'route'   => 'zfcadmin/opt-in/view',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => OptIn::class,
                                    ],
                                    'invokables' => [
                                        Navigation\Invokable\OptInLabel::class,
                                    ],
                                ],
                                'pages'   => [
                                    'edit-opt-in' => [
                                        'label'   => _('txt-edit-opt-in'),
                                        'route'   => 'zfcadmin/opt-in/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => OptIn::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'new-opt-in'  => [
                                'label' => _('txt-new-opt-in'),
                                'route' => 'zfcadmin/opt-in/new',
                            ],
                        ],
                    ],
                    'leave'          => [
                        'label' => _('txt-nav-leave'),
                        'route' => 'zfcadmin/contact/office/leave/manage',
                        'order' => 41,
                    ],
                    'office-members' => [
                        'label' => _('txt-nav-office-members'),
                        'route' => 'zfcadmin/contact/office/list',
                        'order' => 42,
                        'pages' => [
                            'view' => [
                                'label'   => _('txt-view'),
                                'route'   => 'zfcadmin/contact/office/view',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => OfficeContact::class,
                                    ],
                                    'invokables' => [
                                        Navigation\Invokable\Office\ContactLabel::class,
                                    ],
                                ],
                                'pages'   => [
                                    'edit' => [
                                        'label'   => _('txt-edit'),
                                        'route'   => 'zfcadmin/contact/office/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => OfficeContact::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'new'  => [
                                'label'   => _('txt-new-office-member'),
                                'route'   => 'zfcadmin/contact/office/new',
                                'visible' => false,
                            ],
                        ],
                    ],
                ],
            ],
            'tools'   => [
                'pages' => [
                    'import'                    => [
                        'label' => _('txt-nav-contact-import'),
                        'order' => 50,
                        'route' => 'zfcadmin/contact/import',
                    ],
                    'list-duplicate-contacts'   => [
                        'label' => _('txt-nav-list-duplicate-contacts'),
                        'order' => 51,
                        'route' => 'zfcadmin/contact/list-duplicate',
                    ], 'list-inactive-contacts' => [
                        'label' => _('txt-nav-list-inactive-contacts'),
                        'order' => 51,
                        'route' => 'zfcadmin/contact/list-inactive',
                    ],
                ],
            ],
        ],
    ],
];
