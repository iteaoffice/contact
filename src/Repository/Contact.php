<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Contact\Repository;

use Calendar\Entity\Calendar;
use Contact\Entity;
use Contact\Entity\Selection;
use Contact\Entity\SelectionSql;
use Contact\Options;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMapping;
use Organisation\Entity\Organisation;
use Project\Repository\Project;

/**
 * @category    Contact
 */
class Contact extends EntityRepository
{
    /**
     * Return a list of all contacts.
     *
     * @param null $limit
     *
     * @return \Doctrine\ORM\Query
     */
    public function findContacts($limit = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('c');
        $qb->from(Entity\Contact::class, 'c');
        $qb->distinct('c.id');
        $qb->orderBy('c.id', 'DESC');
        /*
         * Only add a limit when asked
         */
        if (!is_null($limit)) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery();
    }

    /**
     * @param array $filter
     *
     * @return Query
     */
    public function findFiltered(array $filter)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('content_entity_contact');
        $queryBuilder->from(Entity\Contact::class, 'content_entity_contact');

        if (array_key_exists('search', $filter)) {
            $queryBuilder->andWhere($queryBuilder->expr()->orX(
                $queryBuilder->expr()
                ->like('content_entity_contact.firstName', ':like'),
                $queryBuilder->expr()->like('content_entity_contact.middleName', ':like'),
                $queryBuilder->expr()->like('content_entity_contact.lastName', ':like'),
                $queryBuilder->expr()->like('content_entity_contact.email', ':like')
            ));

            $queryBuilder->setParameter('like', sprintf("%%%s%%", $filter['search']));
        }

        if (array_key_exists('hasOrganisation', $filter)) {
            $queryBuilder->innerJoin(
                'content_entity_contact.contactOrganisation',
                'contact_entity_contact_organisation_for_filter'
            );
        }

        if (array_key_exists('gender', $filter)) {
            $queryBuilder->join('content_entity_contact.gender', 'general_entity_gender');
            $queryBuilder->andWhere($queryBuilder->expr()
                ->in('general_entity_gender.id', implode($filter['gender'], ', ')));
        }

        if (array_key_exists('title', $filter)) {
            $queryBuilder->join('content_entity_contact.title', 'general_entity_title');
            $queryBuilder->andWhere($queryBuilder->expr()
                ->in('general_entity_title.id', implode($filter['title'], ', ')));
        }

        $direction = 'ASC';
        if (isset($filter['direction']) && in_array(strtoupper($filter['direction']), ['ASC', 'DESC'])) {
            $direction = strtoupper($filter['direction']);
        }

        switch ($filter['order']) {
            case 'name':
                $queryBuilder->addOrderBy('content_entity_contact.lastName', $direction);
                break;
            case 'organisation':
                $queryBuilder->leftJoin(
                    'content_entity_contact.contactOrganisation',
                    'contact_entity_contact_organisation'
                );
                $queryBuilder->leftJoin(
                    'contact_entity_contact_organisation.organisation',
                    'organisation_entity_organisation'
                );

                $queryBuilder->addOrderBy('organisation_entity_organisation.organisation', $direction);
                break;
            default:
                $queryBuilder->addOrderBy('content_entity_contact.id', $direction);
        }

