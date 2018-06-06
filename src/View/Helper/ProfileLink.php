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
 * Class ProfileLink
 *
 * @package Contact\View\Helper
 */
class ProfileLink extends LinkAbstract
{
    public function __invoke(
        Contact $contact = null,
        string $action = 'view',
        string $show = 'name',
        string $hash = null
    ): string {
        $this->setContact($contact);
        $this->setAction($action);
        $this->setShow($show);
        $this->setHash($hash);

        if (!$this->hasAccess($this->getContact(), ContactAssertion::class, $this->getAction())) {
            return '';
        }
        $this->setShowOptions(
            [
                'email'           => $this->getContact()->getEmail(),
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
            case 'profile':
                $this->setRouter('community/contact/profile/view');
                $this->setText(
                    sprintf(
                        $this->translate("txt-view-profile-of-contact-%s"),
                        $this->getContact()->getDisplayName()
                    )
                );
                break;
            case 'organisation':
                $this->setRouter('community/contact/profile/organisation');
                $this->setText(
                    sprintf(
                        $this->translate("txt-view-organisation-of-contact-%s"),
                        $this->getContact()->getDisplayName()
                    )
                );
                break;
            case 'events':
                $this->setRouter('community/contact/profile/events');
                $this->setText(
                    sprintf(
                        $this->translate("txt-view-events-of-contact-%s"),
                        $this->getContact()->getDisplayName()
                    )
                );
                break;
            case 'contact':
                $this->setRouter('community/contact/profile/contact');
                $this->setText(
                    sprintf(
                        $this->translate("txt-view-profile-of-contact-%s"),
                        $this->getContact()->getDisplayName()
                    )
                );
                break;
            case 'edit':
                $this->setRouter('community/contact/profile/edit');
                $this->setText($this->translate("txt-edit-your-profile"));
                break;
            default:
                throw new \Exception(sprintf("%s is an incorrect action for %s", $this->getAction(), __CLASS__));
        }
    }
}
