<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

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
    public function update(): void
    {
        /*
         * Add a route for the facebook
         */
        if (strpos($this->getRouteMatch()->getMatchedRouteName(), 'community') === 0) {
            $this->includeFacebooksInNavigation();
        }
    }


    /**
     * Update the navigation for a publication.
     */
    public function includeFacebooksInNavigation(): void
    {
        $navigation = $this->getNavigation()->findOneBy('id', 'community/contact');

        if (is_null($navigation)) {
            return;
        }
        /*
         * Update the navigation with the facebooks (if a a contact object is present)
         */
        if (!is_null($this->getContact())) {
            foreach ($this->getContactService()->findFacebookByContact($this->getContact()) as $facebook) {

                $page = [
                    'label'      => $facebook->getFacebook(),
                    'route'      => 'community/contact/facebook/view',
                    'active'     => (int)$this->getRouteMatch()->getParam('facebook') === $facebook->getId(),
                    'router'     => $this->getRouter(),
                    'routeMatch' => $this->getRouteMatch(),
                    'params'     => [
                        'facebook' => $facebook->getId(),
                    ],
                ];

                $navigation->addPage($page);
            }
        }
    }

    /**
     * @return Contact
     */
    public function getContact(): Contact
    {
        return $this->contact;
    }

    /**
     * @param Contact $contact
     * @return ContactNavigationService
     */
    public function setContact(Contact $contact): ContactNavigationService
    {
        $this->contact = $contact;

        return $this;
    }
}
