<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     Navigation
 * @subpackage  Service
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Contact\Navigation\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Navigation\Service\DefaultNavigationFactory;
use Zend\Mvc\Router\Http\RouteMatch;

use Contact\Service\ContactService;

/**
 * Factory for the Project admin navigation
 *
 * @package    Calendar
 * @subpackage Navigation\Service
 */
class ContactNavigationFactory extends DefaultNavigationFactory
{
    /**
     * @var RouteMatch
     */
    protected $routeMatch;
    /**
     * @var ContactService;
     */
    protected $contactService;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param array                   $pages
     *
     * @return array
     */
    public function getExtraPages(ServiceLocatorInterface $serviceLocator, array $pages)
    {
        $application          = $serviceLocator->get('Application');
        $this->routeMatch     = $application->getMvcEvent()->getRouteMatch();
        $router               = $application->getMvcEvent()->getRouter();
        $this->contactService = $serviceLocator->get('contact_contact_service');
        $authService          = $serviceLocator->get('zfcuser_auth_service');
        $translate            = $serviceLocator->get('viewhelpermanager')->get('translate');

        /**
         * Return $pages when no match is found
         */
        if (is_null($this->routeMatch)) {
            return $pages;
        }

        /**
         * Return $pages when no match is found
         */
        if (is_null($this->routeMatch)) {
            return $pages;
        }

        if (in_array(
            $this->routeMatch->getMatchedRouteName(),
            array(
                'zfcadmin/contact-manager/view',
                'zfcadmin/contact-manager/edit',
                'zfcadmin/contact-manager/impersonate'
            )
        )
        ) {

            $this->contactService->setContactId($this->routeMatch->getParam('id'));
            /**
             * Go over both arrays and check if the new entities can be added
             */
            $pages['contact']['pages']['view'] = array(
                'label'      => (string) $this->contactService->parseFullName(),
                'route'      => 'zfcadmin/contact-manager/view',
                'routeMatch' => $this->routeMatch,
                'router'     => $router,
                'active'     => true,
                'params'     => array(
                    'id' => $this->routeMatch->getParam('id')
                )
            );
        }

        if ($this->routeMatch->getMatchedRouteName() === 'zfcadmin/contact-manager/impersonate') {

            /**
             * Go over both arrays and check if the new entities can be added
             */
            $pages['contact']['pages']['view']['pages']['edit'] = array(
                'label'      => sprintf(
                    $translate("txt-impersonate-contact-%s"),
                    $this->contactService->parseFullName()
                ),
                'route'      => 'zfcadmin/contact-manager/impersonate',
                'routeMatch' => $this->routeMatch,
                'router'     => $router,
                'active'     => true,
                'params'     => array(
                    'id' => $this->routeMatch->getParam('id')
                )
            );
        }

        if ($this->routeMatch->getMatchedRouteName() === 'zfcadmin/contact-manager/edit') {

            $this->contactService->setContactId($this->routeMatch->getParam('id'));
            /**
             * Go over both arrays and check if the new entities can be added
             */

            $pages['contact']['pages']['view']['pages']['edit'] = array(
                'label'      => sprintf($translate("txt-edit-contact-%s"), $this->contactService->parseFullName()),
                'route'      => 'zfcadmin/contact-manager/edit',
                'routeMatch' => $this->routeMatch,
                'router'     => $router,
                'active'     => true,
                'params'     => array(
                    'id' => $this->routeMatch->getParam('id')
                )
            );
        }

        /**
         * Profile page
         */
        if ($this->routeMatch->getMatchedRouteName() === 'contact/profile') {

            $this->contactService->setContact($authService->getIdentity());

            $pages['community'] = array(
                'label'      => $translate("txt-account-information"),
                'route'      => 'contact/profile',
                'routeMatch' => $this->routeMatch,
                'router'     => $router,
            );

            /**
             * Go over both arrays and check if the new entities can be added
             */
            $pages['community']['pages']['profile'] = array(
                'label'      => sprintf($translate("txt-profile-of-%s"), $this->contactService->parseFullName()),
                'route'      => 'contact/profile',
                'routeMatch' => $this->routeMatch,
                'router'     => $router,
                'active'     => true,
            );
        }

        /**
         * Profile page
         */
        if ($this->routeMatch->getMatchedRouteName() === 'contact/profile-edit') {

            $this->contactService->setContact($authService->getIdentity());

            $pages['community'] = array(
                'label'      => $translate("txt-account-information"),
                'route'      => 'contact/profile',
                'routeMatch' => $this->routeMatch,
                'router'     => $router,
            );

            /**
             * Go over both arrays and check if the new entities can be added
             */
            $pages['community']['pages']['profile'] = array(
                'label'      => sprintf($translate("txt-profile-of-%s"), $this->contactService->parseFullName()),
                'route'      => 'contact/profile',
                'routeMatch' => $this->routeMatch,
                'router'     => $router,
            );

            /**
             * Go over both arrays and check if the new entities can be added
             */
            $pages['community']['pages']['profile']['pages']['edit'] = array(
                'label'      => sprintf($translate("txt-edit-profile")),
                'route'      => 'contact/profile-edit',
                'routeMatch' => $this->routeMatch,
                'router'     => $router,
                'active'     => true,
            );
        }

        /**
         * Profile page
         */
        if ($this->routeMatch->getMatchedRouteName() === 'contact/change-password') {

            $this->contactService->setContact($authService->getIdentity());

            $$pages['community'] = array(
                'label'      => $translate("txt-account-information"),
                'route'      => 'contact/profile',
                'routeMatch' => $this->routeMatch,
                'router'     => $router,
            );

            /**
             * Go over both arrays and check if the new entities can be added
             */
            $pages['community']['pages']['profile'] = array(
                'label'      => sprintf($translate("txt-profile-of-%s"), $this->contactService->parseFullName()),
                'route'      => 'contact/profile',
                'routeMatch' => $this->routeMatch,
                'router'     => $router,
            );

            /**
             * Go over both arrays and check if the new entities can be added
             */
            $pages['community']['pages']['profile']['pages']['change-password'] = array(
                'label'      => sprintf($translate("txt-change-password")),
                'route'      => 'contact/change-password',
                'routeMatch' => $this->routeMatch,
                'router'     => $router,
                'active'     => true,
            );
        }

        return $pages;
    }
}
