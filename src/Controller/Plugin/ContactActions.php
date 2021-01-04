<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\Controller\Plugin;

use Contact\Entity\Contact;
use Contact\Entity\OptIn;
use Contact\Service\ContactService;
use General\Entity\Gender;
use General\Entity\Title;
use General\Service\EmailService;
use General\Service\GeneralService;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

use function strlen;

/**
 * Class ContactActions
 * @package Contact\Controller\Plugin
 */
final class ContactActions extends AbstractPlugin
{
    private ContactService $contactService;
    private GeneralService $generalService;
    private EmailService $emailService;

    public function __construct(
        ContactService $contactService,
        GeneralService $generalService,
        EmailService $emailService
    ) {
        $this->contactService = $contactService;
        $this->generalService = $generalService;
        $this->emailService   = $emailService;
    }

    public function __invoke(): ContactActions
    {
        return $this;
    }

    public function register(
        string $emailAddress,
        string $firstName,
        string $middleName,
        string $lastName,
        array $optIns = []
    ): Contact {
        //Create the account
        $contact = new Contact();
        $contact->setEmail($emailAddress);
        $contact->setFirstName($firstName);
        if (strlen($middleName) > 0) {
            $contact->setMiddleName($middleName);
        }

        $contact->setLastName($lastName);

        /** @var Gender $gender */
        $gender = $this->generalService->find(Gender::class, Gender::GENDER_UNKNOWN);
        $contact->setGender($gender);

        /** @var Title $title */
        $title = $this->generalService->find(Title::class, Title::TITLE_UNKNOWN);
        $contact->setTitle($title);

        foreach ($optIns as $optInId) {
            /** @var OptIn $optIn */
            $optIn = $this->contactService->find(OptIn::class, (int)$optInId);
            if (null !== $optIn) {
                $contact->getOptIn()->add($optIn);
            }
        }

        $this->contactService->save($contact);

        //Send the email
        $email = $this->emailService->createNewWebInfoEmailBuilder('/auth/register:mail');
        $email->addTo($emailAddress);
        $email->setTemplateVariable('display_name', $contact->getDisplayName());
        $email->addDeeplink('community/contact/profile/activate', 'url', $contact);

        $this->emailService->sendBuilder($email);

        return $contact;
    }

    public function subscribe(
        string $emailAddress,
        string $firstName,
        string $middleName,
        string $lastName
    ): Contact {
        //Create the account
        $contact = new Contact();
        $contact->setEmail($emailAddress);
        $contact->setFirstName($firstName);
        if (strlen($middleName) > 0) {
            $contact->setMiddleName($middleName);
        }

        $contact->setLastName($lastName);

        /** @var Gender $gender */
        $gender = $this->generalService->find(Gender::class, Gender::GENDER_UNKNOWN);
        $contact->setGender($gender);

        /** @var Title $title */
        $title = $this->generalService->find(Title::class, Title::TITLE_UNKNOWN);
        $contact->setTitle($title);

        /** @var OptIn $optIn */
        foreach ($this->contactService->findAll(OptIn::class) as $optIn) {
            if (! $optIn->isActive()) {
                continue;
            }
            $contact->getOptIn()->add($optIn);
        }

        $this->contactService->save($contact);

        //Send the email
        $email = $this->emailService->createNewWebInfoEmailBuilder('/auth/subscribe:mail');
        $email->addTo($emailAddress);
        $email->setTemplateVariable('display_name', $contact->getDisplayName());
        $email->addDeeplink('community/contact/profile/activate-optin', 'url', $contact);

        $this->emailService->sendBuilder($email);

        return $contact;
    }

    public function createContact(
        string $emailAddress,
        string $note = '',
        string $firstName = null,
        string $middleName = null,
        string $lastName = null
    ): Contact {
        //Create the account
        $contact = new Contact();
        $contact->setEmail($emailAddress);
        if (null !== $firstName) {
            $contact->setFirstName($firstName);
        }
        if (null !== $middleName) {
            $contact->setMiddleName($middleName);
        }
        if (null !== $lastName) {
            $contact->setLastName($lastName);
        }
        /** @var Gender $gender */
        $gender = $this->generalService->find(Gender::class, Gender::GENDER_UNKNOWN);
        $contact->setGender($gender);

        /** @var Title $title */
        $title = $this->generalService->find(Title::class, Title::TITLE_UNKNOWN);
        $contact->setTitle($title);

        $this->contactService->save($contact);

        if (! empty($note)) {
            $this->contactService->addNoteToContact($note, 'Account creation', $contact);
        }

        return $contact;
    }

    public function lostPassword(string $emailAddress): void
    {
        //Find the contact
        $contact = $this->contactService->findContactByEmail($emailAddress);
        if (null === $contact) {
            return;
        }

        //Send the email
        $email = $this->emailService->createNewWebInfoEmailBuilder('/auth/forgotpassword:mail');
        $email->addContactTo($contact);
        $email->addDeeplink('community/contact/change-password', 'url', $contact);

        $this->emailService->sendBuilder($email);
    }
}
