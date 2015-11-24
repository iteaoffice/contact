<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Contact\Navigation\Service;

use Contact\Entity\Contact;

/**
 * Factory for the Community admin navigation.
 */
class ContactNavigationService extends NavigationServiceAbstract
{
    /**
     * @var Contact
     */
    protected $contact;

    /**
     * Add the dedicated pages to the navigation.
     */
    public function update()
    {
        if (!is_null($this->getRouteMatch())
            && strtolower($this->getRouteMatch()->getParam('namespace')) === 'contact'
        ) {
            if (strpos($this->getRouteMatch()->getMatchedRouteName(), 'zfcadmin') !== false) {
                $this->updateAdminNavigation();
            }
            $this->updatePublicNavigation();
        }

        /*
         * Add a route for the facebook
         */
        if (strpos($this->getRouteMatch()->getMatchedRouteName(), 'community') !== false) {
            $this->includeFacebooksInNavigation();
            $this->updateCommunityNavigation();
        }
    }

    /**
     * @return bool
     */
    public function updateCommunityNavigation()
    {
        switch ($this->getRouteMatch()->getMatchedRouteName()) {
            case 'community/contact/profile/contact':
                /**
                 * We are on a profile-page. Squeeze the profile page in the path
                 */
                $communityNavigation = $this->getNavigation()->findOneBy('route', 'community/contact/search');

                //Find the contact
                $contact = $this->getContactService()
                    ->findEntityById('contact', $this->getRouteMatch()->getParam('id'));

                $communityNavigation->addPage([
                        'label'  => sprintf($this->translate("txt-profile-of-%s"), $contact->getDisplayName()),
                        'route'  => 'community/contact/profile/contact',
                        'active' => true,
                        'router' => $this->getRouter(),
                        'params' => [
                            'id'   => $contact->getId(),
                            'hash' => $contact->parseHash()
                        ]
                    ]);
                break;
        }
    }

    /**
     * Update the navigation for a publication.
     */
    public function includeFacebooksInNavigation()
    {
        $communityNavigation = $this->getNavigation()->findOneBy('id', 'community/contact');

        if (is_null($communityNavigation)) {
            return null;
        }

        /*
         * Update the navigation with the facebooks (if a a contact object is present)
         */
        if (!is_null($this->getContact())) {
            foreach ($this->getContactService()->findFacebookByContact($this->getContact()) as $facebook) {
                $page = [
                    'label'  => $facebook->getFacebook(),
                    'route'  => 'community/contact/facebook/facebook',
                    'active' => strtolower($this->getRouteMatch()->getParam('namespace')) === 'contact'
                        && intval($this->getRouteMatch()->getParam('id')) === $facebook->getId(),
                    'router' => $this->getRouter(),
                    'params' => [
                        'id' => $facebook->getId(),
                    ],
                ];

                $communityNavigation->addPage($page);
            }
        }
    }

    /**
     *
     */
    protected function updateAdminNavigation()
    {
        $adminNavigation = $this->getNavigation()->findOneBy('route', 'zfcadmin');
        if (is_null($this->getRouteMatch()->getParam('id'))) {
            return;
        }
        $this->getContactService()->setContactId($this->getRouteMatch()->getParam('id'));
        switch ($this->getRouteMatch()->getMatchedRouteName()) {
            case 'zfcadmin/contact-admin/view':
                $adminNavigation->addPage([
                    'label'  => sprintf(
                        $this->translate("txt-manage-contact-%s"),
                        $this->getContactService()->parseFullName()
                    ),
                    'route'  => 'zfcadmin/contact-admin/view',
                    'router' => $this->getRouter(),
                ]);
                break;
            case 'zfcadmin/contact-admin/edit':
                $adminNavigation->addPage([
                    'label'  => sprintf(
                        $this->translate("txt-manage-contact-%s"),
                        $this->getContactService()->parseFullName()
                    ),
                    'route'  => 'zfcadmin/contact-admin/view',
                    'router' => $this->getRouter(),
                    'pages'  => [
                        [
                            'label'  => sprintf(
                                $this->translate("txt-edit-contact-%s"),
                                $this->getContactService()->parseFullName()
                            ),
                            'route'  => $this->getRouteMatch()->getMatchedRouteName(),
                            'active' => true,
                            'router' => $this->getRouter(),
                            'params' => [
                                'call-id' => $this->routeMatch->getParam('call-id'),
                            ],
                        ],
                    ],
                ]);
                break;
            case 'zfcadmin/contact-admin/impersonate':
                $adminNavigation->addPage([
                    'label'  => sprintf(
                        $this->translate("txt-manage-contact-%s"),
                        $this->getContactService()->parseFullName()
                    ),
                    'route'  => 'zfcadmin/contact-admin/view',
                    'router' => $this->getRouter(),
                    'pages'  => [
                        [
                            'label'  => sprintf(
                                $this->translate("txt-impersonate-contact-%s"),
                                $this->getContactService()->parseFullName()
                            ),
                            'route'  => $this->getRouteMatch()->getMatchedRouteName(),
                            'active' => true,
                            'router' => $this->getRouter(),
                            'params' => [
                                'call-id' => $this->routeMatch->getParam('call-id'),
                            ],
                        ],
                    ],
                ]);
                break;
        }
    }

