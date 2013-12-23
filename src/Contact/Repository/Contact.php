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

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;

use Contact\Entity;
use Contact\Options;
use Contact\Entity\SelectionSql;

use Project\Repository\Project;

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
     * @param $email
     *
     * @return Contact|null
     */
    public function findContactByEmail($email)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('c');
        $queryBuilder->from('Contact\Entity\Contact', 'c');
        $queryBuilder->leftJoin('c.emailAddress', 'e');

        $queryBuilder->orWhere('c.email = ?1');
        $queryBuilder->orWhere('e.email = ?2');
        $queryBuilder->setParameter(1, $email);
        $queryBuilder->setParameter(2, $email);
        $queryBuilder->setMaxResults(1);

        $result = $queryBuilder->getQuery()->getResult();

        //Limit to 1 to have only 1 match
        if (sizeof($result) > 0) {
            return $result[0];
        } else {
            return null;
        }
    }

    /**
     *  Returns true of false depending if a contact is a community member
     *
     * @param Entity\Contact                    $contact
     * @param Options\CommunityOptionsInterface $options
     *
     * @return bool
     */
    public function findIsCommunityMember(Entity\Contact $contact, Options\CommunityOptionsInterface $options)
    {
        if ($options->getCommunityViaMembers()) {
            //Todo Artemisia
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
        $query = $this->getEntityManager()->createNativeQuery("SELECT contact_id, email FROM contact
                    WHERE contact_id IN (" . $sql->getQuery() . ")", $resultSetMap);

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
        $query = $this->getEntityManager()->createNativeQuery("SELECT contact_id FROM contact
                    WHERE contact_id IN (" . $sql->getQuery() . ") AND contact_id = " . $contact->getId(),
            $resultSetMap);

        if (sizeof($query->getResult()) > 0) {
            return true;
        } else {
            return false;
        }
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
        $qb->select('p');
        $qb->from("Contact\Entity\Contact", 'c');
        $qb->distinct('c.id');

        $qb->andWhere('c.firstName LIKE :searchItem OR c.lastName LIKE :searchItem OR p.emailAddress LIKE :searchItem');

        $qb->setParameter('searchItem', "%" . $searchItem . "%");

        $qb->setMaxResults($maxResults);

        $qb->orderBy('p.id', 'DESC');

        return $qb->getQuery()->getResult();
    }
}
