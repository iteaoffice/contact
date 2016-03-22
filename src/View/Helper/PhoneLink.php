<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Contact\View\Helper;

use Contact\Acl\Assertion\Phone as PhoneAssertion;
use Contact\Entity\Contact;
use Contact\Entity\Phone;

/**
 * Create a link to an phone.
 *
 * @category    Phone
 */
class PhoneLink extends LinkAbstract
{
    /**
     * @param Phone|null $phone
     * @param string $action
     * @param string $show
     * @param Contact|null $contact
     * @return string
     */
    public function __invoke(
        Phone $phone = null,
        $action = 'view',
        $show = 'name',
        Contact $contact = null
    ) {
        $this->setPhone($phone);
        $this->setAction($action);
        $this->setShow($show);
        $this->setContact($contact);

        if (!$this->hasAccess(
            $this->getPhone(),
            PhoneAssertion::class,
            $this->getAction()
        )
        ) {
            return '';
        }

        $this->setShowOptions(
            [
                'name' => $this->getPhone()->getPhone(),
            ]
        );
        $this->addRouterParam('id', $this->getPhone()->getId());
        $this->addRouterParam('contact', $this->getContact()->getId());

        return $this->createLink();
    }

    /**
     * @throws \Exception
     */
    public function parseAction()
    {
        switch ($this->getAction()) {
            case 'new':
                if (is_null($this->getContact())) {
                    throw new \Exception(sprintf("A contact is needed for a new phone"));
                }

                $this->setRouter('zfcadmin/phone-manager/new');
                $this->setText($this->translate("txt-new-phone"));
                break;
            case 'edit':
                $this->setRouter('zfcadmin/phone-manager/edit');
                $this->setText(
                    sprintf($this->translate("txt-edit-phone-%s"), $this->getPhone()->getPhone())
                );
                break;
            default:
                throw new \Exception(sprintf("%s is an incorrect action for %s", $this->getAction(), __CLASS__));
        }
    }
}
