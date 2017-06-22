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
    /**
     * @param Contact|null $contact
     * @param string $action
     * @param string $show
     * @param null $hash
     * @param null $alternativeShow
     * @param null $fragment
     *
     * @return string
     */
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


        /*
         * If the alternativeShow is not null, use it an otherwise take the hash
         */
        if (!is_null($alternativeShow)) {
            $this->setAlternativeShow($alternativeShow);
        } else {
            $this->setAlternativeShow($hash);
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
                'initials'        => sprintf(
                    "%s%s%s",
                    substr((string)$this->getContact()->getFirstName(), 0, 1),
                    substr((string)$this->getContact()->getMiddleName(), 0, 1),
                    substr((string)$this->getContact()->getLastName(), 0, 1)
                ),
                'name'            => $this->getContact()->getDisplayName(),
            ]
        );
        $this->addRouterParam('hash', $hash);
        $this->addRouterParam('id', $this->getContact()->getId());

        return $this->createLink();
    }

    /**
     * @throws \Exception
     */
    public function parseAction(): void
    {
        switch ($this->getAction()) {
            case 'new':
                $this->setRouter('zfcadmin/contact-admin/new');
                $this->setText($this->translate("txt-new-contact"));
                break;
            case 'list':
                $this->setRouter('zfcadmin/contact-admin/list');
                $this->setText($this->translate("txt-list-contacts"));
                break;
            case 'import':
                $this->setRouter('zfcadmin/contact-admin/import');
                $this->setText($this->translate("txt-import-contacts"));
                break;
            case 'edit-admin':
                $this->setRouter('zfcadmin/contact-admin/edit');
                $this->setText(
                    sprintf(
                        $this->translate("txt-edit-contact-in-admin-%s"),
                        $this->getContact()->getDisplayName()
                    )
                );
                break;
            case 'profile':
                $this->setRouter('community/contact/profile/view');
                $this->setText(
                    sprintf(
                        $this->translate("txt-view-profile-of-contact-%s"),
                        $this->getContact()->getDisplayName()
                    )
                );
                break;
            case 'profile-contact':
                $this->setRouter('community/contact/profile/contact');
                $this->setText(
                    sprintf(
                        $this->translate("txt-view-profile-of-contact-%s"),
                        $this->getContact()->getDisplayName()
                    )
                );
                break;
            case 'signature':
                $this->setRouter('community/contact/signature');
                $this->setText($this->translate("txt-signature"));
                break;
            case 'view-admin':
                $this->setRouter('zfcadmin/contact-admin/view');
                $this->setText(
                    sprintf(
                        $this->translate("txt-view-contact-in-admin-%s"),
                        $this->getContact()->getDisplayName()
                    )
                );
                break;
            case 'impersonate':
                $this->setRouter('zfcadmin/contact-admin/impersonate');
                $this->setText(
                    sprintf(
                        $this->translate("txt-impersonate-contact-%s"),
                        $this->getContact()->getDisplayName()
                    )
                );
                break;
            case 'permit':
                $this->setRouter('zfcadmin/contact-admin/permit');
                $this->setText(
                    sprintf(
                        $this->translate("txt-permit-of-contact-%s"),
                        $this->getContact()->getDisplayName()
                    )
                );
                break;
            case 'edit-profile':
                $this->setRouter('community/contact/profile/edit');
                $this->setText($this->translate("txt-edit-your-profile"));
                break;
            case 'change-password':
                $this->setRouter('community/contact/change-password');
                /*
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
}
