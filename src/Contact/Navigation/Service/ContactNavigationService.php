<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     Navigation
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Contact\Navigation\Service;

use Contact\Entity\Contact;

/**
 * Factory for the Community admin navigation
 *
 * @package    Application
 * @subpackage Navigation\Service
 */
class ContactNavigationService extends NavigationServiceAbstract
{
    /**
     * @var Contact
     */
    protected $contact;

    /**
     * Add the dedicated pages to the navigation
     */
    public function update()
    {
        if (!is_null($this->getRouteMatch()) &&
            strtolower($this->getRouteMatch()->getParam('namespace')) === 'contact'
        ) {
            if (strpos($this->getRouteMatch()->getMatchedRouteName(), 'zfcadmin') !== false) {
                $this->updateAdminNavigation();
            }
            $this->updatePublicNavigation();
        }

        /**
         * Add a route for the facebook
         */
        if (strpos($this->getRouteMatch()->getMatchedRouteName(), 'community') !== false) {
            $this->updateCommunityNavigation();
        }
    }

    /**
     * Update the navigation for a publication
     */
    public function updateCommunityNavigation()
    {
        $communityNavigation = $this->getNavigation()->findOneBy('route', 'community/contact');

        if (is_null($communityNavigation)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "The route with id %s cannot be found",
                    'community/contact'
                )
            );
        }

        /**
         * Update the navigation with the categories
         */
        foreach ($this->getContactService()->findFacebookByContact(
            $this->getContact()
        ) as $facebook) {
            $page = [
                'label'  => $facebook->getFacebook(),
                'route'  => 'community/contact/facebook/facebook',
                'active' => strtolower($this->getRouteMatch()->getParam('namespace')) === 'contact' &&
                    intval($this->getRouteMatch()->getParam('id')) === $facebook->getId(),
                'router' => $this->getRouter(),
                'params' => [
                    'id' => $facebook->getId(),
                ],
            ];

            $communityNavigation->addPage($page);
        }
    }

    /**
     *
     */
    protected function updateAdminNavigation()
    {
        $adminNavigation = $this->getNavigation()->findOneBy('route', 'zfcadmin');
        $this->getContactService()->setContactId($this->getRouteMatch()->getParam('id'));
        switch ($this->getRouteMatch()->getMatchedRouteName()) {
            case 'zfcadmin/contact-manager/view':
                $adminNavigation->addPage(
                    [
                        'label'  => sprintf(
                            $this->translate("txt-manage-contact-%s"),
                            $this->getContactService()->parseFullName()
                        ),
                        'route'  => 'zfcadmin/contact-manager/view',
                        'router' => $this->getRouter(),
                    ]
                );
                break;
            case 'zfcadmin/contact-manager/edit':
                $adminNavigation->addPage(
                    [
                        'label'  => sprintf(
                            $this->translate("txt-manage-contact-%s"),
                            $this->getContactService()->parseFullName()
                        ),
                        'route'  => 'zfcadmin/contact-manager/view',
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
                    ]
                );
                break;
            case 'zfcadmin/contact-manager/impersonate':
                $adminNavigation->addPage(
                    [
                        'label'  => sprintf(
                            $this->translate("txt-manage-contact-%s"),
                            $this->getContactService()->parseFullName()
                        ),
                        'route'  => 'zfcadmin/contact-manager/view',
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
                    ]
                );
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
            case 'contact/profile':
                $publicNavigation->addPage(
                    [
                        'label'  => $this->translate("txt-home"),
                        'route'  => 'home',
                        'active' => true,
                        'router' => $this->getRouter(),
                        'pages'  => [
                            [
                                'label'  => $this->translate("txt-account-information"),
                                'route'  => 'contact/profile',
                                'active' => true,
                                'router' => $this->getRouter(),
                            ],
                        ],
                    ]
                );
                break;
            case 'contact/profile-edit':
                $publicNavigation->addPage(
                    [
                        'label'  => $this->translate("txt-home"),
                        'route'  => 'home',
                        'active' => true,
                        'router' => $this->getRouter(),
                        'pages'  => [
                            [
                                'label'  => $this->translate("txt-account-information"),
                                'route'  => 'contact/profile',
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
                    ]
                );
                break;
            case 'contact/change-password':
                $publicNavigation->addPage(
                    [
                        'label'  => $this->translate("txt-home"),
                        'route'  => 'home',
                        'active' => true,
                        'router' => $this->getRouter(),
                        'pages'  => [
                            [
                                'label'  => $this->translate("txt-account-information"),
                                'route'  => 'contact/profile',
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
                    ]
                );
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
