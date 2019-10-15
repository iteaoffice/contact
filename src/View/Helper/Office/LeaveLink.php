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

use Contact\Acl\Assertion\Office\LeaveAssertion;
use Contact\Entity\Office\Contact as OfficeContact;
use Contact\Entity\Office\Leave;
use Contact\View\Helper\LinkAbstract;

/**
 * Class LeaveLink
 * @package Contact\View\Helper\Office
 */
class LeaveLink extends LinkAbstract
{
    /**
     * @var Leave
     */
    private $leave;

    public function __invoke(
        Leave         $leave = null,
        string        $action = 'view',
        string        $show = 'name',
        OfficeContact $officeContact = null
    ): string {
        $this->leave = $leave ?? new Leave();
        $this->setAction($action);
        $this->setShow($show);

        if (!$this->hasAccess($this->leave, LeaveAssertion::class, $this->getAction())) {
            return '';
        }
        $this->setShowOptions([
            'name' => $this->leave->getDescription()
        ]);

        $this->addRouterParam('id', $this->leave->getId());

        if (null !== $officeContact) {
            $this->addRouterParam('officeContactId', $officeContact->getId());
        }

        return $this->createLink();
    }

    public function parseAction(): void
    {
        switch ($this->getAction()) {
            case 'new':
                $this->setRouter('zfcadmin/contact/office/leave/new');
                $this->setText($this->translate('txt-new-leave'));
                break;
            case 'list':
                $this->setRouter('zfcadmin/contact/office/leave/list');
                $this->setText($this->translate('txt-list-leave'));
                break;
            case 'edit':
                $this->setRouter('zfcadmin/contact/office/leave/edit');
                $this->setText($this->translate('txt-edit-leave'));
                break;
            case 'view':
                $this->setRouter('zfcadmin/contact/office/leave/view');
                $this->setText($this->translate('txt-view-leave'));
                break;
            case 'new-admin':
                $this->setRouter('zfcadmin/contact/office/new-leave');
                $this->setText($this->translate('txt-new-leave'));
                break;
            case 'edit-admin':
                $this->setRouter('zfcadmin/contact/office/edit-leave');
                $this->setText($this->translate('txt-edit-leave'));
                break;
        }
    }
}
