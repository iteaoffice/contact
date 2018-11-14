<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Controller\Plugin;

use Contact\Entity\Contact;
use Contact\Entity\ContactOrganisation;
use Contact\Entity\OptIn;
use Contact\Entity\Phone;
use Contact\Entity\PhoneType;
use Contact\Entity\Selection;
use Contact\Entity\SelectionContact;
use Contact\Service\ContactService;
use Contact\Service\SelectionContactService;
use Contact\Service\SelectionService;
use Doctrine\Common\Collections\ArrayCollection;
use General\Entity\Gender;
use General\Entity\Title;
use General\Service\CountryService;
use General\Service\GeneralService;
use Organisation\Entity\Organisation;
use Organisation\Entity\Type;
use Organisation\Entity\Web;
use Organisation\Service\OrganisationService;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Validator\EmailAddress;

/**
 * Class HandleImport
 *
 * @package Contact\Controller\Plugin
 */
final class HandleImport extends AbstractPlugin
{
    /**
     * @var array
     */
    private $header = [];
    /**
     * Inverse lookup-array which keeps the keys of the columns.
     *
     * @var array
     */
    private $headerKeys = [];
    /**
     * @var array
     */
    private $content = [];
    /**
     * @var array
     */
    private $errors = [];
    /**
     * @var array
     */
    private $warnings = [];
    /**
     * @var Contact[]
     */
    private $contacts = [];
    /**
     * @var Contact[]
     */
    private $importedContacts = [];
    /**
     * @var OptIn[]
     */
    private $optIn = [];
    /**
     * @var Selection
     */
    private $selection;
    /**
     * @var string
     */
    private $delimiter = "\t";

    /**
     * @var GeneralService
     */
    private $generalService;
    /**
     * @var CountryService
     */
    private $countryService;
    /**
     * @var OrganisationService
     */
    private $organisationService;
    /**
     * @var ContactService
     */
    private $contactService;
    /**
     * @var SelectionContactService
     */
    private $selectionContactService;
    /**
     * @var SelectionService
     */
    private $selectionService;

    public function __construct(
        GeneralService $generalService,
        CountryService $countryService,
        OrganisationService $organisationService,
        ContactService $contactService,
        SelectionContactService $selectionContactService,
        SelectionService $selectionService
    ) {
        $this->generalService = $generalService;
        $this->countryService = $countryService;
        $this->organisationService = $organisationService;
        $this->contactService = $contactService;
        $this->selectionContactService = $selectionContactService;
        $this->selectionService = $selectionService;
    }


    public function __invoke(
        Contact $contact,
        string $data,
        ?array $import = [],
        array $includeOptIn = [],
        ?int $selectionId = null,
        ?string $selectionName = null
    ): self {
        $this->setData($data);

        $this->validateData();

        /** set the selection */
        if (null !== $selectionId || null !== $selectionName) {
            $this->setSelectionFromFromData($contact, $selectionId, $selectionName);
        }

        /** set the optIn */
        $this->setOptInFromFormData($includeOptIn);

        if (!$this->hasErrors()) {
            $this->prepareContent();

            if (\is_array($import) && \count($import) > 0) {
                $this->importContacts($import);
            }
        }

        return $this;
    }

    private function setData(string $sourceData): void
    {
        $sourceData = utf8_encode($sourceData);

        //Explode first on the \n to have the different rows
        $data = \explode(PHP_EOL, $sourceData);

        /*
         * Correct first the delimiter, normally a ; but it can be a ;
         */
        if (\strpos($data[0], ';') !== false) {
            $this->delimiter = "\t";
        }

        $this->header = \explode($this->delimiter, $data[0]);

        //Trim all the elements
        $this->header = \array_map('trim', $this->header);
        /*
         * Go over the rest of the data and add the rows to the array
         */
        $amount = count($data);
        for ($i = 1; $i < $amount; $i++) {
            $row = \explode($this->delimiter, $data[$i]);

            if (\count($row) === count($this->header)) {
                //Trim all the elements
                $row = \array_map('trim', $row);

                $this->content[] = $row;
            } else {
                $this->warnings[] = sprintf(
                    'Row %s has been skipped, does not contain %s elements but %s',
                    $i + 1,
                    \count($this->header),
                    \count($row)
                );
            }
        }
    }

