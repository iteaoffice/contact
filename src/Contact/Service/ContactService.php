<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Contact
 * @package     Service
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
namespace Contact\Service;

use Contact\Entity\Contact;

use Project\Service\ProjectService;

use Organisation\Service\OrganisationService;

/**
 * ContactService
 *
 * this is a generic wrapper service for all the other services
 *
 * First parameter of all methods (lowercase, underscore_separated)
 * will be used to fetch the correct model service, one exception is the 'linkModel'
 * method.
 *
 */
class ContactService extends ServiceAbstract
{
    /**
     * @var ProjectService
     */
    protected $projectService;
    /**
     * @var OrganisationService
     */
    protected $organisationService;
    /**
     * @var Contact
     */
    protected $contact;

    /** @param int $id
     *
     * @return ContactService;
     */
    public function setContactId($id)
    {
        $this->setContact($this->findEntityById('contact', $id));

        return $this;
    }

    /**
     * @param $email
     *
     * @return null|Contact
     */
    public function findContactByEmail($email)
    {
        return $this->getEntityManager()
            ->getRepository($this->getFullEntityName('contact'))
            ->findContactByEmail($email);
    }

    /**
     * Find a contact based on a hash
     *
     * @param $hash
     *
     * @return null|Contact
     */
    public function findContactByHash($hash)
    {
        $contact   = new Contact();
        $contactId = $contact->decryptHash($hash);

        return $this->findEntityById('contact', $contactId);
    }

    /**
     * @return OrganisationService
     */
    public function findOrganisationService()
    {
        return $this->getOrganisationService()->setOrganisationId(
            $this->getContact()->getContactOrganisation()->getOrganisation()->getId());
    }


    /**
     * @return \Project\Service\ProjectService[]
     */
    public function findProjects()
    {
        return $this->getProjectService()->findProjectByContact($this->getContact());
    }

    /**
     * @param Contact $contact
     *
     * @return ContactService
     */
    private function createServiceElement(Contact $contact)
    {
        $contactService = new self();
        $contactService->setServiceLocator($this->getServiceLocator());
        $contactService->setContact($contact);

        return $contactService;
    }

    /**
     * @param \Contact\Entity\Contact $contact
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return \Contact\Entity\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param ProjectService $projectService
     */
    public function setProjectService($projectService)
    {
        $this->projectService = $projectService;
    }

    /**
     * @return ProjectService
     */
    public function getProjectService()
    {
        if (!$this->projectService instanceof ProjectService) {
            $this->setProjectService($this->getServiceLocator()->get('project_project_service'));
        }

        return $this->projectService;
    }

    /**
     * @param OrganisationService $organisationService
     */
    public function setOrganisationService($organisationService)
    {
        $this->organisationService = $organisationService;
    }

    /**
     * @return OrganisationService
     */
    public function getOrganisationService()
    {
        if (!$this->organisationService instanceof OrganisationService) {
            $this->setOrganisationService($this->getServiceLocator()->get('organisation_organisation_service'));
        }

        return $this->organisationService;
    }
}
