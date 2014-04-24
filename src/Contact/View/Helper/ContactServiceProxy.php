<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     View
 * @subpackage  Helper
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */

namespace Contact\View\Helper;

use Zend\View\HelperPluginManager;
use Zend\View\Helper\AbstractHelper;
use Contact\Entity\Contact;
use Contact\Service\ContactService;

/**
 * Class ContactHandler
 * @package Contact\View\Helper
 */
class ContactServiceProxy extends AbstractHelper
{
    /**
     * @var ContactService
     */
    protected $contactService;

    /**
     * @param HelperPluginManager $helperPluginManager
     */
    public function __construct(HelperPluginManager $helperPluginManager)
    {
        $this->contactService = $helperPluginManager->getServiceLocator()->get('contact_contact_service');
    }

    /**
     * @param Contact $contact
     *
     * @return ContactService
     */
    public function __invoke(Contact $contact)
    {
        return $this->contactService->setContact($contact);
    }
}
