<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Project
 * @package     View
 * @subpackage  Helper
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */

namespace Contact\View\Helper;

use Contact\Entity\Contact;
use Contact\Service\ContactService;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;

/**
 * Class VersionServiceProxy
 * @package General\View\Helper
 */
class ContactServiceProxy extends AbstractHelper implements ServiceLocatorAwareInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @param Contact $contact
     *
     * @return ContactService
     */
    public function __invoke(Contact $contact)
    {
        $contactService = clone $this->serviceLocator->getServiceLocator()->get('contact_contact_service');
        return $contactService->setContact($contact);
    }

    /**
     * Get the service locator.
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
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
}