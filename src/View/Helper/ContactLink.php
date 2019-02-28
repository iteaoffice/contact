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

use Contact\Acl\Assertion\Contact as ContactAssertion;
use Contact\Entity\Contact;

/**
 * Create a link to an contact.
 *
 * @category    Contact
 */
class ContactLink extends LinkAbstract
{
    public function __invoke(
        Contact $contact = null,
        $action = 'view',
        $show = 'name',
        $hash = null,
        $alternativeShow = null,
        $fragment = null
    ): string {
        $this->setContact($contact);
        $this->setAction($action);
        $this->setShow($show);
        $this->setHash($hash);
        $this->setFragment($fragment);

        $this->setAlternativeShow($hash);
        if (null !== $alternativeShow) {
            $this->setAlternativeShow($alternativeShow);
        }

        if (!$this->hasAccess($this->getContact(), ContactAssertion::class, $this->getAction())) {
            return '';
        }
        $this->setShowOptions(
            [
                'email'           => $this->getContact()->getEmail(),
                'paginator'       => $this->getAlternativeShow(),
                'alternativeShow' => $this->getAlternativeShow(),
                'firstname'       => $this->getContact()->getFirstName(),
                'initials'        => $this->getContact()->parseInitials(),
                'name'            => $this->getContact()->getDisplayName(),
            ]
        );
        $this->addRouterParam('hash', $hash);
        $this->addRouterParam('id', $this->getContact()->getId());

        return $this->createLink();
    }

    public function parseAction(): void
    {
        switch ($this->getAction()) {
            case 'new':
                $this->setRouter('zfcadmin/contact-admin/new');
                $this->setText($this->translate('txt-new-contact'));
                break;
            case 'list':
                $this->setRouter('zfcadmin/contact-admin/list');
                $this->setText($this->translate('txt-list-contacts'));
                break;
            case 'list-old':
                $this->setRouter('zfcadmin/contact-admin/list-old');
                $this->setText($this->translate('txt-list-contacts-legacy'));
                break;
            case 'import':
                $this->setRouter('zfcadmin/contact-admin/import');
                $this->setText($this->translate('txt-import-contacts'));
                break;
            case 'edit-admin':
                $this->setRouter('zfcadmin/contact-admin/edit');
                $this->setText(
                    sprintf(
                        $this->translate('txt-edit-contact-in-admin-%s'),
                        $this->getContact()->getDisplayName()
                    )
                );
                break;
            case 'view-admin':
                $this->setRouter('zfcadmin/contact-admin/view');
                $this->setText(
                    sprintf(
                        $this->translate('txt-view-contact-in-admin-%s'),
                        $this->getContact()->getDisplayName()
                    )
                );
                break;
            case 'impersonate':
                $this->setRouter('zfcadmin/contact-admin/impersonate');
                $this->setText(
                    sprintf(
                        $this->translate('txt-impersonate-contact-%s'),
                        $this->getContact()->getDisplayName()
                    )
                );
                break;
            case 'permit':
                $this->setRouter('zfcadmin/contact-admin/permit');
                $this->setText(
                    sprintf(
                        $this->translate('txt-permit-of-contact-%s'),
                        $this->getContact()->getDisplayName()
                    )
                );
                break;
            case 'change-password':
                $this->setRouter('community/contact/change-password');
                /*
                 * Users can have access without a password (via the deeplink)
                 * We will therefore have the option to set a password
                 */
                if (null === $this->getContact()->getSaltedPassword()) {
                    $this->setText($this->translate('txt-set-your-password'));
                    $this->addClasses('btn-danger');
                } else {
                    $this->setText($this->translate('txt-update-your-password'));
                }
                break;
            case 'add-project':
                $this->setRouter('zfcadmin/contact-admin/add-project');
                $this->setText(
                    sprintf(
                        $this->translate('txt-add-%s-to-a-project'),
                        $this->getContact()->getDisplayName()
                    )
                );
                break;
        }
    }
}