    protected function validateData(): void
    {
        $minimalRequiredElements = ['email', 'firstname', 'lastname'];

        /*
         * Go over all elements and check if the required elements are present
         */
        foreach ($minimalRequiredElements as $element) {
            if (!\in_array(strtolower($element), $this->header, true)) {
                $this->errors[] = sprintf("Element %s is missing in the file", $element);
            }
        }

        /*
         * Create the lookup-table
         */
        $this->headerKeys = array_flip($this->header);

        /*
         * Validate the elements.
         */
        $counter = 2;
        foreach ($this->content as $content) {
            /**
             * Validate the email addresses
             */
            $validate = new EmailAddress();
            if (!$validate->isValid($content[$this->headerKeys['email']])) {
                $this->errors[] = sprintf(
                    'EmailAddress (%s) in row %s is invalid',
                    $content[$this->headerKeys['email']],
                    $counter
                );
            }

            /**
             * Validate the organisation_id
             */
            if (!empty($this->headerKeys['organisation_id'])) {
                $organisation = $this->organisationService->findOrganisationById(
                    (int)$this->headerKeys['organisation_id']
                );
                if (null === $organisation) {
                    $this->errors[] = sprintf(
                        'Organisation with ID (%s) in row %s cannot be found',
                        $content[$this->headerKeys['organisation_id']],
                        $counter
                    );
                }
            }

            /**
             * Validate the country
             */
            if (!empty($this->headerKeys['country'])) {
                $country = $this->countryService->findCountryByName($content[$this->headerKeys['country']]);
                if (null === $country) {
                    $this->warnings[] = sprintf(
                        'Country (%s) in row %s cannot be found',
                        $content[$this->headerKeys['country']],
                        $counter
                    );
                }
            }

            $counter++;
        }
    }

    private function setSelectionFromFromData(Contact $contact, ?int $selectionId, ?string $selectionName): void
    {
        $selection = null;
        /** Parse the $selectionId if not empty */
        if (null !== $selectionId) {
            $selection = $this->selectionService->findSelectionById((int) $selectionId);
            if (null !== $selection) {
                $this->selection = $selection;
            }
        }

        if (null === $selection && null !== $selectionName) {
            $selection = new Selection();
            $selection->setSelection($selectionName);
            $selection->setContact($contact);

            $this->selectionService->save($selection);
            $this->selection = $selection;
        }
    }

    private function setOptInFromFormData(array $includeOptIn): void
    {
        foreach ($includeOptIn as $optInId) {
            $optIn = $this->contactService->find(OptIn::class, (int) $optInId);
            if (null !== $optIn) {
                $this->optIn[] = $optIn;
            }
        }
    }

    public function hasErrors(): bool
    {
        return \count($this->errors) > 0;
    }