        return $queryBuilder->getQuery();
    }


    /**
     * @param $projectId
     *
     * @return array
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \InvalidArgumentException
     */
    public function findContactByProjectId($projectId)
    {
        $queryBuilder = $this->findContactByProjectIdQueryBuilder();
        $queryBuilder->setParameter(1, $projectId);

        return $queryBuilder->getQuery()->useQueryCache(true)->getResult();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findContactByProjectIdQueryBuilder()
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('c');
        $queryBuilder->from(Entity\Contact::class, 'c');
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
        $queryBuilder->andWhere($queryBuilder->expr()->orX(
            $queryBuilder->expr()->in('c.id', $associates->getDQL()),
            $queryBuilder->expr()->in('c.id', $affiliates->getDQL()),
            $queryBuilder->expr()->in('c.id', $workpackage->getDQL()),
            $queryBuilder->expr()->in('c.id', $projectLeaders->getDQL())
        ));

        $queryBuilder->addOrderBy('c.lastName', 'ASC');

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
        $queryBuilder->from(Entity\Contact::class, 'c');
        $queryBuilder->orWhere('c.email = ?1');
        $queryBuilder->setParameter(1, $email);

        if (!$onlyMain) {
            $queryBuilder->leftJoin('c.emailAddress', 'e');
            $queryBuilder->orWhere('e.email = ?2');
            $queryBuilder->setParameter(2, $email);
        }

        $queryBuilder->setMaxResults(1);

        return $queryBuilder->getQuery()->useQueryCache(true)->getOneOrNullResult();
    }

    /**
     * @return Contact[]
     */
    public function findContactsWithDateOfBirth()
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('c');
        $queryBuilder->from(Entity\Contact::class, 'c');
        $queryBuilder->andWhere($queryBuilder->expr()->isNull('c.dateEnd'));
        $queryBuilder->andWhere($queryBuilder->expr()->isNotNull('c.dateOfBirth'));

        return $queryBuilder->getQuery()->useQueryCache(true)->getResult();
    }

    /**
     * @return Contact[]
     */
    public function findContactsWithCV()
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('c');
        $queryBuilder->from(Entity\Contact::class, 'c');
        $queryBuilder->andWhere($queryBuilder->expr()->isNull('c.dateEnd'));
        $queryBuilder->innerJoin('c.cv', 'cv');

        return $queryBuilder->getQuery()->useQueryCache(true)->getResult();
    }

    /**
     * @param  bool $onlyPublic
     *
     * @return Contact[]
     */
    public function findContactsWithActiveProfile($onlyPublic)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('c');
        $queryBuilder->from(Entity\Contact::class, 'c');
        $queryBuilder->innerJoin('c.profile', 'p');
        $queryBuilder->andWhere($queryBuilder->expr()->isNull('c.dateEnd'));
        $queryBuilder->andWhere($queryBuilder->expr()->isNotNull('p.description'));
        //Exclude the empty descriptions
        $queryBuilder->andWhere('p.description <> ?2');
        $queryBuilder->setParameter(2, '');

        if ($onlyPublic) {
            $queryBuilder->andWhere('p.visible <> ?1');
            $queryBuilder->setParameter(1, Entity\Profile::VISIBLE_HIDDEN);
        }

        return $queryBuilder->getQuery()->useQueryCache(true)->getResult();
    }

    /**
     *  Returns true of false depending if a contact is a community member.
     *
     * @param Entity\Contact        $contact
     * @param Options\ModuleOptions $options
     *
     * @return boolean|null
     */
    public function findIsCommunityMember(Entity\Contact $contact, Options\ModuleOptions $options)
    {
        if ($options->getCommunityViaProjectParticipation()) {
            /** @var Project $projectRepository */
            $projectRepository = $this->getEntityManager()->getRepository(\Project\Entity\Project::class);
            /*
             * Go over the associates first
             */
            $queryBuilder = $this->_em->createQueryBuilder();
            $queryBuilder->select('contact.id');
            $queryBuilder->from(Entity\Contact::class, 'contact');
            $queryBuilder->join('contact.associate', 'a');
            $queryBuilder = $projectRepository->onlyActiveProjectOrRecentPO($queryBuilder);
            $queryBuilder->andWhere('contact = :contact');
            $queryBuilder->setParameter('contact', $contact);
            //If we find a associate, return true, else proceed
            if (sizeof($queryBuilder->getQuery()->useQueryCache(true)->getResult()) > 0) {
                return true;
            }
            /*
             * Go over the affiliations
             */
            $queryBuilder = $this->_em->createQueryBuilder();
            $queryBuilder->select('contact.id');
            $queryBuilder->from(Entity\Contact::class, 'contact');
            $queryBuilder->join('contact.contactOrganisation', 'co');
            $queryBuilder->join('co.organisation', 'organisation');
            $queryBuilder->join('organisation.affiliation', 'a');
            $queryBuilder = $projectRepository->onlyActiveProjectOrRecentPO($queryBuilder);
            $queryBuilder->andWhere('contact = :contact');
            $queryBuilder->setParameter('contact', $contact);
            //If we find a associate, return true, else proceed
            if (sizeof($queryBuilder->getQuery()->useQueryCache(true)->getResult()) > 0) {
                return true;
            }
            /*
             * Go over the affiliations via the cluster
             */
            $queryBuilder = $this->_em->createQueryBuilder();
            $queryBuilder->select('contact.id');
            $queryBuilder->from(Entity\Contact::class, 'contact');
            $queryBuilder->join('contact.contactOrganisation', 'co');
            $queryBuilder->join('co.organisation', 'organisation');
            $queryBuilder->join('organisation.cluster', 'cluster1', 'cluster1.organisation = organisation');
            $queryBuilder->join('organisation.cluster', 'cluster2', 'cluster1.cluster = cluster2.cluster');
            $queryBuilder->join('organisation.affiliation', 'a');
            $queryBuilder = $projectRepository->onlyActiveProjectOrRecentPO($queryBuilder);
            $queryBuilder->andWhere('contact = :contact');
            $queryBuilder->setParameter('contact', $contact);
            //If we find a associate, return true, else proceed
            if (sizeof($queryBuilder->getQuery()->useQueryCache(true)->getResult()) > 0) {
                return true;
            }

            return false;
        }
    }

    /**
     * Return Contact entities based on a selection SQL using a native SQL query.
     *
     * @param SelectionSql $sql
     * @param bool         $toArray
     *
     * @return Entity\Contact[]
     */
    public function findContactsBySelectionSQL(SelectionSql $sql, $toArray = false)
    {
        $resultSetMap = new ResultSetMapping();
        $resultSetMap->addEntityResult(Entity\Contact::class, 'c');
        $resultSetMap->addJoinedEntityResult('Contact\Entity\ContactOrganisation', 'co', 'c', 'contactOrganisation');

        $resultSetMap->addFieldResult('c', 'contact_id', 'id');
        $resultSetMap->addFieldResult('c', 'email', 'email');
        $resultSetMap->addFieldResult('c', 'firstname', 'firstName');
        $resultSetMap->addFieldResult('c', 'middlename', 'middleName');
        $resultSetMap->addFieldResult('c', 'lastname', 'lastName');


        $resultSetMap->addFieldResult('co', 'id', 'contact_organisation_id');

        $resultSetMap->addJoinedEntityResult('Organisation\Entity\Organisation', 'o', 'co', 'organisation');
        $resultSetMap->addJoinedEntityResult('General\Entity\Country', 'cy', 'o', 'country');

        $resultSetMap->addFieldResult('o', 'organisation', 'organisation');
        $resultSetMap->addFieldResult('cy', 'country', 'country');

        $query = $this->getEntityManager()->createNativeQuery("SELECT c.contact_id, email, firstname, middlename, lastname, co.contact_organisation_id, o.organisation, cy.country FROM contact c
                LEFT JOIN contact_organisation co ON co.contact_id = c.contact_id
                LEFT JOIN organisation o ON co.organisation_id = o.organisation_id
                LEFT JOIN country cy ON o.country_id = cy.country_id
            WHERE c.contact_id IN (" . $sql->getQuery() . ") AND date_end IS NULL ORDER BY lastName", $resultSetMap);

        /**
         * 'c_id' => int 5059
         * 'c_email' => string 'laila.gide@thalesgroup.com' (length=26)
         * 'c_firstName' => string 'Laila' (length=5)
         * 'c_middleName' => null
         * 'c_lastName' => string 'Gide' (length=4)
         * 'o_organisation' => string 'Thales' (length=6)
         * 'cy_country' => string 'Thales' (length=6)
         */
        if ($toArray) {
            return $this->reIndexContactArray($query->getResult(AbstractQuery::HYDRATE_SCALAR));
        } else {
            //Note that the contactOrgansiation is always empty
            return $query->getResult();
        }
    }

    /**
     * Return Contact entities based on a query generated by the Facebook functionality.
     *
     * @param Entity\Facebook $facebook
     *
     * @return Entity\Contact[]
     */
    public function findContactsInFacebook(Entity\Facebook $facebook)
    {
        $resultSetMap = new ResultSetMapping();
        $resultSetMap->addEntityResult(Entity\Contact::class, 'c');
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

        $query = $this->getEntityManager()
            ->createNativeQuery(sprintf(
                "SELECT contact_id, email, firstname, middlename, lastname, position FROM contact WHERE contact_id IN (%s) AND date_end IS NULL %s ",
                $queryInString,
                $orderBy
            ), $resultSetMap);

        return $query->getResult();
    }

    /**
     * Return Contact entities based on a selection SQL using a native SQL query.
     *
     * @param Selection $selection
     * @param bool      $toArray
     *
     * @return Entity\Contact[]
     */
    public function findContactsBySelectionContact(Selection $selection, $toArray = false)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('c', 'co', 'o', 'cy');
        $qb->from(Entity\Contact::class, 'c');
        $qb->join('c.selectionContact', 'sc');
        $qb->leftJoin('c.contactOrganisation', 'co');
        $qb->join('co.organisation', 'o');
        $qb->join('o.country', 'cy');
        $qb->distinct('c.id');
        $qb->andWhere($qb->expr()->isNull('c.dateEnd'));
        $qb->andWhere('sc.selection = ?1');
        $qb->setParameter(1, $selection->getId());
        $qb->orderBy('c.lastName');

        if ($toArray) {
            return $this->reIndexContactArray($qb->getQuery()->getResult(AbstractQuery::HYDRATE_SCALAR));
        } else {
            return $qb->getQuery()->getResult();
        }
    }

    /**
     * @param Entity\OptIn $optIn
     * @param bool         $toArray
     *
     * @return Entity\Contact[]
     */
    public function findContactsByOptIn(Entity\OptIn $optIn, $toArray = false)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('c');
        $qb->from(Entity\Contact::class, 'c');
        $qb->join("c.optIn", 'optIn');
        $qb->where($qb->expr()->in('optIn.id', $optIn->getId()));

        $qb->orderBy('c.lastName');

        if ($toArray) {
            return $qb->getQuery()->getArrayResult();
        } else {
            return $qb->getQuery()->getResult();
        }
    }


    /**
     * Return Contact entities based on a selection SQL using a native SQL query.
     *
     * @param Entity\Contact $contact
     * @param SelectionSql   $sql
     *
     * @return bool
     */
    public function isContactInSelectionSQL(Entity\Contact $contact, SelectionSql $sql)
    {
        $resultSetMap = new ResultSetMapping();
        $resultSetMap->addEntityResult(Entity\Contact::class, 'c');
        $resultSetMap->addFieldResult('c', 'contact_id', 'id');
        $query = $this->getEntityManager()->createNativeQuery("SELECT
             contact_id
             FROM contact
             WHERE contact_id
             IN (" . $sql->getQuery() . ") AND contact_id = " . $contact->getId(), $resultSetMap);

        return sizeof($query->getResult()) > 0;
    }

    /**
     * This is basic search for contacts (based on the name, and email.
     *
     * @param string $searchItem
     * @param int    $maxResults
     *
     * @return Entity\Contact[]
     */
    public function searchContacts($searchItem, $maxResults = 12)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(['c.id', 'c.firstName', 'c.middleName', 'c.lastName', 'c.email', 'o.organisation']);
        $qb->from(Entity\Contact::class, 'c');
        $qb->leftJoin('c.contactOrganisation', 'co');
        $qb->join('co.organisation', 'o');
        $qb->distinct('c.id');

        $qb->where($qb->expr()->orX(
            $qb->expr()->like(
                $qb->expr()
                ->concat(
                    'c.firstName',
                    $qb->expr()->concat($qb->expr()->literal(' '), 'c.middleName'),
                    $qb->expr()->concat($qb->expr()->literal(' '), 'c.lastName')
                ),
                $qb->expr()->literal("%" . $searchItem . "%")
            ),
            $qb->expr()->like('c.email', $qb->expr()->literal("%" . $searchItem . "%"))
        ));

        $qb->orderBy('c.lastName', 'ASC');

        $qb->setMaxResults($maxResults);

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * @param Organisation $organisation
     *
     * @return Contact[]
     */
    public function findContactsInOrganisation(Organisation $organisation)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('c');
        $qb->from(Entity\Contact::class, 'c');
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
     * @param Calendar $calendar
     *
     * @return Entity\Contact[]
     */
    public function findPossibleContactByCalendar(Calendar $calendar)
    {
        /*
         * Use the contactQueryBuilder and exclude the ones which are already present based on the roles
         */
        $findContactByProjectIdQueryBuilder = $this->findContactByProjectIdQueryBuilder();

        //Find the reviewers
        $findReviewContactByProjectQueryBuilder = $this->getEntityManager()
            ->getRepository('Project\Entity\Review\Review')->findReviewContactByProjectQueryBuilder();

        //Remove all the contacts which are already in the project as associate or otherwise affected
        $findContactByProjectIdQueryBuilder->andWhere($findContactByProjectIdQueryBuilder->expr()
            ->notIn('c', $findReviewContactByProjectQueryBuilder->getDQL()));

        $findContactByProjectIdQueryBuilder->setParameter(1, $calendar->getProjectCalendar()->getProject()->getId());
        $findContactByProjectIdQueryBuilder->setParameter('project', $calendar->getProjectCalendar()->getProject());
        $findContactByProjectIdQueryBuilder->addOrderBy('c.lastName', 'ASC');

        return $findContactByProjectIdQueryBuilder->getQuery()->useQueryCache(true)->getResult();
    }

    /**
     * @param $contacts
     *
     * @return array
     */
    private function reIndexContactArray($contacts)
    {
        //Normalize the array to default values
        foreach ($contacts as $k => $contact) {
            $contact['id'] = $contact['c_id'];
            unset($contact['c_id']);
            $contact['email'] = $contact['c_email'];
            unset($contact['c_email']);
            $contact['firstName'] = $contact['c_firstName'];
            unset($contact['c_firstName']);
            $contact['middleName'] = $contact['c_middleName'];
            unset($contact['c_middleName']);
            $contact['lastName'] = $contact['c_lastName'];
            unset($contact['c_lastName']);
            $contact['fullname'] = sprintf(
                "%s %s",
                $contact['firstName'],
                trim(implode(' ', [$contact['middleName'], $contact['lastName']]))
            );
            $contact['organisation'] = $contact['o_organisation'];
            unset($contact['o_organisation']);
            $contact['country'] = $contact['cy_country'];
            unset($contact['cy_country']);

            $contacts[$k] = $contact;
        }

        return $contacts;
    }
}
