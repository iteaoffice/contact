<?php
/**
 * ITEA Office all rights reserved
 *
 * @contact    Contact
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\View\Helper;

use Contact\Entity\OptIn;
use Contact\Service\ContactService;
use Content\Entity\Content;
use Content\Entity\Param;
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
                    'No handler available for <code>%s</code> in class <code>%s</code>',
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
        /**
         * Go over the handler params and try to see if it is hardcoded or just set via the route
         */
        foreach ($content->getHandler()->getParam() as $parameter) {
            /*
             * When the parameterId is 0 (so we want to get the article from the URL
             */
            switch ($parameter->getParam()) {
                case 'optin':
                    $optInId = $this->findParamValueFromContent($content, $parameter);

                    if (!is_null($optInId)) {
                        /** @var OptIn $optIn */
                        $optIn = $this->getContactService()->findEntityById(OptIn::class, $optInId);
                        $this->setOptIn($optIn);
                    }
                    break;
            }
        }
    }

    /**
     * @param Content $content
     * @param Param $param
     *
     * @return null|string
     */
    private function findParamValueFromContent(Content $content, Param $param)
    {

        //Try first to see if the param can be found from the route (rule 1)
        if (!is_null($this->getRouteMatch()->getParam($param->getParam()))) {
            return $this->getRouteMatch()->getParam($param->getParam());
        }

        //If it cannot be found, try to find it from the docref (rule 2)
        foreach ($content->getContentParam() as $contentParam) {
            if ($contentParam->getParameter() === $param && !empty($contentParam->getParameterId())) {
                return $contentParam->getParameterId();
            }
        }

        //If not found, take rule 3
        return null;
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
                'hasIdentity'       => $this->getServiceManager()->get('Application\Authentication\Service')
                    ->hasIdentity(),
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
