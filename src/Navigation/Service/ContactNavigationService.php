<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Navigation\Service;

use Contact\Entity\Contact;
use Contact\Entity\Facebook;
use Contact\Service\ContactService;
use Laminas\I18n\Translator\Translator;
use Laminas\Navigation\Navigation;
use Laminas\Router\RouteMatch;
use Laminas\Router\SimpleRouteStack;

/**
 * Class ContactNavigationService
 *
 * @package Contact\Navigation\Service
 */
final class ContactNavigationService
{
    /**
     * @var Contact
     */
    private $contact;
    /**
     * @var RouteMatch
     */
    private $routeMatch;
    /**
     * @var Navigation
     */
    private $navigation;
    /**
     * @var SimpleRouteStack
     */
    private $router;
    /**
     * @var Translator
     */
    private $translator;
    /**
     * @var ContactService;
     */
    private $contactService;


    public function update(): void
    {
        /*
         * Add a route for the facebook
         */
        if (strpos($this->routeMatch->getMatchedRouteName(), 'community') === 0) {
            $this->includeFacebooksInNavigation();
        }
    }


    public function includeFacebooksInNavigation(): void
    {
        $navigation = $this->navigation->findOneBy('id', 'community/contact');

        if (null === $navigation) {
            return;
        }
        /*
         * Update the navigation with the facebooks (if a a contact object is present)
         */
        if (null !== $this->contact) {
            /** @var Facebook $facebook */
            foreach ($this->contactService->findFacebookByContact($this->contact) as $facebook) {
                $page = [
                    'label'      => $facebook->getFacebook(),
                    'route'      => 'community/contact/facebook/view',
                    'active'     => (int)$this->routeMatch->getParam('facebook') === $facebook->getId(),
                    'router'     => $this->router,
                    'routeMatch' => $this->routeMatch,
                    'params'     => [
                        'facebook' => $facebook->getId(),
                    ],
                    'pages'      => [
                        [
                            'label'      => $this->translator->translate('txt-send-message'),
                            'route'      => 'community/contact/facebook/send-message',
                            'active'     => $this->routeMatch->getMatchedRouteName()
                                === 'community/contact/facebook/send-message',
                            'router'     => $this->router,
                            'routeMatch' => $this->routeMatch,
                            'params'     => [
                                'facebook' => $facebook->getId(),
                            ],
                        ]
                    ]
                ];

                $navigation->addPage($page);
            }
        }
    }

    public function setContact(Contact $contact): ContactNavigationService
    {
        $this->contact = $contact;

        return $this;
    }

    public function setRouteMatch(RouteMatch $routeMatch): ContactNavigationService
    {
        $this->routeMatch = $routeMatch;
        return $this;
    }

    public function setTranslator(Translator $translator): ContactNavigationService
    {
        $this->translator = $translator;
        return $this;
    }

    public function setContactService(ContactService $contactService): ContactNavigationService
    {
        $this->contactService = $contactService;
        return $this;
    }

    public function setNavigation(Navigation $navigation): ContactNavigationService
    {
        $this->navigation = $navigation;
        return $this;
    }

    public function setRouter(SimpleRouteStack $router): ContactNavigationService
    {
        $this->router = $router;
        return $this;
    }
}
