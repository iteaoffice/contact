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
use Contact\Service\SelectionService;
use Doctrine\Common\Collections\ArrayCollection;
use General\Entity\Gender;
use General\Entity\Title;
use General\Service\GeneralService;
use Organisation\Entity\Organisation;
use Organisation\Entity\Type;
use Organisation\Entity\Web;
use Organisation\Service\OrganisationService;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Validator\EmailAddress;

/**
 * Class HandleImport.
 */
class HandleImport extends AbstractPlugin
{
    /**
     * @var array
     */
    protected $header = [];
    /**
     * Inverse lookup-array which keeps the keys of the columns.
     *
     * @var array
     */
    protected $headerKeys = [];
    /**
     * @var array
     */
    protected $content = [];
    /**
     * @var array
     */
    protected $errors = [];
    /**
     * @var array
     */
    protected $warnings = [];
    /**
     * @var Contact[]
     */
    protected $contacts = [];
    /**
     * @var Contact[]
     */
    protected $importedContacts = [];
    /**
     * @var OptIn[]
     */
    protected $optIn = [];
    /**
     * @var Selection
     */
    protected $selection;
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;
    /**
     * @var string
     */
    private $delimiter = "\t";

    /**
     * @param       $data
     * @param array $import
     * @param array $includeOptIn
     * @param null  $selectionId
     * @param null  $selectionName
     *
     * @return $this
     */
    public function __invoke(
        $data,
        $import = [],
        $includeOptIn = [],
        $selectionId = null,
        $selectionName = null
    ): self {
        $this->setData($data);

        $this->validateData();

        /** set the selection */
        $this->setSelectionFromFromData($selectionId, $selectionName);

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

    /**
     * $this function extracts the data and created local arrays.
     *
     * @param $data
     */
    private function setData($data): void
    {
        $data = utf8_encode($data);

        //Explode first on the \n to have the different rows
        $data = \explode(PHP_EOL, $data);

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
                    count($this->header),
                    count($row)
                );
            }
        }
    }

    /**
     * With this function we will do some basic testing to see if the least amount of information is available.
     */
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
                    "EmailAddress (%s) in row %s is invalid",
                    $content[$this->headerKeys['email']],
                    $counter
                );
            }

            /**
             * Validate the organisation_id
             */
            if (!empty($this->headerKeys['organisation_id'])) {
                $organisation = $this->getOrganisationService()
                    ->findOrganisationById($this->headerKeys['organisation_id']);
                if (null === $organisation) {
                    $this->errors[] = sprintf(
                        "Organisation with ID (%s) in row %s cannot be found",
                        $content[$this->headerKeys['organisation_id']],
                        $counter
                    );
                }
            }

            /**
             * Validate the country
             */
            if (!empty($this->headerKeys['country'])) {
                $country = $this->getGeneralService()->findCountryByName($content[$this->headerKeys['country']]);
                if (null === $country) {
                    $this->warnings[] = sprintf(
                        "Country (%s) in row %s cannot be found",
                        $content[$this->headerKeys['country']],
                        $counter
                    );
                }
            }

            $counter++;
        }
    }

    /**
     * @return OrganisationService
     */
    public function getOrganisationService(): OrganisationService
    {
        return $this->getServiceLocator()->get(OrganisationService::class);
    }

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator(): ServiceLocatorInterface
    {
        return $this->serviceLocator;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return $this
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * @return GeneralService
     */
    public function getGeneralService(): GeneralService
    {
        return $this->getServiceLocator()->get(GeneralService::class);
    }

    /**
     * @param $selectionId
     * @param $selectionName
     */
    private function setSelectionFromFromData($selectionId, $selectionName): void
    {
        if (empty($selectionId) && empty($selectionName)) {
            return;
        }

        /** Parse the $selectionId if not empty */
        if (!empty($selectionId)) {
            $selection = $this->getSelectionService()->findSelectionById($selectionId);
            if (null !== $selection) {
                $this->setSelection($selection);
            }
        }

        if (!empty($selectionName)) {
            $selection = new Selection();
            $selection->setSelection($selectionName);
            $selection->setContact($this->getServiceLocator()->get(AuthenticationService::class)->getIdentity());

            $this->getContactService()->newEntity($selection);
            $this->setSelection($selection);
        }
    }

    /**
     * @return SelectionService
     */
    public function getSelectionService(): SelectionService
    {
        return $this->getServiceLocator()->get(SelectionService::class);
    }

    /**
     * @return ContactService
     */
    public function getContactService(): ContactService
    {
        return $this->getServiceLocator()->get(ContactService::class);
    }

    /**
     * @param array $includeOptIn
     *
     * @return null
     */
    private function setOptInFromFormData($includeOptIn): void
    {
        foreach ($includeOptIn as $optInId) {
            $optIn = $this->getContactService()->findEntityById(OptIn::class, $optInId);
            if (null !== $optIn) {
                $this->optIn[] = $optIn;
            }
        }
    }

    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return \count($this->errors) > 0;
    }

    /**
     * Body function, creating the contactObjects.
     */
    private function prepareContent()
    {
        foreach ($this->content as $key => $content) {
            //See first if the contact can be found
            $contact = $this->getContactService()->findContactByEmail($content[$this->headerKeys['email']]);

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
                $gender = $this->getGeneralService()->findGenderByGender($content[$this->headerKeys['gender']]);
                $contact->setGender($gender);
            }

            if (null === $gender) {
                $gender = $this->getGeneralService()->find(Gender::class, Gender::GENDER_UNKNOWN);
            }

            $contact->setGender($gender);

            if (isset($this->headerKeys['title']) && !empty($content[$this->headerKeys['title']])) {
                $title = $this->getGeneralService()->findTitleByTitle($content[$this->headerKeys['title']]);
                $contact->setTitle($title);
            }

            if (null === $title) {
                $title = $this->getGeneralService()->find(Title::class, Title::TITLE_UNKNOWN);
            }

            $contact->setTitle($title);

            if (isset($this->headerKeys['phone']) && !empty($content[$this->headerKeys['phone']])) {
                $contact->setPosition($content[$this->headerKeys['phone']]);
            }

            //If found, set the phone number
            if (isset($this->headerKeys['phone']) && !empty($content[$this->headerKeys['phone']])) {
                $phone = new Phone();

                /** @var PhoneType $phoneType */
                $phoneType = $this->getContactService()->findEntityById(PhoneType::class, PhoneType::PHONE_TYPE_DIRECT);
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
                $country = $this->getGeneralService()->findCountryByName($content[$this->headerKeys['country']]);
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
                $organisation = $this->getOrganisationService()
                    ->findOrganisationById($content[$this->headerKeys['organisation_id']]);
            }

            if (null === $organisation && null !== $country && null !== $organisationName) {
                $organisation = $this->getOrganisationService()->findOrganisationByNameCountry(
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
                $organisationType = $this->getOrganisationService()->findEntityById(Type::class, Type::TYPE_UNKNOWN);
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

                if (isset($this->headerKeys['website']) && !\is_null($content[$this->headerKeys['website']])) {
                    //Strip the http:// and https://
                    $website = \str_replace('http://', '', $content[$this->headerKeys['website']]);
                    $website = \str_replace('https://', '', $website);

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

    /**
     * @param $import
     */
    private function importContacts($import): void
    {
        foreach ($this->contacts as $key => $contact) {
            if (\in_array($key, $import, false)) {
                if (null === $contact->getId()) {
                    $contact = $this->getContactService()->newEntity($contact);
                }

                $contactOptIn = new ArrayCollection($this->optIn);
                /** Add the optIn */
                foreach ($contact->getOptIn() as $optIn) {
                    $contactOptIn->removeElement($optIn);
                }
                $contact->addOptIn($contactOptIn);

                if (!$this->getContactService()->contactInSelection($contact, [$this->getSelection()])) {
                    $selectionContact = new SelectionContact();
                    $selectionContact->setSelection($this->getSelection());
                    $selectionContact->setContact($contact);
                    $this->getSelectionService()->newEntity($selectionContact);
                }

                $this->importedContacts[] = $contact;
            }
        }
    }

    /**
     * @return Selection
     */
    public function getSelection()
    {
        return $this->selection;
    }

    /**
     * @param Selection $selection
     *
     * @return HandleImport
     */
    public function setSelection($selection): HandleImport
    {
        $this->selection = $selection;

        return $this;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function hasWarnings(): bool
    {
        return \count($this->warnings) > 0;
    }

    /**
     * @return array
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * @return Contact[]
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * @return \Contact\Entity\Contact[]
     */
    public function getImportedContacts()
    {
        return $this->importedContacts;
    }
}
