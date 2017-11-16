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

namespace Contact\View\Helper;

use Contact\Acl\Assertion\Address as AddressAssertion;
use Contact\Entity\Address;
use Contact\Entity\Contact;

/**
 * Create a link to an address.
 *
 * @category    Address
 */
class AddressLink extends LinkAbstract
{
    /**
     * @param Address|null $address
     * @param string $action
     * @param string $show
     * @param Contact|null $contact
     *
     * @return string
     */
    public function __invoke(
        Address $address = null,
        $action = 'view',
        $show = 'name',
        Contact $contact = null
    ) {
        $this->setAddress($address);
        $this->setAction($action);
        $this->setShow($show);
        $this->setContact($contact);

        if (!$this->hasAccess(
            $this->getAddress(),
            AddressAssertion::class,
            $this->getAction()
        )
        ) {
            return '';
        }

        $this->setShowOptions(
            [
                'name' => $this->getAddress()->getAddress(),
            ]
        );
        $this->addRouterParam('id', $this->getAddress()->getId());
        $this->addRouterParam('contact', $this->getContact()->getId());

        return $this->createLink();
    }

    /**
     * @throws \Exception
     */
    public function parseAction(): void
    {
        switch ($this->getAction()) {
            case 'new':
                if (\is_null($this->getContact())) {
                    throw new \Exception(sprintf("A contact is needed for a new address"));
                }

                $this->setRouter('zfcadmin/address/new');
                $this->setText($this->translate("txt-new-address"));
                break;
            case 'edit':
                $this->setRouter('zfcadmin/address/edit');
                $this->setText(
                    sprintf($this->translate("txt-edit-address-%s"), $this->getAddress()->getAddress())
                );
                break;
            default:
                throw new \Exception(sprintf("%s is an incorrect action for %s", $this->getAction(), __CLASS__));
        }
    }
}
