<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Affiliation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/affiliation for the canonical source repository
 */

declare(strict_types=1);

namespace Contact\Controller\Plugin;

use Contact\Entity\Contact;
use Contact\Entity\OptIn;
use Contact\Service\ContactService;
use Deeplink\Entity\Target;
use Deeplink\Service\DeeplinkService;
use General\Entity\Gender;
use General\Entity\Title;
use General\Service\EmailService;
use General\Service\GeneralService;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Class MergeContact
 *
 * @package Contact\Controller\Plugin
 */
final class ContactActions extends AbstractPlugin
{
    /**
     * @var ContactService
     */
    private $contactService;
    /**
     * @var GeneralService
     */
    private $generalService;
    /**
     * @var DeeplinkService
     */
    private $deeplinkService;
    /**
     * @var EmailService
     */
    private $emailService;

    public function __construct(
        ContactService $contactService,
        GeneralService $generalService,
        DeeplinkService $deeplinkService,
        EmailService $emailService
    ) {
        $this->contactService = $contactService;
        $this->generalService = $generalService;
        $this->deeplinkService = $deeplinkService;
        $this->emailService = $emailService;
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
        if (\strlen($middleName) > 0) {
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

        /** @var Target $target */
        $target = $this->deeplinkService->createTargetFromRoute('community/contact/profile/activate');
        //Create a deep link for the user which redirects to the profile-page
        $deeplink = $this->deeplinkService->createDeeplink($target, $contact);

        $this->emailService->setWebInfo('/auth/register:mail');
        $this->emailService->addToEmailAddress($emailAddress);
        $this->emailService->setTemplateVariable('display_name', $contact->getDisplayName());
        $this->emailService->setTemplateVariable('url', $this->deeplinkService->parseDeeplinkUrl($deeplink));
        $this->emailService->send();

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

        if (!empty($note)) {
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

        /** @var Target $target */
        $target = $this->deeplinkService->createTargetFromRoute('community/contact/change-password');
        //Create a deeplink for the user which redirects to the profile-page
        $deeplink = $this->deeplinkService->createDeeplink($target, $contact);

        $this->emailService->setWebInfo('/auth/forgotpassword:mail');
        $this->emailService->addTo($contact);
        $this->emailService->setTemplateVariable('url', $this->deeplinkService->parseDeeplinkUrl($deeplink));
        $this->emailService->send();
    }
}
