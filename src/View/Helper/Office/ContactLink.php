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

namespace Contact\View\Helper\Office;

use Contact\Entity\Contact;
use Contact\Acl\Assertion\Office\ContactAssertion;
use Contact\Entity\Office\Contact as OfficeContact;
use Contact\View\Helper\LinkAbstract;

/**
 * Class ContactLink
 * @package Contact\View\Helper\Office
 */
class ContactLink extends LinkAbstract
{
    /**
     * @var OfficeContact
     */
    private $officeContact;

    public function __invoke(
        OfficeContact $officeContact = null,
        $action = 'view',
        $show = 'name'
    ): string {
        $this->officeContact = $officeContact ?? (new OfficeContact())->setContact(new Contact());
        $this->setAction($action);
        $this->setShow($show);

        if (!$this->hasAccess($this->officeContact, ContactAssertion::class, $this->getAction())) {
            return '';
        }
        $this->setShowOptions([
            'name' => $this->officeContact->getContact()->getDisplayName(),
        ]);

        $this->addRouterParam('id', $this->officeContact->getId());

        return $this->createLink();
    }

    public function parseAction(): void
    {
        switch ($this->getAction()) {
            case 'new':
                $this->setRouter('zfcadmin/contact/office/new');
                $this->setText($this->translate('txt-new-office-member'));
                break;
            case 'list':
                $this->setRouter('zfcadmin/contact/office/list');
                $this->setText($this->translate('txt-list-office-members'));
                break;
            case 'edit':
                $this->setRouter('zfcadmin/contact/office/edit');
                $this->setText($this->translate('txt-edit-office-member'));
                break;
            case 'view':
                $this->setRouter('zfcadmin/contact/office/view');
                $this->setText($this->translate('txt-view-office-member'));
                break;
        }
    }
}
