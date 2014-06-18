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

/**
 * Factory for the Community admin navigation
 *
 * @package    Application
 * @subpackage Navigation\Service
 */
class ContactNavigationService extends NavigationServiceAbstract
{
    /**
     * Add the dedicated pages to the navigation
     */
    public function update()
    {
        if (!is_null($this->getRouteMatch()) &&
            strtolower($this->getRouteMatch()->getParam('namespace')) === 'contact'
        ) {
            if (strpos($this->getRouteMatch()->getMatchedRouteName(), 'community') !== false) {
                //                $this->updateCommunityNavigation();
            }
            if (strpos($this->getRouteMatch()->getMatchedRouteName(), 'zfcadmin') !== false) {
                $this->updateAdminNavigation();
            }
            $this->updatePublicNavigation();
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
                    array(
                        'label'  => sprintf(
                            $this->translate("txt-manage-contact-%s"),
                            $this->getContactService()->parseFullName()
                        ),
                        'route'  => 'zfcadmin/contact-manager/view',
                        'router' => $this->getRouter(),
                    )
                );
                break;
            case 'zfcadmin/contact-manager/edit':
                $adminNavigation->addPage(
                    array(
                        'label'  => sprintf(
                            $this->translate("txt-manage-contact-%s"),
                            $this->getContactService()->parseFullName()
                        ),
                        'route'  => 'zfcadmin/contact-manager/view',
                        'router' => $this->getRouter(),
                        'pages'  => array(
                            array(
                                'label'  => sprintf(
                                    $this->translate("txt-edit-contact-%s"),
                                    $this->getContactService()->parseFullName()
                                ),
                                'route'  => $this->getRouteMatch()->getMatchedRouteName(),
                                'active' => true,
                                'router' => $this->getRouter(),
                                'params' => array(
                                    'call-id' => $this->routeMatch->getParam('call-id')
                                )
                            )
                        )
                    )
                );
                break;
            case 'zfcadmin/contact-manager/impersonate':
                $adminNavigation->addPage(
                    array(
                        'label'  => sprintf(
                            $this->translate("txt-manage-contact-%s"),
                            $this->getContactService()->parseFullName()
                        ),
                        'route'  => 'zfcadmin/contact-manager/view',
                        'router' => $this->getRouter(),
                        'pages'  => array(
                            array(
                                'label'  => sprintf(
                                    $this->translate("txt-impersonate-contact-%s"),
                                    $this->getContactService()->parseFullName()
                                ),
                                'route'  => $this->getRouteMatch()->getMatchedRouteName(),
                                'active' => true,
                                'router' => $this->getRouter(),
                                'params' => array(
                                    'call-id' => $this->routeMatch->getParam('call-id')
                                )
                            )
                        )
                    )
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
                    array(
                        'label'  => $this->translate("txt-home"),
                        'route'  => 'home',
                        'active' => true,
                        'router' => $this->getRouter(),
                        'pages'  => array(
                            array(
                                'label'  => $this->translate("txt-account-information"),
                                'route'  => 'contact/profile',
                                'active' => true,
                                'router' => $this->getRouter(),
                            )
                        )
                    )
                );
                break;
            case 'contact/profile-edit':
                $publicNavigation->addPage(
                    array(
                        'label'  => $this->translate("txt-home"),
                        'route'  => 'home',
                        'active' => true,
                        'router' => $this->getRouter(),
                        'pages'  => array(
                            array(
                                'label'  => $this->translate("txt-account-information"),
                                'route'  => 'contact/profile',
                                'active' => true,
                                'router' => $this->getRouter(),
                                'pages'  => array(
                                    array(
                                        'label'  => $this->translate("txt-profile-edit"),
                                        'route'  => $this->getRouteMatch()->getMatchedRouteName(),
                                        'router' => $this->getRouter(),
                                        'active' => true
                                    )
                                )
                            )
                        )
                    )
                );
                break;
            case 'contact/change-password':
                $publicNavigation->addPage(
                    array(
                        'label'  => $this->translate("txt-home"),
                        'route'  => 'home',
                        'active' => true,
                        'router' => $this->getRouter(),
                        'pages'  => array(
                            array(
                                'label'  => $this->translate("txt-account-information"),
                                'route'  => 'contact/profile',
                                'active' => true,
                                'router' => $this->getRouter(),
                                'pages'  => array(
                                    array(
                                        'label'  => $this->translate("txt-change-password"),
                                        'route'  => $this->getRouteMatch()->getMatchedRouteName(),
                                        'router' => $this->getRouter(),
                                        'active' => true
                                    )
                                )
                            )
                        )
                    )
                );
                break;
        }

        return true;
    }
}