    private function prepareContent(): void
    {
        foreach ($this->content as $key => $content) {
            //See first if the contact can be found
            $contact = $this->contactService->findContactByEmail($content[$this->headerKeys['email']]);

            if (null !== $contact) {
                $contact->key = $key;
                $this->contacts[] = $contact;
                continue;
            }


            /**
             * 'organisation_id' => int 0
             * 'organisation' => int 1
             * 'firstname' => int 2
             * 'middlename' => int 3
             * 'lastname' => int 4
             * 'position' => int 5
             * 'email' => int 6
             * 'phone' => int 7
             * 'country' => int 8
             * 'gender' => int 8
             * 'title' => int 8
             */

            //Contact is not found
            $contact = new Contact();
            $contact->key = $key;
            $contact->setFirstName($content[$this->headerKeys['firstname']]);
            if (isset($this->headerKeys['middlename']) && !empty($content[$this->headerKeys['middlename']])) {
                $contact->setMiddleName($content[$this->headerKeys['middlename']]);
            }
            $contact->setLastName($content[$this->headerKeys['lastname']]);
            $contact->setEmail($content[$this->headerKeys['email']]);

            $gender = null;
            $title = null;
            if (isset($this->headerKeys['gender']) && !empty($content[$this->headerKeys['gender']])) {
                $gender = $this->generalService->findGenderByGender($content[$this->headerKeys['gender']]);
                $contact->setGender($gender);
            }

            if (null === $gender) {
                $gender = $this->generalService->find(Gender::class, Gender::GENDER_UNKNOWN);
            }

            $contact->setGender($gender);

            if (isset($this->headerKeys['title']) && !empty($content[$this->headerKeys['title']])) {
                $title = $this->generalService->findTitleByTitle($content[$this->headerKeys['title']]);
                $contact->setTitle($title);
            }

            if (null === $title) {
                $title = $this->generalService->find(Title::class, Title::TITLE_UNKNOWN);
            }

            $contact->setTitle($title);

            if (isset($this->headerKeys['phone']) && !empty($content[$this->headerKeys['phone']])) {
                $contact->setPosition($content[$this->headerKeys['phone']]);
            }

            //If found, set the phone number
            if (isset($this->headerKeys['phone']) && !empty($content[$this->headerKeys['phone']])) {
                $phone = new Phone();

                /** @var PhoneType $phoneType */
                $phoneType = $this->contactService->find(PhoneType::class, PhoneType::PHONE_TYPE_DIRECT);
                $phone->setType($phoneType);
                $phone->setPhone($content[$this->headerKeys['phone']]);
                $phone->setContact($contact);
                $phones = new ArrayCollection();
                $phones->add($phone);
                $contact->setPhone($phones);
            }


            //Try to find the country
            $country = null;

            if (isset($this->headerKeys['country']) && !empty($content[$this->headerKeys['country']])) {
                $country = $this->countryService->findCountryByName($content[$this->headerKeys['country']]);
            }

            $organisation = null;
            $organisationName = null;

            if (isset($this->headerKeys['organisation']) && !empty($content[$this->headerKeys['organisation']])) {
                //Try to find the organisation
                $organisationName = $content[$this->headerKeys['organisation']];
            }


            if (isset($this->headerKeys['organisation_id'])
                && !empty($content[$this->headerKeys['organisation_id']])
            ) {
                $organisation = $this->organisationService->findOrganisationById(
                    (int)$content[$this->headerKeys['organisation_id']]
                );
            }

            if (null !== $organisationName && null === $organisation && null !== $country) {
                $organisation = $this->organisationService->findOrganisationByNameCountry(
                    $organisationName,
                    $country
                );
            }

            //If the organisation does not exist, create it
            if (null === $organisation && null !== $country && null !== $organisationName) {
                $organisation = new Organisation();
                $organisation->setOrganisation($organisationName);
                $organisation->setCountry($country);

                //Add the type
                /** @var Type $organisationType */
                $organisationType = $this->organisationService->findEntityById(Type::class, Type::TYPE_UNKNOWN);
                $organisation->setType($organisationType);

                //Add the domain
                $validate = new EmailAddress();
                $validate->isValid($content[$this->headerKeys['email']]);

                $organisationWebElements = new ArrayCollection();

                $organisationWeb = new Web();
                $organisationWeb->setWeb($validate->hostname);
                $organisationWeb->setMain(Web::MAIN);
                $organisationWeb->setOrganisation($organisation);
                $organisationWebElements->add($organisationWeb);

                if (isset($this->headerKeys['website']) && null !== $content[$this->headerKeys['website']]) {
                    //Strip the http:// and https://
                    $website = \str_replace(['http://', 'https://'], '', $content[$this->headerKeys['website']]);

                    $organisationWebsite = new Web();
                    $organisationWebsite->setMain(Web::MAIN);
                    $organisationWebsite->setWeb($website);
                    $organisationWebsite->setOrganisation($organisation);
                    $organisationWebElements->add($organisationWebsite);
                }

                $organisation->setWeb($organisationWebElements);
            }

            /**
             * If an organisation is found, add it to the contact
             */
            if (null !== $organisation) {
                $contactOrganisation = new ContactOrganisation();
                $contactOrganisation->setOrganisation($organisation);
                $contactOrganisation->setContact($contact);
                $contact->setContactOrganisation($contactOrganisation);
            }


            /** Add the contact to the contacts array */
            $this->contacts[] = $contact;
        }
    }

    private function importContacts(array $import = []): void
    {
        foreach ($this->contacts as $key => $contact) {
            if (\in_array($key, $import, false)) {
                if (null === $contact->getId()) {
                    $contact = $this->contactService->save($contact);
                }

                $contactOptIn = new ArrayCollection($this->optIn);
                /** Add the optIn */
                foreach ($contact->getOptIn() as $optIn) {
                    $contactOptIn->removeElement($optIn);
                }
                $contact->addOptIn($contactOptIn);

                if (!$this->selectionContactService->contactInSelection($contact, [$this->selection])) {
                    $selectionContact = new SelectionContact();
                    $selectionContact->setSelection($this->selection);
                    $selectionContact->setContact($contact);
                    $this->selectionService->save($selectionContact);
                }

                $this->importedContacts[] = $contact;
            }
        }
    }

    public function hasWarnings(): bool
    {
        return \count($this->warnings) > 0;
    }


    public function getImportedContacts(): array
    {
        return $this->importedContacts;
    }
}
