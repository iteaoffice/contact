<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     Controller
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Contact\Controller;

use Admin\Service\AdminService;
use Admin\Service\AdminServiceAwareInterface;
use BjyAuthorize\Controller\Plugin\IsAllowed;
use Contact\Service\ContactService;
use Contact\Service\ContactServiceAwareInterface;
use Contact\Service\FormService;
use Contact\Service\FormServiceAwareInterface;
use Contact\Service\SelectionService;
use Contact\Service\SelectionServiceAwareInterface;
use Deeplink\Service\DeeplinkService;
use Deeplink\Service\DeeplinkServiceAwareInterface;
use General\Service\EmailService;
use General\Service\GeneralService;
use General\Service\GeneralServiceAwareInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\Plugin\FlashMessenger;
use ZfcUser\Controller\Plugin\ZfcUserAuthentication;

/**
 * @category    Contact
 * @package     Controller
 * @method      ZfcUserAuthentication zfcUserAuthentication()
 * @method      FlashMessenger flashMessenger()
 * @method      IsAllowed isAllowed($resource, $action)
 */
abstract class ContactAbstractController extends AbstractActionController implements
    SelectionServiceAwareInterface,
    AdminServiceAwareInterface,
    FormServiceAwareInterface,
    ContactServiceAwareInterface,
    DeeplinkServiceAwareInterface,
    GeneralServiceAwareInterface
{
    /**
     * @var AdminService
     */
    protected $adminService;
    /**
     * @var ContactService
     */
    protected $contactService;
    /**
     * @var GeneralService
     */
    protected $generalService;
    /**
     * @var EmailService
     */
    protected $emailService;
    /**
     * @var DeeplinkService
     */
    protected $deeplinkService;
    /**
     * @var SelectionService
     */
    protected $selectionService;
    /**
     * @var FormService
     */
    protected $formService;

    /**
     * Gateway to the Contact Service
     *
     * @return ContactService
     */
    public function getContactService()
    {
        return $this->contactService;
    }

    /**
     * @param $contactService
     *
     * @return ContactAbstractController
     */
    public function setContactService(ContactService $contactService)
    {
        $this->contactService = $contactService;

        return $this;
    }

    /**
     * @return GeneralService
     */
    public function getGeneralService()
    {
        return $this->generalService;
    }

    /**
     * @param GeneralService $generalService
     *
     * @return ContactAbstractController
     */
    public function setGeneralService(GeneralService $generalService)
    {
        $this->generalService = $generalService;

        return $this;
    }

    /**
     * Gateway to the Deeplink Service
     *
     * @return DeeplinkService
     */
    public function getDeeplinkService()
    {
        return $this->deeplinkService;
    }

    /**
     * @param $deeplinkService
     *
     * @return ContactAbstractController
     */
    public function setDeeplinkService(DeeplinkService $deeplinkService)
    {
        $this->deeplinkService = $deeplinkService;

        return $this;
    }

    /**
     * @return \Contact\Service\FormService
     */
    public function getFormService()
    {
        return $this->formService;
    }

    /**
     * @param $formService
     *
     * @return ContactController
     */
    public function setFormService($formService)
    {
        $this->formService = $formService;

        return $this;
    }

    /**
     * @return SelectionService
     */
    public function getSelectionService()
    {
        return $this->selectionService;
    }

    /**
     * @param SelectionService $selectionService
     *
     * @return $this
     */
    public function setSelectionService(SelectionService $selectionService)
    {
        $this->selectionService = $selectionService;

        return $this;
    }

    /**
     * @return AdminService
     */
    public function getAdminService()
    {
        return $this->adminService;
    }

    /**
     * @param AdminService $adminService
     *
     * @return $this
     */
    public function setAdminService(AdminService $adminService)
    {
        $this->adminService = $adminService;

        return $this;
    }

    /**
     * @return EmailService
     */
    public function getEmailService()
    {
        return $this->emailService;
    }

    /**
     * @param EmailService $emailService
     *
     * @return $this
     */
    public function setEmailService(EmailService $emailService)
    {
        $this->emailService = $emailService;

        return $this;
    }
}
