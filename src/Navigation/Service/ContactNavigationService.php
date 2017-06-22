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
    public function update()
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
                    'active' => strtolower((string)$this->getRouteMatch()->getParam('namespace')) === 'contact'
                        && (int)$this->getRouteMatch()->getParam('id') === $facebook->getId(),
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
