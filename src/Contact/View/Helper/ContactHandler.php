<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @contact    Contact
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Contact\View\Helper;

use Contact\Entity\Contact;
use Contact\Entity\OptIn;
use Contact\Service\ContactService;
use Content\Entity\Content;
use Zend\Http\Request;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;
use Zend\View\HelperPluginManager;
use ZfcTwig\View\TwigRenderer;

/**
 * Class ContactHandler.
 */
class ContactHandler extends AbstractHelper implements ServiceLocatorAwareInterface
{
    /**
     * @var HelperPluginManager
     */
    protected $serviceLocator;
    /**
     * @var Contact
     */
    protected $contact;
    /**
     * @var OptIn
     */
    protected $optIn;

    /**
     * @param Content $content
     *
     * @return string
     */
    public function __invoke(Content $content)
    {
        /*
         * Extract the contentParams and save them in the class
         */
        $this->extractContentParam($content);
        switch ($content->getHandler()->getHandler()) {
            case 'contact_optin_button':
                return $this->parseOptInButton($this->getOptIn());
            default:
                return sprintf(
                    "No handler available for <code>%s</code> in class <code>%s</code>",
                    $content->getHandler()->getHandler(),
                    __CLASS__
                );
        }
    }

    /**
     * @param Content $content
     */
    public function extractContentParam(Content $content)
    {
        foreach ($content->getContentParam() as $param) {
            /*
             * When the parameterId is 0 (so we want to get the article from the URL
             */
            switch ($param->getParameter()->getParam()) {
                case 'optin':
                    if (!is_null($optInId = $param->getParameterId())) {
                        /** @var OptIn $optIn */
                        $optIn = $this->getContactService()->findEntityById('optIn', $optInId);
                        $this->setOptIn($optIn);
                    }
                    break;
                default:
                    $this->setContactId($param->getParameterId());
                    break;
            }
        }
    }

    /**
     * @return RouteMatch
     */
    public function getRouteMatch()
    {
        return $this->getServiceLocator()->get('application')->getMvcEvent()->getRouteMatch();
    }

    /**
     * Get the service locator.
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator->getServiceLocator();
    }

    /**
     * Set the service locator.
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return AbstractHelper
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
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
        if (!$contact instanceof Contact) {
            $contact = $this->getContactService()->findEntityById('contact', $contact);
        }
        $this->contact = $contact;
    }

    /**
     * @return ContactService
     */
    public function getContactService()
    {
        return $this->getServiceLocator()->get(ContactService::class);
    }

    /**
     * @param string $id
     *
     * @return ContactService
     */
    public function setContactId($id)
    {
        return $this->getContactService()->setContactId($id);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function translate($string)
    {
        return $this->serviceLocator->get('translate')->__invoke($string);
    }

    /**
     * @param OptIn $optIn
     *
     * @return string
     */
    public function parseOptInButton(OptIn $optIn)
    {
        return $this->getRenderer()->render('contact/partial/optin-button', [
            'includeAngularApp' => true,
            'optIn'             => $optIn,
            'hasIdentity'       => $this->getServiceLocator()->get('Application\Authentication\Service')->hasIdentity(),
        ]);
    }

    /**
     * @param ContactService $contactService
     *
     * @return string
     */
    public function parseContact(ContactService $contactService)
    {
        return $this->getRenderer()->render('contact/partial/contact', [
            'contact' => $contactService->getContact(),
        ]);
    }

    /**
     * @return TwigRenderer
     */
    public function getRenderer()
    {
        return $this->getServiceLocator()->get('ZfcTwigRenderer');
    }

    /**
     * Proxy to the original request object to handle form.
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->getServiceLocator()->get('application')->getMvcEvent()->getRequest();
    }

    /**
     * @return OptIn
     */
    public function getOptIn()
    {
        return $this->optIn;
    }

    /**
     * @param OptIn $optIn
     */
    public function setOptIn($optIn)
    {
        $this->optIn = $optIn;
    }
}
