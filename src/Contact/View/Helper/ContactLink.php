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

use Contact\Acl\Assertion\Contact as ContactAssertion;
use Contact\Entity\Contact;

/**
 * Create a link to an contact
 *
 * @category    Contact
 * @package     View
 * @subpackage  Helper
 */
class ContactLink extends LinkAbstract
{
    /**
     * @var Contact
     */
    protected $contact;

    /**
     * @param Contact $contact
     * @param string  $action
     * @param string  $show
     * @param null    $page
     * @param null    $alternativeShow
     *
     * @return string
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function __invoke(
        Contact $contact = null,
        $action = 'view',
        $show = 'name',
        $page = null,
        $alternativeShow = null
    ) {

        $this->setContact($contact);
        $this->setAction($action);
        $this->setShow($show);

        /**
         * If the alternativeShow is not null, use it an otherwise take the page
         */
        if (!is_null($alternativeShow)) {
            $this->setAlternativeShow($alternativeShow);
        } else {
            $this->setAlternativeShow($page);
        }

        if (!$this->hasAccess(
            $this->getContact(),
            ContactAssertion::class,
            $this->getAction()
        )
        ) {
            return 'Access denied';
        }

        $this->setShowOptions(
            [
                'email'     => $this->getContact()->getEmail(),
                'paginator' => $this->getAlternativeShow(),
                'name'      => $this->getContact()->getDisplayName(),
            ]
        );

        $this->addRouterParam('page', $page);
        $this->addRouterParam('id', $this->getContact()->getId());

        return $this->createLink();
    }

    public function parseAction()
    {

        switch ($this->getAction()) {
            case 'new':
                $this->setRouter('zfcadmin/contact-manager/new');
                $this->setText($this->translate("txt-new-contact"));
                break;
            case 'list':
                $this->setRouter('zfcadmin/contact-manager/list');
                $this->setText($this->translate("txt-list-contacts"));
                break;
            case 'edit-admin':
                $this->setRouter('zfcadmin/contact-manager/edit');
                $this->setText(sprintf($this->translate("txt-edit-contact-%s"), $this->getContact()->getDisplayName()));
                break;
            case 'profile':
                $this->setRouter('contact/profile');
                $this->setText(
                    sprintf(
                        $this->translate("txt-view-profile-of-contact-%s"),
                        $this->getContact()->getDisplayName()
                    )
                );
                break;
            case 'view-admin':
                $this->setRouter('zfcadmin/contact-manager/view');
                $this->setText(sprintf($this->translate("txt-view-contact-%s"), $this->getContact()->getDisplayName()));
                break;
            case 'impersonate':
                $this->setRouter('zfcadmin/contact-manager/impersonate');
                $this->setText(
                    sprintf($this->translate("txt-impersonate-contact-%s"), $this->getContact()->getDisplayName())
                );
                break;
            case 'edit-profile':
                $this->setRouter('contact/profile-edit');
                $this->setText($this->translate("txt-edit-your-profile"));
                break;
            case 'change-password':
                $this->setRouter('contact/change-password');
                /**
                 * Users can have access without a password (via the deeplink)
                 * We will therefore have the option to set a password
                 */
                if (is_null($this->getContact()->getSaltedPassword())) {
                    $this->setText($this->translate("txt-set-your-password"));
                    $this->addClasses('btn-danger');
                } else {
                    $this->setText($this->translate("txt-update-your-password"));
                }

                break;

            default:
                throw new \Exception(sprintf("%s is an incorrect action for %s", $this->getAction(), __CLASS__));
        }
    }

    /**
     * @return Contact
     */
    public function getContact()
    {
        if (is_null($this->contact)) {
            $this->contact = new Contact();
        }

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
