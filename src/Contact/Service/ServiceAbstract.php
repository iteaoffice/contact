<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */

namespace Contact\Service;

use Admin\Service\AdminService;
use Admin\Service\AdminServiceAwareInterface;
use Contact\Entity\Contact;
use Contact\Entity\EntityAbstract;
use Contact\Entity\Selection;
use Deeplink\Service\DeeplinkService;
use Deeplink\Service\DeeplinkServiceAwareInterface;
use Event\Service\MeetingService;
use General\Service\EmailService;
use General\Service\EmailServiceAwareInterface;
use General\Service\GeneralService;
use General\Service\GeneralServiceAwareInterface;
use Organisation\Service\OrganisationService;
use Organisation\Service\OrganisationServiceAwareInterface;
use Project\Service\ProjectService;
use Project\Service\ProjectServiceAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * ServiceAbstract.
 */
abstract class ServiceAbstract implements
    ServiceLocatorAwareInterface,
    ServiceInterface,
    DeeplinkServiceAwareInterface,
    EmailServiceAwareInterface,
    GeneralServiceAwareInterface,
    OrganisationServiceAwareInterface,
    ProjectServiceAwareInterface,
    AdminServiceAwareInterface
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
     * @var DeeplinkService
     */
    protected $deeplinkService;
    /**
     * @var ProjectService
     */
    protected $projectService;
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
     * @param   $entity
     *
     * @return \Contact\Entity\OptIn[]
     */
    public function findAll($entity)
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName($entity))->findAll();
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if (null === $this->entityManager) {
            $this->entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }

        return $this->entityManager;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return \Zend\ServiceManager\ServiceManager
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
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
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
            $entity = $entity[0].ucfirst($entity[1]);
        }

        return ucfirst(implode('', array_slice(explode('\\', __NAMESPACE__), 0, 1))).'\\'.'Entity'.'\\'.ucfirst(
            $entity
        );
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
     * @param \Contact\Entity\EntityAbstract $entity
     *
     * @return \Contact\Entity\EntityAbstract
     */
    public function newEntity(EntityAbstract $entity)
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        return $entity;
    }

    /**
     * @param \Contact\Entity\EntityAbstract $entity
     *
     * @return \Contact\Entity\EntityAbstract
     */
    public function updateEntity(EntityAbstract $entity)
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        return $entity;
    }

    /**
     * @param \Contact\Entity\EntityAbstract $entity
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
    public function setGeneralService(GeneralService $generalService)
    {
        $this->generalService = $generalService;

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
    public function setDeeplinkService(DeeplinkService $deeplinkService)
    {
        $this->deeplinkService = $deeplinkService;

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
    public function setEmailService(EmailService $emailService)
    {
        $this->emailService = $emailService;

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
    public function setOrganisationService(OrganisationService $organisationService)
    {
        $this->organisationService = $organisationService;

        return $this;
    }

    /**
     * @return MeetingService
     */
    public function getMeetingService()
    {
        return $this->getServiceLocator()->get(MeetingService::class);
    }

    /**
     * @param MeetingService $meetingService
     *
     * @return ServiceAbstract
     */
    public function setMeetingService(MeetingService $meetingService)
    {
        $this->meetingService = $meetingService;

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
    public function setProjectService(ProjectService $projectService)
    {
        $this->projectService = $projectService;

        return $this;
    }

    /**
     * @return adminService
     */
    public function getAdminService()
    {
        return $this->adminService;
    }

    /**
     * @param adminService $adminService
     *
     * @return ServiceAbstract
     */
    public function setAdminService(adminService $adminService)
    {
        $this->adminService = $adminService;

        return $this;
    }

    /**
     * @return selectionService
     */
    public function getSelectionService()
    {
        return $this->selectionService;
    }

    /**
     * @param selectionService $selectionService
     *
     * @return ServiceAbstract
     */
    public function setSelectionService(selectionService $selectionService)
    {
        $this->selectionService = $selectionService;

        return $this;
    }

    /**
     * @return ContactService
     */
    public function getContactService()
    {
        return $this->getServiceLocator()->get('contact_contact_service');
    }
}
