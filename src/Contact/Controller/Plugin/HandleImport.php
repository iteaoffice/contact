<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */

namespace Contact\Controller\Plugin;

use Contact\Entity\Contact;
use Contact\Service\ContactService;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Validator\EmailAddress;

/**
 * Class HandleImport.
 */
class HandleImport extends AbstractPlugin implements ServiceLocatorAwareInterface
{
    /**
     * @var string
     */
    protected $delimiter = ',';
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
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @param $data
     *
     * @return $this
     */
    public function __invoke($data)
    {
        $this->setData($data);

        $this->validateData();

        if (!$this->hasErrors()) {
            $this->importContent();
        }
    }

    /**
     * Body function, creating the contactObjects.
     */
    private function importContent()
    {
        foreach ($this->content as $content) {
            //See first if the contact can be found
            $contact = $this->getContactService()->findContactByEmail($content[$this->headerKeys['email']]);

            if (!is_null($contact)) {
                $this->contacts[] = $contact;
            }
        }
    }

    /**
     * With this function we will do some basic testing to see if the least amount of information is available.
     */
    protected function validateData()
    {
        $minimalRequiredElements = ['email', 'firstname', 'lastname'];

        /*
         * Go over all elements and check if the required elements are present
         */
        foreach ($minimalRequiredElements as $element) {
            if (!in_array($element, $this->header)) {
                $this->errors[] = sprintf("Element %s is missing in the file", $element);
            }
        }

        /*
         * Create the lookup-table
         */
        $this->headerKeys = array_flip($this->header);

        /*
         * Validate the emails.
         */
        $counter = 2;
        foreach ($this->content as $content) {
            $validate = new EmailAddress();
            if (!$validate->isValid($content[$this->headerKeys['email']])) {
                $this->errors[] = sprintf(
                    "EmailAddress (%s) in row %s is invalid",
                    $content[$this->headerKeys['email']],
                    $counter++
                );
            }
            $counter++;
        }
    }

    /**
     * $this function extracts the data and created local arrays.
     *
     * @param $data
     */
    private function setData($data)
    {
        //Explode first on the \n to have the different rows
        $data = explode("\n", $data);

        /*
         * Correct first the delimiter, normally a ; but it can be a ;
         */
        if (strpos($data[0], ';') !== false) {
            $this->delimiter = ';';
        }

        $this->header = explode($this->delimiter, $data[0]);

        /*
         * Go over the rest of the data and add the rows to the array
         */
        $amount = sizeof($data);
        for ($i = 1; $i < $amount; $i++) {
            $row = explode($this->delimiter, $data[$i]);

            if (sizeof($row) === sizeof($this->header)) {
                $this->content[] = $row;
            } else {
                $this->warnings[] = sprintf(
                    "Row %s has been skipped, does not contain %s elements but %s",
                    $i + 1,
                    sizeof($this->header),
                    sizeof($row)
                );
            }
        }
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return sizeof($this->errors) > 0;
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
    public function hasWarnings()
    {
        return sizeof($this->warnings) > 0;
    }

    /**
     * @return array
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator->getServiceLocator();
    }

    /**
     * @return ContactService
     */
    public function getContactService()
    {
        return $this->getServiceLocator()->get('contact_contact_service');
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
     * @return Contact[]
     */
    public function getContacts()
    {
        return $this->contacts;
    }
}