    /**
     * @return bool
     */
    protected function updatePublicNavigation()
    {
        $publicNavigation = $this->getNavigation();
        switch ($this->getRouteMatch()->getMatchedRouteName()) {
            case 'community/contact/profile/view':
                $publicNavigation->addPage([
                    'label'  => $this->translate("txt-community"),
                    'route'  => 'community',
                    'active' => true,
                    'router' => $this->getRouter(),
                    'pages'  => [
                        [
                            'label'  => $this->translate("txt-account-information"),
                            'route'  => 'community/contact/profile/view',
                            'active' => true,
                            'router' => $this->getRouter(),
                        ],
                    ],
                ]);
                break;
            case 'community/contact/profile/edit':
                $publicNavigation->addPage([
                    'label'  => $this->translate("txt-community"),
                    'route'  => 'community',
                    'active' => true,
                    'router' => $this->getRouter(),
                    'pages'  => [
                        [
                            'label'  => $this->translate("txt-account-information"),
                            'route'  => 'community/contact/profile/view',
                            'active' => true,
                            'router' => $this->getRouter(),
                            'pages'  => [
                                [
                                    'label'  => $this->translate("txt-profile-edit"),
                                    'route'  => $this->getRouteMatch()->getMatchedRouteName(),
                                    'router' => $this->getRouter(),
                                    'active' => true,
                                ],
                            ],
                        ],
                    ],
                ]);
                break;
            case 'community/contact/signature':
                $publicNavigation->addPage([
                    'label'  => $this->translate("txt-community"),
                    'route'  => 'community',
                    'active' => false,
                    'router' => $this->getRouter(),
                    'pages'  => [
                        [
                            'label'  => $this->translate("txt-account-information"),
                            'route'  => 'community/contact/profile/view',
                            'active' => true,
                            'router' => $this->getRouter(),
                            'pages'  => [
                                [
                                    'label'  => $this->translate("txt-signature"),
                                    'route'  => $this->getRouteMatch()->getMatchedRouteName(),
                                    'router' => $this->getRouter(),
                                    'active' => true,
                                ],
                            ],
                        ],
                    ],
                ]);
                break;

            case 'contact/change-password':
                $publicNavigation->addPage([
                    'label'  => $this->translate("txt-home"),
                    'route'  => 'home',
                    'active' => true,
                    'router' => $this->getRouter(),
                    'pages'  => [
                        [
                            'label'  => $this->translate("txt-account-information"),
                            'route'  => 'community/contact/profile/view',
                            'active' => true,
                            'router' => $this->getRouter(),
                            'pages'  => [
                                [
                                    'label'  => $this->translate("txt-change-password"),
                                    'route'  => $this->getRouteMatch()->getMatchedRouteName(),
                                    'router' => $this->getRouter(),
                                    'active' => true,
                                ],
                            ],
                        ],
                    ],
                ]);
                break;
        }

        return true;
    }

    /**
     * @return Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param Contact $contact
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    }
}
