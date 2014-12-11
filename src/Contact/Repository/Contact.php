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

use Calendar\Entity\Calendar;
use Contact\Entity;
use Contact\Entity\Selection;
use Contact\Entity\SelectionSql;
use Contact\Options;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Organisation\Entity\Organisation;

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
        $queryBuilder = $this->findContactByProjectIdQueryBuilder();
        $queryBuilder->setParameter(1, $projectId);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findContactByProjectIdQueryBuilder()
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

        return $queryBuilder;
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
     * @return Contact[]
     */
    public function findContactsWithCV()
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('c');
        $queryBuilder->from('Contact\Entity\Contact', 'c');
        $queryBuilder->andWhere($queryBuilder->expr()->isNull('c.dateEnd'));
        $queryBuilder->innerJoin('c.cv', 'cv');

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
            /**
             * @var $memberRepository \Member\Repository\Member
             */
            $memberRepository = $this->getEntityManager()->getRepository('Member\Entity\Member');
            $queryBuilder = $this->_em->createQueryBuilder();
            $queryBuilder->select('contact.id');
            $queryBuilder->from('Contact\Entity\Contact', 'contact');
            $queryBuilder->join('contact.contactOrganisation', 'co');
            $queryBuilder->join('co.organisation', 'organisation');
            $queryBuilder->join('organisation.clusterMember', 'cluster');
            $queryBuilder->join('cluster.organisation', 'organisation2');
            $queryBuilder->join('organisation2.member', 'm');
            $queryBuilder = $memberRepository->onlyActiveMember($queryBuilder);
            $queryBuilder->andWhere('co.contact = :contact');
            $queryBuilder->setParameter('contact', $contact);

            //check update
            if (sizeof($queryBuilder->getQuery()->getResult()) > 0) {
                return true;
            }

            $queryBuilder = $this->_em->createQueryBuilder();
            $queryBuilder->select('contact.id');
            $queryBuilder->from('Contact\Entity\Contact', 'contact');
            $queryBuilder->join('contact.contactOrganisation', 'co');
            $queryBuilder->join('co.organisation', 'organisation');
            $queryBuilder->join('organisation.member', 'm');
            $queryBuilder = $memberRepository->onlyActiveMember($queryBuilder);
            $queryBuilder->andWhere('contact = :contact');
            $queryBuilder->setParameter('contact', $contact);
            //check update
            if (sizeof($queryBuilder->getQuery()->getResult()) > 0) {

                return true;
            }



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
            $sql->getQuery() . ") AND date_end IS NULL",
            $resultSetMap
        );

        return $query->getResult();
    }

    /**
     * Return Contact entities based on a query generated by the Facebook functionality
     *
     * @param Entity\Facebook $facebook
     *
     * @return Entity\Contact[]
     */
    public function findContactsInFacebook(Entity\Facebook $facebook)
    {
        $resultSetMap = new ResultSetMapping();
        $resultSetMap->addEntityResult('Contact\Entity\Contact', 'c');
        $resultSetMap->addFieldResult('c', 'contact_id', 'id');
        $resultSetMap->addFieldResult('c', 'email', 'email');
        $resultSetMap->addFieldResult('c', 'firstname', 'firstName');
        $resultSetMap->addFieldResult('c', 'middlename', 'middleName');
        $resultSetMap->addFieldResult('c', 'lastname', 'lastName');
        $resultSetMap->addFieldResult('c', 'position', 'position');

        $queryInString = sprintf(
            "SELECT %s FROM %s WHERE %s",
            $facebook->getContactKey(),
            $facebook->getFromClause(),
            $facebook->getWhereClause()
        );

        $orderBy = null;
        if (null !== $facebook->getOrderbyClause()) {
            $orderBy = sprintf(" ORDER BY %s", $facebook->getOrderbyClause());
        }


        $query = $this->getEntityManager()->createNativeQuery(
            sprintf(
                "SELECT contact_id, email, firstname, middlename, lastname, position FROM contact WHERE contact_id IN (%s) AND date_end IS NULL %s ",
                $queryInString,
                $orderBy
            ),
            $resultSetMap
        );

        return $query->getResult();
    }


    /**
     * Return Contact entities based on a selection SQL using a native SQL query
     *
     * @param Selection $selection
     *
     * @return Entity\Contact[]
     */
    public function findContactsBySelectionContact(Selection $selection)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('c');
        $qb->from("Contact\Entity\Contact", 'c');
        $qb->join('c.selectionContact', 'sc');
        $qb->distinct('c.id');
        $qb->andWhere($qb->expr()->isNull('c.dateEnd'));
        $qb->andWhere('sc.selection = ?1');
        $qb->setParameter(1, $selection->getId());
        $qb->orderBy('c.lastName');

        return $qb->getQuery()->getResult();
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
            "SELECT
             contact_id
             FROM contact
             WHERE contact_id
             IN (" . $sql->getQuery() . ") AND contact_id = " . $contact->getId(),
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
        $qb->select(['c.id', 'c.firstName', 'c.middleName', 'c.lastName', 'c.email']);
        $qb->from("Contact\Entity\Contact", 'c');
        $qb->distinct('c.id');

        $qb->where(
            $qb->expr()->orX(
                $qb->expr()->like(
                    $qb->expr()->concat(
                        'c.firstName',
                        $qb->expr()->concat($qb->expr()->literal(' '), 'c.middleName'),
                        $qb->expr()->concat($qb->expr()->literal(' '), 'c.lastName')
                    ),
                    $qb->expr()->literal("%" . $searchItem . "%")
                ),
                $qb->expr()->like('c.email', $qb->expr()->literal("%" . $searchItem . "%"))
            )
        );

        $qb->orderBy('c.lastName', 'ASC');

        $qb->setMaxResults($maxResults);
        return $qb->getQuery()->getArrayResult();
    }

    /**
     * @param  Organisation $organisation
     * @return Contact[]
     */
    public function findContactsInOrganisation(Organisation $organisation)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('c');
        $qb->from("Contact\Entity\Contact", 'c');
        $qb->addOrderBy('c.lastName', 'ASC');

        //Select the contacts based on their organisations
        $subSelect = $this->_em->createQueryBuilder();
        $subSelect->select('contact');
        $subSelect->from('Contact\Entity\ContactOrganisation', 'co');
        $subSelect->join('co.organisation', 'o');
        $subSelect->join('co.contact', 'contact');
        $subSelect->where('o.id = ?1');
        $qb->setParameter(1, $organisation->getId());

        $qb->andWhere($qb->expr()->isNull('c.dateEnd'));
        $qb->andWhere($qb->expr()->in('c', $subSelect->getDQL()));

        return $qb->getQuery()->getResult();
    }

    /**
     * @param  Calendar $calendar
     * @return Entity\Contact[]
     */
    public function findPossibleContactByCalendar(Calendar $calendar)
    {
        /**
         * Use the contactQueryBuilder and exclude the ones which are already present based on the roles
         */
        $findContactByProjectIdQueryBuilder = $this->findContactByProjectIdQueryBuilder();

        //Find the reviewers
        $findReviewContactByProjectQueryBuilder = $this->getEntityManager()->getRepository(
            'Project\Entity\Review\Review'
        )->findReviewContactByProjectQueryBuilder();

        //Remove all the contacts which are already in the project as associate or otherwise affected
        $findContactByProjectIdQueryBuilder->andWhere(
            $findContactByProjectIdQueryBuilder->expr()->notIn(
                'c',
                $findReviewContactByProjectQueryBuilder->getDQL()
            )
        );

        $findContactByProjectIdQueryBuilder->setParameter(1, $calendar->getProjectCalendar()->getProject()->getId());
        $findContactByProjectIdQueryBuilder->setParameter('project', $calendar->getProjectCalendar()->getProject());
        $findContactByProjectIdQueryBuilder->addOrderBy('c.lastName', 'ASC');

        return $findContactByProjectIdQueryBuilder->getQuery()->getResult();
    }
}
