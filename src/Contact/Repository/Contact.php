<?php
/**
 * DebraNova copyright message placeholder
 *
 * @category    Contact
 * @package     Repository
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Contact\Repository;

use Contact\Entity;
use Contact\Entity\SelectionSql;
use Contact\Options;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @category    Contact
 * @package     Repository
 */
class Contact extends EntityRepository
{
    /**
     * Return a list of all contacts
     *
     * @param null $limit
     *
     * @return \Doctrine\ORM\Query
     */
    public function findContacts($limit = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('c');
        $qb->from("Contact\Entity\Contact", 'c');
        $qb->distinct('c.id');
        $qb->orderBy('c.id', 'DESC');
        /**
         * Only add a limit when asked
         */
        if (!is_null($limit)) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery();
    }

    /**
     * @param $projectId
     *
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \InvalidArgumentException
     */
    public function findContactByProjectId($projectId)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('c');
        $queryBuilder->from("Contact\Entity\Contact", 'c');
        $queryBuilder->distinct('c.id');
        //Add the associates
        $associates = $this->_em->createQueryBuilder();
        $associates->select('associateContact.id');
        $associates->from('Affiliation\Entity\Affiliation', 'associateAffiliation');
        $associates->join('associateAffiliation.associate', 'associateContact');
        $associates->join('associateAffiliation.project', 'associateProject');
        $associates->andWhere('associateProject.id = ?1');
        //Add the affiliates
        $affiliates = $this->_em->createQueryBuilder();
        $affiliates->select('affiliationContact.id');
        $affiliates->from('Affiliation\Entity\Affiliation', 'affiliation');
        $affiliates->join('affiliation.project', 'affiliationProject');
        $affiliates->join('affiliation.contact', 'affiliationContact');
        $affiliates->andWhere('affiliationProject.id = ?1');
        //Add the workpackage leaders
        $workpackage = $this->_em->createQueryBuilder();
        $workpackage->select('workpackageContact.id');
        $workpackage->from('Project\Entity\Workpackage\Workpackage', 'workpackage');
        $workpackage->join('workpackage.project', 'workpackageProject');
        $workpackage->join('workpackage.contact', 'workpackageContact');
        $workpackage->andWhere('workpackageProject.id = ?1');
        //Add the project leaders
        $projectLeaders = $this->_em->createQueryBuilder();
        $projectLeaders->select('projectContact.id');
        $projectLeaders->from('Project\Entity\Project', 'project');
        $projectLeaders->join('project.contact', 'projectContact');
        $projectLeaders->andWhere('project.id = ?1');
        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->in('c.id', $associates->getDQL()),
                $queryBuilder->expr()->in('c.id', $affiliates->getDQL()),
                $queryBuilder->expr()->in('c.id', $workpackage->getDQL()),
                $queryBuilder->expr()->in('c.id', $projectLeaders->getDQL())
            )
        );
        $queryBuilder->setParameter(1, $projectId);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param $email
     * @param $onlyMain
     *
     * @return Contact|null
     */
    public function findContactByEmail($email, $onlyMain)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('c');
        $queryBuilder->from('Contact\Entity\Contact', 'c');
        $queryBuilder->orWhere('c.email = ?1');
        $queryBuilder->setParameter(1, $email);

        if (!$onlyMain) {
            $queryBuilder->leftJoin('c.emailAddress', 'e');
            $queryBuilder->orWhere('e.email = ?2');
            $queryBuilder->setParameter(2, $email);
        }

        $queryBuilder->setMaxResults(1);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Contact[]
     */
    public function findContactsWithDateOfBirth()
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('c');
        $queryBuilder->from('Contact\Entity\Contact', 'c');
        $queryBuilder->andWhere($queryBuilder->expr()->isNull('c.dateEnd'));
        $queryBuilder->andWhere($queryBuilder->expr()->isNotNull('c.dateOfBirth'));

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     *  Returns true of false depending if a contact is a community member
     *
     * @param Entity\Contact                    $contact
     * @param Options\CommunityOptionsInterface $options
     *
     * @return boolean|null
     */
    public function findIsCommunityMember(Entity\Contact $contact, Options\CommunityOptionsInterface $options)
    {
        if ($options->getCommunityViaMembers()) {
            return false;
        }
        if ($options->getCommunityViaProjectParticipation()) {
            $projectRepository = $this->getEntityManager()->getRepository('Project\Entity\Project');
            /**
             * Go over the associates first
             */
            $queryBuilder = $this->_em->createQueryBuilder();
            $queryBuilder->select('contact.id');
            $queryBuilder->from('Contact\Entity\Contact', 'contact');
            $queryBuilder->join('contact.associate', 'a');
            $queryBuilder = $projectRepository->onlyActiveProjectOrRecentPO($queryBuilder);
            $queryBuilder->andWhere('contact = :contact');
            $queryBuilder->setParameter('contact', $contact);
            //If we find a associate, return true, else proceed
            if (sizeof($queryBuilder->getQuery()->getResult()) > 0) {
                return true;
            }
            /**
             * Go over the affiliations
             */
            $queryBuilder = $this->_em->createQueryBuilder();
            $queryBuilder->select('contact.id');
            $queryBuilder->from('Contact\Entity\Contact', 'contact');
            $queryBuilder->join('contact.contactOrganisation', 'co');
            $queryBuilder->join('co.organisation', 'organisation');
            $queryBuilder->join('organisation.affiliation', 'a');
            $queryBuilder = $projectRepository->onlyActiveProjectOrRecentPO($queryBuilder);
            $queryBuilder->andWhere('contact = :contact');
            $queryBuilder->setParameter('contact', $contact);
            //If we find a associate, return true, else proceed
            if (sizeof($queryBuilder->getQuery()->getResult()) > 0) {
                return true;
            }
            /**
             * Go over the affiliations via the cluster
             */
            $queryBuilder = $this->_em->createQueryBuilder();
            $queryBuilder->select('contact.id');
            $queryBuilder->from('Contact\Entity\Contact', 'contact');
            $queryBuilder->join('contact.contactOrganisation', 'co');
            $queryBuilder->join('co.organisation', 'organisation');
            $queryBuilder->join('organisation.cluster', 'cluster1', 'cluster1.organisation = organisation');
            $queryBuilder->join('organisation.cluster', 'cluster2', 'cluster1.cluster = cluster2.cluster');
            $queryBuilder->join('organisation.affiliation', 'a');
            $queryBuilder = $projectRepository->onlyActiveProjectOrRecentPO($queryBuilder);
            $queryBuilder->andWhere('contact = :contact');
            $queryBuilder->setParameter('contact', $contact);
            //If we find a associate, return true, else proceed
            if (sizeof($queryBuilder->getQuery()->getResult()) > 0) {
                return true;
            }

            return false;
        }
    }

    /**
     * Return Contact entities based on a selection SQL using a native SQL query
     *
     * @param SelectionSql $sql
     *
     * @return Entity\Contact[]
     */
    public function findContactsBySelectionSQL(SelectionSQL $sql)
    {
        $resultSetMap = new ResultSetMapping();
        $resultSetMap->addEntityResult('Contact\Entity\Contact', 'c');
        $resultSetMap->addFieldResult('c', 'contact_id', 'id');
        $resultSetMap->addFieldResult('c', 'email', 'email');
        $resultSetMap->addFieldResult('c', 'firstname', 'firstName');
        $resultSetMap->addFieldResult('c', 'middlename', 'middleName');
        $resultSetMap->addFieldResult('c', 'lastname', 'lastName');
        $query = $this->getEntityManager()->createNativeQuery(
            "SELECT contact_id, email, firstname, middlename, lastname FROM contact WHERE contact_id IN (" .
            $sql->getQuery() . ")",
            $resultSetMap
        );

        return $query->getResult();
    }

    /**
     * Return Contact entities based on a selection SQL using a native SQL query
     *
     * @param Entity\Contact $contact
     * @param SelectionSql   $sql
     *
     * @return bool
     */
    public function isContactInSelectionSQL(Entity\Contact $contact, SelectionSQL $sql)
    {
        $resultSetMap = new ResultSetMapping();
        $resultSetMap->addEntityResult('Contact\Entity\Contact', 'c');
        $resultSetMap->addFieldResult('c', 'contact_id', 'id');
        $query = $this->getEntityManager()->createNativeQuery(
            "SELECT contact_id FROM contact
                                WHERE contact_id IN (" . $sql->getQuery() . ") AND contact_id = " . $contact->getId(),
            $resultSetMap
        );

        return sizeof($query->getResult()) > 0;
    }

    /**
     * This is basic search for contacts (based on the name, and email
     *
     * @param string $searchItem
     * @param int    $maxResults
     *
     * @return Entity\Contact[]
     */
    public function searchContacts($searchItem, $maxResults = 12)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('c');
        $qb->from("Contact\Entity\Contact", 'c');
        $qb->distinct('c.id');
        $qb->andWhere('c.firstName LIKE :searchItem OR c.lastName LIKE :searchItem OR c.email LIKE :searchItem');
        $qb->setParameter('searchItem', "%" . $searchItem . "%");
        $qb->setMaxResults($maxResults);
        $qb->orderBy('c.id', 'DESC');

        return $qb->getQuery()->getResult();
    }
}
