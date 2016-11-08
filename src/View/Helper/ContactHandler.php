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

use Contact\Entity\OptIn;
use Contact\Service\ContactService;
use Content\Entity\Content;
use Zend\Http\Request;
use Zend\View\HelperPluginManager;

/**
 * Class ContactHandler.
 */
class ContactHandler extends AbstractViewHelper
{
    /**
     * @var HelperPluginManager
     */
    protected $serviceLocator;
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
                    if (! is_null($optInId = $param->getParameterId())) {
                        /** @var OptIn $optIn */
                        $optIn = $this->getContactService()->findEntityById(OptIn::class, $optInId);
                        $this->setOptIn($optIn);
                    }
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * @return ContactService
     */
    public function getContactService()
    {
        return $this->getServiceManager()->get(ContactService::class);
    }

    /**
     * @param OptIn $optIn
     *
     * @return string
     */
    public function parseOptInButton(OptIn $optIn)
    {
        return $this->getRenderer()->render(
            'contact/partial/optin-button',
            [
            'includeAngularApp' => true,
            'optIn'             => $optIn,
            'hasIdentity'       => $this->getServiceManager()->get('Application\Authentication\Service')->hasIdentity(),
            ]
        );
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

    /**
     * Proxy to the original request object to handle form.
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->getServiceManager()->get('application')->getMvcEvent()->getRequest();
    }
}
