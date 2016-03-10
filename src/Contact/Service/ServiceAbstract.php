<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Contact\Service;

use Admin\Service\AdminService;
use Contact\Entity\Address;
use Contact\Entity\Contact;
use Contact\Entity\EntityAbstract;
use Contact\Entity\Selection;
use Contact\Options\ModuleOptions;
use Deeplink\Service\DeeplinkService;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Event\Service\MeetingService;
use General\Service\EmailService;
use General\Service\GeneralService;
use Organisation\Service\OrganisationService;
use Project\Service\ProjectService;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcUser\Options\UserServiceOptionsInterface;

/**
 * ServiceAbstract.
 */
abstract class ServiceAbstract implements ServiceInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;
    /**
     * @var ModuleOptions
     */
    protected $moduleOptions;
    /**
     * @var DeeplinkService
     */
    protected $deeplinkService;
    /**
     * @var ProjectService
     */
    protected $projectService;
    /**
     * @var ContactService
     */
    protected $contactService;
    /**
     * @var SelectionService
     */
    protected $selectionService;
    /**
     * @var MeetingService
     */
    protected $meetingService;
    /**
     * @var OrganisationService
     */
    protected $organisationService;
    /**
     * @var GeneralService
     */
    protected $generalService;
    /**
     * @var EmailService
     */
    protected $emailService;
    /**
     * @var AdminService
     */
    protected $adminService;
    /**
     * @var AddressService
     */
    protected $addressService;
    /**
     * @var UserServiceOptionsInterface
     */
    protected $zfcUserOptions;
    /**
     * @var Contact
     */
    protected $contact;
    /**
     * @var Address
     */
    protected $address;
    /**
     * @var Selection
     */
    protected $selection;


    /**
     * @param   $entity
     *
     * @return \Contact\Entity\OptIn[]
     */
    public function findAll($entity)
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName($entity))->findAll();
    }

    /**
     * @param string $entity
     * @param        $filter
     *
     * @return Query
     */
    public function findEntitiesFiltered($entity, $filter)
    {
        $equipmentList = $this->getEntityManager()->getRepository($this->getFullEntityName($entity))
            ->findFiltered($filter, AbstractQuery::HYDRATE_SIMPLEOBJECT);

        return $equipmentList;
    }

    /**
     * Create a full path to the entity for Doctrine.
     *
     * @param $entity
     *
     * @return string
     */
    public function getFullEntityName($entity)
    {
        /*
         * Convert a - to a camelCased situation
         */
        if (strpos($entity, '-') !== false) {
            $entity = explode('-', $entity);
            $entity = $entity[0] . ucfirst($entity[1]);
        }

        return ucfirst(implode('', array_slice(explode('\\', __NAMESPACE__), 0, 1))) . '\\' . 'Entity' . '\\'
        . ucfirst($entity);
    }

    /**
     * Find 1 entity based on the id.
     *
     * @param string $entity
     * @param        $id
     *
     * @return null|Contact|Selection
     */
    public function findEntityById($entity, $id)
    {
        return $this->getEntityManager()->find($this->getFullEntityName($entity), $id);
    }

    /**
     * @param EntityAbstract $entity
     *
     * @return EntityAbstract
     */
    public function newEntity(EntityAbstract $entity)
    {
        return $this->updateEntity($entity);
    }

    /**
     * @param EntityAbstract $entity
     *
     * @return EntityAbstract
     */
    public function updateEntity(EntityAbstract $entity)
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        return $entity;
    }

    /**
     * @param EntityAbstract $entity
     *
     * @return bool
     */
    public function removeEntity(EntityAbstract $entity)
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();

        return true;
    }

    /**
     * Build dynamically a entity based on the full entity name.
     *
     * @param $entity
     *
     * @return mixed
     */
    public function getEntity($entity)
    {
        $entity = $this->getFullEntityName($entity);

        return new $entity();
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     *
     * @return ServiceAbstract
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;

        return $this;
    }

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ServiceAbstract
     */
    public function setServiceLocator($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * @return ModuleOptions
     */
    public function getModuleOptions()
    {
        return $this->moduleOptions;
    }

    /**
     * @param ModuleOptions $moduleOptions
     *
     * @return ServiceAbstract
     */
    public function setModuleOptions($moduleOptions)
    {
        $this->moduleOptions = $moduleOptions;

        return $this;
    }

    /**
     * @return DeeplinkService
     */
    public function getDeeplinkService()
    {
        return $this->deeplinkService;
    }

    /**
     * @param DeeplinkService $deeplinkService
     *
     * @return ServiceAbstract
     */
    public function setDeeplinkService($deeplinkService)
    {
        $this->deeplinkService = $deeplinkService;

        return $this;
    }

    /**
     * @return ProjectService
     */
    public function getProjectService()
    {
        return $this->projectService;
    }

    /**
     * @param ProjectService $projectService
     *
     * @return ServiceAbstract
     */
    public function setProjectService($projectService)
    {
        $this->projectService = $projectService;

        return $this;
    }

    /**
     * @return SelectionService
     */
    public function getSelectionService()
    {
        return $this->selectionService;
    }

    /**
     * @param SelectionService $selectionService
     *
     * @return ServiceAbstract
     */
    public function setSelectionService($selectionService)
    {
        $this->selectionService = $selectionService;

        return $this;
    }

    /**
     * @return MeetingService
     */
    public function getMeetingService()
    {
        if (is_null($this->meetingService)) {
            $this->meetingService = $this->getServiceLocator()->get(MeetingService::class);
        }

        return $this->meetingService;
    }

    /**
     * @param MeetingService $meetingService
     *
     * @return ServiceAbstract
     */
    public function setMeetingService($meetingService)
    {
        $this->meetingService = $meetingService;

        return $this;
    }

    /**
     * @return OrganisationService
     */
    public function getOrganisationService()
    {
        return $this->organisationService;
    }

    /**
     * @param OrganisationService $organisationService
     *
     * @return ServiceAbstract
     */
    public function setOrganisationService($organisationService)
    {
        $this->organisationService = $organisationService;

        return $this;
    }

    /**
     * @return GeneralService
     */
    public function getGeneralService()
    {
        return $this->generalService;
    }

    /**
     * @param GeneralService $generalService
     *
     * @return ServiceAbstract
     */
    public function setGeneralService($generalService)
    {
        $this->generalService = $generalService;

        return $this;
    }

    /**
     * @return EmailService
     */
    public function getEmailService()
    {
        return $this->emailService;
    }

    /**
     * @param EmailService $emailService
     *
     * @return ServiceAbstract
     */
    public function setEmailService($emailService)
    {
        $this->emailService = $emailService;

        return $this;
    }

    /**
     * @return AdminService
     */
    public function getAdminService()
    {
        return $this->adminService;
    }

    /**
     * @param AdminService $adminService
     *
     * @return ServiceAbstract
     */
    public function setAdminService($adminService)
    {
        $this->adminService = $adminService;

        return $this;
    }

    /**
     * @return AddressService
     */
    public function getAddressService()
    {
        return $this->addressService;
    }

    /**
     * @param AddressService $addressService
     *
     * @return ServiceAbstract
     */
    public function setAddressService($addressService)
    {
        $this->addressService = $addressService;

        return $this;
    }

    /**
     * @return UserServiceOptionsInterface
     */
    public function getZfcUserOptions()
    {
        return $this->zfcUserOptions;
    }

    /**
     * @param UserServiceOptionsInterface $zfcUserOptions
     *
     * @return ServiceAbstract
     */
    public function setZfcUserOptions($zfcUserOptions)
    {
        $this->zfcUserOptions = $zfcUserOptions;

        return $this;
    }

    /**
     * @return Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param Contact $contact
     *
     * @return ServiceAbstract
     */
    public function setContact($contact)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param Address $address
     *
     * @return ServiceAbstract
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
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
     * @return ServiceAbstract
     */
    public function setSelection($selection)
    {
        $this->selection = $selection;

        return $this;
    }

    /**
     * @return ContactService
     */
    public function getContactService()
    {
        return $this->contactService;
    }

    /**
     * @param ContactService $contactService
     *
     * @return ServiceAbstract
     */
    public function setContactService($contactService)
    {
        $this->contactService = $contactService;

        return $this;
    }
}
