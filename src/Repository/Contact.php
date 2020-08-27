<?php

/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (contact_entity_contact) Copyright (c) 2019 ITEA Office (https://itea3.org) (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Repository;

use Admin\Entity\Pageview;
use Affiliation\Entity\Affiliation;
use Calendar\Entity\Calendar;
use Contact\Entity;
use Contact\Entity\ContactOrganisation;
use Contact\Entity\Selection;
use Contact\Entity\SelectionSql;
use DateInterval;
use DateTime;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use DoctrineExtensions\Query\Mysql\Year;
use Evaluation\Entity\Evaluation;
use Evaluation\Entity\Reviewer;
use Evaluation\Repository\ReviewerRepository;
use General\Entity\Country;
use Organisation\Entity\Organisation;
use Organisation\Entity\Type;
use PDO;
use Project\Entity\Achievement;
use Project\Entity\ChangeRequest\Process;
use Project\Entity\Contract;
use Project\Entity\Description\Description;
use Project\Entity\Invite;
use Project\Entity\Log;
use Project\Entity\Pca;
use Project\Entity\Project;
use Project\Entity\Rationale;
use Project\Entity\Result\Result;
use Project\Entity\Version\Version;
use Project\Entity\Workpackage\Workpackage;

use function array_key_exists;
use function count;
use function in_array;
use function sprintf;
use function strtoupper;

/***
 * Class Contact
 *
 * @package Contact\Repository
 */
class Contact extends EntityRepository
{
    public function findContacts(int $limit = null): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('contact_entity_contact');
        $qb->from(Entity\Contact::class, 'contact_entity_contact');
        $qb->distinct('contact_entity_contact.id');
        $qb->orderBy('contact_entity_contact.id', 'DESC');
        /*
         * Only add a limit when asked
         */
        if (null !== $limit) {
            $qb->setMaxResults($limit);
        }

        return $qb;
    }

    public function findFiltered(array $filter): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('contact_entity_contact');
        $qb->from(Entity\Contact::class, 'contact_entity_contact');

        $qb = $this->applyContactFilter(
            $qb,
            $filter,
            ['order' => 'contact_entity_contact.id', 'direction' => Criteria::DESC]
        );

        return $qb;
    }

    private function applyContactFilter(QueryBuilder $qb, array $filter, array $order): QueryBuilder
    {
        $direction = Criteria::ASC;
        if (
            isset($filter['direction'])
            && in_array(strtoupper($filter['direction']), [Criteria::ASC, Criteria::DESC], true)
        ) {
            $direction = strtoupper($filter['direction']);
        }

        if (array_key_exists('search', $filter)) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('contact_entity_contact.firstName', ':like'),
                    $qb->expr()->like('contact_entity_contact.middleName', ':like'),
                    $qb->expr()->like('contact_entity_contact.lastName', ':like'),
                    $qb->expr()->like('contact_entity_contact.email', ':like'),
                    $qb->expr()->like(
                        $qb->expr()->concat(
                            'contact_entity_contact.firstName',
                            $qb->expr()->literal(' '),
                            'contact_entity_contact.middleName',
                            $qb->expr()->literal(' '),
                            'contact_entity_contact.lastName'
                        ),
                        ':like'
                    ),
                    $qb->expr()->like(
                        $qb->expr()->concat(
                            'contact_entity_contact.firstName',
                            $qb->expr()->literal(' '),
                            'contact_entity_contact.lastName'
                        ),
                        ':like'
                    )
                )
            );

            $qb->setParameter('like', sprintf('%%%s%%', $filter['search']));

            //Reset the order to order on lastname (if the order is on id)
            if ($filter['order'] === 'contact_entity_contact.id') {
                $filter['order'] = 'contact_entity_contact.lastName';
            }
        }

        if (array_key_exists('options', $filter)) {
            if (in_array('hasOrganisation', $filter['options'], false)) {
                $qb->innerJoin(
                    'contact_entity_contact.contactOrganisation',
                    'contact_entity_contact_organisation_for_filter'
                );
            }
            if (in_array('onlyDeactivated', $filter['options'], false)) {
                $qb->andWhere($qb->expr()->isNotNull('contact_entity_contact.dateEnd'));
            }
        }


        /** Only when the filter is turned on, omit this extra rule */
        if (
            ! (array_key_exists('options', $filter)
                && in_array(
                    'includeDeactivated',
                    $filter['options'],
                    true
                ))
            && (isset($filter['options']) && ! in_array('onlyDeactivated', $filter['options'], true))
        ) {
            $qb->andWhere($qb->expr()->isNull('contact_entity_contact.dateEnd'));
        }

        if (! isset($filter['options'])) {
            $qb->andWhere($qb->expr()->isNull('contact_entity_contact.dateEnd'));
        }

        if (array_key_exists('gender', $filter)) {
            $qb->join('contact_entity_contact.gender', 'general_entity_gender');
            $qb->andWhere(
                $qb->expr()
                    ->in('general_entity_gender.id', $filter['gender'])
            );
        }


        if (array_key_exists('country', $filter) && ! empty($filter['country'])) {
            $qb->innerJoin(
                'contact_entity_contact.contactOrganisation',
                'contact_entity_contact_organisation_for_country'
            );
            $qb->innerJoin(
                'contact_entity_contact_organisation_for_country.organisation',
                'contact_entity_contact_organisation_for_country_organisation'
            );
            $qb->innerJoin(
                'contact_entity_contact_organisation_for_country_organisation.country',
                'contact_entity_contact_organisation_for_country_organisation_country'
            );
            $qb->andWhere(
                $qb->expr()
                    ->in(
                        'contact_entity_contact_organisation_for_country_organisation_country.id',
                        $filter['country']
                    )
            );
        }
        switch ($filter['order']) {
            case 'amount':
                $qb->addOrderBy('amount', $direction);
                break;
            case 'name':
                $qb->addOrderBy('contact_entity_contact.lastName', $direction);
                break;
            case 'organisation':
                $qb->leftJoin(
                    'contact_entity_contact.contactOrganisation',
                    'contact_entity_contact_organisation'
                );
                $qb->leftJoin(
                    'contact_entity_contact_organisation.organisation',
                    'organisation_entity_organisation'
                );

                $qb->addOrderBy('organisation_entity_organisation.organisation', $direction);
                break;
            case 'country':
                $qb->leftJoin(
                    'contact_entity_contact.contactOrganisation',
                    'contact_entity_contact_organisation'
                );
                $qb->leftJoin(
                    'contact_entity_contact_organisation.organisation',
                    'organisation_entity_organisation'
                );
                $qb->leftJoin(
                    'organisation_entity_organisation.country',
                    'general_entity_country'
                );
                $qb->addOrderBy('general_entity_country.country', $direction);
                $qb->addOrderBy('contact_entity_contact.lastName', $direction);
                break;

            default:
                $qb->addOrderBy($order['order'], $order['direction']);
        }

        return $qb;
    }

    public function findDuplicateContacts(array $filter): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(
            'contact_entity_contact contact',
            'COUNT(contact_entity_contact) amount'
        );
        $qb->from(Entity\Contact::class, 'contact_entity_contact');
        $qb->groupBy('contact_entity_contact.firstName, contact_entity_contact.lastName');
        $qb->having('amount > 1');

        $qb = $this->applyContactFilter(
            $qb,
            $filter,
            ['order' => 'amount', 'direction' => Criteria::DESC]
        );

        return $qb;
    }

    public function isInactiveContact(Entity\Contact $contact): bool
    {
        $qb = $this->findInactiveContactQuery();
        $qb->andWhere($qb->expr()->eq('contact_entity_contact.id', $contact->getId()));

        return null !== $qb->getQuery()->getOneOrNullResult();
    }

    private function findInactiveContactQuery(): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select(
            [
                'contact_entity_contact.id',
                'contact_entity_contact.firstName',
                'contact_entity_contact.middleName',
                'contact_entity_contact.lastName',
                'contact_entity_contact.email'
            ]
        );
        $qb->from(Entity\Contact::class, 'contact_entity_contact');

        $relations = [
            'project',
            'projectVersion',
            'projectDescription',
            'optIn',
            'pca',
            'deeplinkContact',
            'ndaApprover',
            'programDoa',
            'rationale',
            'organisationLog',
            'affiliation',
            'parent',
            'parentFinancial',
            'parentOrganisation',
            'financial',
            'invoice',
            'associate',
            'registration',
            'dnd',
            'badge',
            'mailing',
            'result',
            'workpackage',
            'idea',
            'ideaMessage',
            'ideaPartner',
            'evaluation',
            'calendarContact',
            'calendarDocument',
            'calendar',
            'projectReviewers',
            'projectReport',
            'projectChangelog',
            'projectCalendarReviewers',
            'projectReportReviewers',
            'projectVersionReviewers',
            'projectReportEffortSpent',
            'contract',
            'invite',
            'inviteContact',
            'ideaInvite',
            'ideaInviteContact',
            'loi',
            'affiliationDoa',
            'parentDoa',

            'invoiceLog',
            'achievement',
            'changeRequestProcess',
            'versionContact',
            'workpackageContact',
            'log',
            'affiliationVersion',
            'affiliationDescription',
            'projectBooth',
            'organisationBooth',
            /* //More than 61 is not possible
              'journal','officeContact',
             'organisationUpdates'*/
        ];

        foreach ($relations as $relation) {
            $qb->leftJoin('contact_entity_contact.' . $relation, $relation);
            $qb->andWhere($qb->expr()->isNull($relation . '.id'));
        }

        // Exclude the active selections
        $selectionContact = $this->_em->createQueryBuilder();
        $selectionContact->select('selectionContact.id');
        $selectionContact->from(Entity\SelectionContact::class, 'contact_entity_selection_contact');
        $selectionContact->join('contact_entity_selection_contact.contact', 'selectionContact');
        $selectionContact->join('contact_entity_selection_contact.selection', 'contact_entity_selection');
        $selectionContact->andWhere($selectionContact->expr()->isNull('contact_entity_selection.dateDeleted'));

        $qb->andWhere($qb->expr()->notIn('contact_entity_contact.id', $selectionContact->getDQL()));
        $qb->orderBy('contact_entity_contact.id', Criteria::DESC);

        // Add a constraint for a month
        $lastMonth = new DateTime();
        $lastMonth->sub(new DateInterval('P1M'));

        $qb->andWhere('contact_entity_contact.dateCreated < :lastMonth');
        $qb->setParameter('lastMonth', $lastMonth, Types::DATETIME_MUTABLE);

        return $qb;
    }

    public function findInactiveContacts(): array
    {
        $queryBuilder = $this->findInactiveContactQuery();

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function contactIsActiveInProject(Entity\Contact $contact, int $years = 5, string $which = 'recent'): bool
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('contact_entity_contact');
        $qb->from(Entity\Contact::class, 'contact_entity_contact');
        $qb->andWhere('contact_entity_contact = :contact');
        $qb->setParameter('contact', $contact);

        $relations = [
            'project'              => Project::class,
            'projectVersion'       => Version::class,
            'projectDescription'   => Description::class,
            'pca'                  => Pca::class,
            'rationale'            => Rationale::class,
            'affiliation'          => Affiliation::class,
            'result'               => Result::class,
            'workpackage'          => Workpackage::class,
            'evaluation'           => Evaluation::class,
            'contract'             => Contract::class,
            'invite'               => Invite::class,
            'achievement'          => Achievement::class,
            'changeRequestProcess' => Process::class,
            'log'                  => Log::class,
        ];

        foreach ($relations as $key => $relation) {
            $subQuery = $this->_em->createQueryBuilder();
            $subQuery->select('contact_entity_contact_' . $key);
            $subQuery->from($relation, $key);
            $subQuery->join($key . '.contact', 'contact_entity_contact_' . $key);
            $subQuery->where($qb->expr()->eq('contact_entity_contact_' . $key . '.id', $contact->getId()));

            //Project leaders are exempted from this constraint
            $today = new DateTime();
            $yearsAgo = $today->sub(new DateInterval(sprintf('P%dY', $years)));

            if ($key !== 'project') {
                $subQuery->join($key . '.project', 'project_entity_project_' . $key);

                if ($which === 'recent') {
                    $subQuery->andWhere('project_entity_project_' . $key . '.dateEnd > :dateTime');
                }

                if ($which === 'older') {
                    $subQuery->andWhere('project_entity_project_' . $key . '.dateEnd < :dateTime');
                }

                $qb->setParameter('dateTime', $yearsAgo, Types::DATETIME_MUTABLE);
            }

            $qb->andWhere($qb->expr()->notIn('contact_entity_contact', $subQuery->getDQL()));
        }

        return null === $qb->getQuery()->getOneOrNullResult();
    }

    public function contactIsReviewer(Entity\Contact $contact): bool
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('contact_entity_contact');
        $qb->from(Entity\Contact::class, 'contact_entity_contact');
        $qb->andWhere('contact_entity_contact = :contact');
        $qb->setParameter('contact', $contact);

        $relations = [
            'projectReviewers'         => Reviewer::class,
            'projectCalendarReviewers' => \Project\Entity\Calendar\Reviewer::class,
            'projectReportReviewers'   => \Project\Entity\Report\Reviewer::class,
            'projectVersionReviewers'  => \Project\Entity\Version\Reviewer::class,
            'projectReviewContact'     => Reviewer\Contact::class
        ];

        foreach ($relations as $key => $relation) {
            $subQuery = $this->_em->createQueryBuilder();
            $subQuery->select('contact_entity_contact_' . $key);
            $subQuery->from($relation, $key);
            $subQuery->join($key . '.contact', 'contact_entity_contact_' . $key);
            $subQuery->where($qb->expr()->eq('contact_entity_contact_' . $key . '.id', $contact->getId()));

            $qb->andWhere($qb->expr()->notIn('contact_entity_contact', $subQuery->getDQL()));
        }

        return null === $qb->getQuery()->getOneOrNullResult();
    }

    public function contactIsPresentAtEvent(Entity\Contact $contact, int $years = 2, string $which = 'recent'): bool
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('contact_entity_contact');
        $qb->from(Entity\Contact::class, 'contact_entity_contact');
        $qb->andWhere('contact_entity_contact = :contact');
        $qb->setParameter('contact', $contact);

        $qb->join('contact_entity_contact.registration', 'event_entity_registration');
        $qb->join('event_entity_registration.meeting', 'event_entity_meeting');

        if ($which === 'recent') {
            $qb->andWhere('event_entity_meeting.dateFrom > :dateTime');
        }

        if ($which === 'older') {
            $qb->andWhere('event_entity_meeting.dateFrom < :dateTime');
        }

        $today = new DateTime();
        $yearsAgo = $today->sub(new DateInterval(sprintf('P%dY', $years)));
        $qb->setParameter('dateTime', $yearsAgo, Types::DATETIME_MUTABLE);

        return null !== $qb->getQuery()->getOneOrNullResult();
    }

    public function contactHasIdea(Entity\Contact $contact, int $years = 2, string $which = 'recent'): bool
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('contact_entity_contact');
        $qb->from(Entity\Contact::class, 'contact_entity_contact');
        $qb->andWhere('contact_entity_contact = :contact');
        $qb->setParameter('contact', $contact);

        $qb->join('contact_entity_contact.idea', 'project_entity_idea');


        if ($which === 'recent') {
            $qb->andWhere('project_entity_idea.dateCreated > :dateTime');
        }

        if ($which === 'older') {
            $qb->andWhere('project_entity_idea.dateCreated < :dateTime');
        }


        $today = new DateTime();
        $yearsAgo = $today->sub(new DateInterval(sprintf('P%dY', $years)));
        $qb->setParameter('dateTime', $yearsAgo, Types::DATETIME_MUTABLE);

        return null !== $qb->getQuery()->getOneOrNullResult();
    }

    public function findContactByProjectId(int $projectId): array
    {
        $qb = $this->findContactByProjectIdQueryBuilder();
        $qb->setParameter(1, $projectId);

        return $qb->getQuery()->useQueryCache(true)->getResult();
    }

    public function findContactByProjectIdQueryBuilder(): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('contact_entity_contact');
        $qb->from(Entity\Contact::class, 'contact_entity_contact');
        $qb->distinct('contact_entity_contact.id');
        //Add the associates
        $associates = $this->_em->createQueryBuilder();
        $associates->select('associateContact.id');
        $associates->from(Affiliation::class, 'associateAffiliation');
        $associates->join('associateAffiliation.associate', 'associateContact');
        $associates->join('associateAffiliation.project', 'associateProject');
        $associates->andWhere($associates->expr()->isNull('associateAffiliation.dateEnd'));
        $associates->andWhere('associateProject.id = ?1');
        //Add the affiliates
        $affiliates = $this->_em->createQueryBuilder();
        $affiliates->select('affiliationContact.id');
        $affiliates->from(Affiliation::class, 'affiliation');
        $affiliates->join('affiliation.project', 'affiliationProject');
        $affiliates->join('affiliation.contact', 'affiliationContact');
        $affiliates->andWhere($associates->expr()->isNull('affiliation.dateEnd'));
        $affiliates->andWhere('affiliationProject.id = ?1');
        //Add the workpackage leaders
        $workpackage = $this->_em->createQueryBuilder();
        $workpackage->select('workpackageContact.id');
        $workpackage->from(Workpackage::class, 'workpackage');
        $workpackage->join('workpackage.project', 'workpackageProject');
        $workpackage->join('workpackage.contact', 'workpackageContact');
        $workpackage->andWhere('workpackageProject.id = ?1');
        //Add the Rationale
        $rationale = $this->_em->createQueryBuilder();
        $rationale->select('rationaleContact.id');
        $rationale->from(Rationale::class, 'rationale');
        $rationale->join('rationale.project', 'rationaleProject');
        $rationale->join('rationale.contact', 'rationaleContact');
        $rationale->andWhere('rationaleProject.id = ?1');
        //Add the project leaders
        $projectLeaders = $this->_em->createQueryBuilder();
        $projectLeaders->select('projectContact.id');
        $projectLeaders->from(Project::class, 'project');
        $projectLeaders->join('project.contact', 'projectContact');
        $projectLeaders->andWhere('project.id = ?1');
        $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->in('contact_entity_contact.id', $associates->getDQL()),
                $qb->expr()->in('contact_entity_contact.id', $affiliates->getDQL()),
                $qb->expr()->in('contact_entity_contact.id', $workpackage->getDQL()),
                $qb->expr()->in('contact_entity_contact.id', $rationale->getDQL()),
                $qb->expr()->in('contact_entity_contact.id', $projectLeaders->getDQL())
            )
        );

        $qb->addOrderBy('contact_entity_contact.lastName', 'ASC');

        return $qb;
    }

    public function findContactByEmail(string $email, bool $onlyMain): ?Entity\Contact
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('contact_entity_contact');
        $qb->from(Entity\Contact::class, 'contact_entity_contact');
        $qb->orWhere('contact_entity_contact.email = ?1');
        $qb->setParameter(1, $email);

        if (! $onlyMain) {
            $qb->leftJoin('contact_entity_contact.emailAddress', 'contact_entity_email');
            $qb->orWhere('contact_entity_email.email = ?2');
            $qb->setParameter(2, $email);
        }

        $qb->setMaxResults(1);

        return $qb->getQuery()->useQueryCache(true)->getOneOrNullResult();
    }

    public function findContactsWithDateOfBirth(): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('contact_entity_contact');
        $qb->from(Entity\Contact::class, 'contact_entity_contact');
        $qb->andWhere($qb->expr()->isNull('contact_entity_contact.dateEnd'));
        $qb->andWhere($qb->expr()->isNotNull('contact_entity_contact.dateOfBirth'));

        return $qb->getQuery()->useQueryCache(true)->getResult();
    }

    /**
     * @param bool $onlyPublic
     *
     * @return Entity\Contact[]
     */
    public function findContactsWithActiveProfile(bool $onlyPublic): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('contact_entity_contact');
        $qb->from(Entity\Contact::class, 'contact_entity_contact');
        $qb->innerJoin('contact_entity_contact.profile', 'p');
        $qb->andWhere($qb->expr()->isNull('contact_entity_contact.dateEnd'));
        $qb->andWhere($qb->expr()->isNotNull('contact_entity_contact.dateActivated'));
        $qb->andWhere($qb->expr()->isNull('contact_entity_contact.dateAnonymous'));

        $qb->andWhere($qb->expr()->isNotNull('p.description'));
        //Exclude the empty descriptions
        $qb->andWhere('p.description <> ?2');
        $qb->setParameter(2, '');

        if ($onlyPublic) {
            $qb->andWhere('p.visible <> ?1');
            $qb->setParameter(1, Entity\Profile::VISIBLE_HIDDEN);
        }

        return $qb->getQuery()->useQueryCache(true)->getResult();
    }

    public function findContactsBySelectionSQL(SelectionSql $sql, bool $toArray = false): array
    {
        $resultSetMap = new ResultSetMapping();
        $resultSetMap->addEntityResult(Entity\Contact::class, 'contact');

        $resultSetMap->addJoinedEntityResult(
            Entity\ContactOrganisation::class,
            'contact_organisation',
            'contact',
            'contactOrganisation'
        );
        $resultSetMap->addJoinedEntityResult(
            Organisation::class,
            'organisation',
            'contact_organisation',
            'organisation'
        );
        $resultSetMap->addJoinedEntityResult(
            Type::class,
            'organisation_type',
            'organisation',
            'type'
        );
        $resultSetMap->addJoinedEntityResult(
            Country::class,
            'country',
            'organisation',
            'country'
        );

        $resultSetMap->addFieldResult('contact', 'contact_id', 'id');
        $resultSetMap->addFieldResult('contact', 'email', 'email');
        $resultSetMap->addFieldResult('contact', 'firstname', 'firstName');
        $resultSetMap->addFieldResult('contact', 'middlename', 'middleName');
        $resultSetMap->addFieldResult('contact', 'lastname', 'lastName');
        $resultSetMap->addFieldResult('contact', 'department', 'department');
        $resultSetMap->addFieldResult('contact_organisation', 'contact_organisation_id', 'id');
        $resultSetMap->addFieldResult('organisation', 'organisation_id', 'id');
        $resultSetMap->addFieldResult('organisation', 'organisation', 'organisation');
        $resultSetMap->addFieldResult('organisation_type', 'type_id', 'id');
        $resultSetMap->addFieldResult('organisation_type', 'type', 'type');
        $resultSetMap->addFieldResult('country', 'country_id', 'id');
        $resultSetMap->addFieldResult('country', 'iso3', 'iso3');
        $resultSetMap->addFieldResult('country', 'country', 'country');


        $query = $this->getEntityManager()->createNativeQuery(
            'SELECT 
                      contact.contact_id, 
                      contact.email, 
                      contact.firstname, 
                      contact.middlename, 
                      contact.lastname, 
                      contact_organisation.contact_organisation_id, 
                      organisation.organisation_id,
                      organisation.organisation,
                      organisation_type.type_id,
                      organisation_type.type,
                      country.country_id,
                      country.country,
                      country.iso3
                FROM contact
                LEFT JOIN contact_organisation ON contact_organisation.contact_id = contact.contact_id
                LEFT JOIN organisation ON contact_organisation.organisation_id = organisation.organisation_id
                LEFT JOIN organisation_type ON organisation.type_id = organisation_type.type_id
                LEFT JOIN country ON organisation.country_id = country.country_id
            WHERE contact.contact_id IN (' . $sql->getQuery()
            . ') AND date_end IS NULL ORDER BY lastName',
            $resultSetMap
        );


        /**
         *
         */
        if ($toArray) {
            return $query->getResult(AbstractQuery::HYDRATE_ARRAY);
        }

        //Note that the contactOrganisation is always empty
        return $query->getResult();
    }

    public function findAmountOfContactsInSelection(Selection $selection): int
    {
        if (null !== $selection->getSql()) {
            $resultSetMap = new ResultSetMapping();
            $resultSetMap->addEntityResult(Entity\Contact::class, 'contact');
            $resultSetMap->addFieldResult('contact', 'blabla', 'blabla');

            $query = sprintf(
                'SELECT COUNT(contact.contact_id) FROM contact
            WHERE contact_id IN (%s) AND date_end IS NULL',
                $selection->getSql()->getQuery()
            );

            $statement = $this->_em->getConnection()->prepare($query);
            $statement->execute();

            return (int)$statement->fetch(PDO::FETCH_COLUMN);
        }


        $qb = $this->_em->createQueryBuilder();
        $qb->select('COUNT(contact_entity_contact)');
        $qb->from(Entity\Contact::class, 'contact_entity_contact');
        $qb->join('contact_entity_contact.selectionContact', 'contact_entity_selection_contact');
        $qb->distinct('contact_entity_contact.id');
        $qb->andWhere($qb->expr()->isNull('contact_entity_contact.dateEnd'));
        $qb->andWhere('contact_entity_selection_contact.selection = ?1');
        $qb->setParameter(1, $selection->getId());

        return (int)$qb->getQuery()->getSingleScalarResult();
    }

    public function findContactsInFacebook(Entity\Facebook $facebook): array
    {
        $resultSetMap = new ResultSetMapping();
        $resultSetMap->addEntityResult(Entity\Contact::class, 'contact_entity_contact');
        $resultSetMap->addFieldResult('contact_entity_contact', 'contact_id', 'id');
        $resultSetMap->addFieldResult('contact_entity_contact', 'email', 'email');
        $resultSetMap->addFieldResult('contact_entity_contact', 'firstname', 'firstName');
        $resultSetMap->addFieldResult('contact_entity_contact', 'middlename', 'middleName');
        $resultSetMap->addFieldResult('contact_entity_contact', 'lastname', 'lastName');
        $resultSetMap->addFieldResult('contact_entity_contact', 'position', 'position');
        $resultSetMap->addFieldResult('contact_entity_contact', 'date_activated', 'dateActivated');

        $queryInString = sprintf(
            'SELECT %s FROM %s WHERE %s',
            $facebook->getContactKey(),
            $facebook->getFromClause(),
            $facebook->getWhereClause()
        );

        $orderBy = null;
        if (null !== $facebook->getOrderbyClause()) {
            $orderBy = sprintf(' ORDER BY %s', $facebook->getOrderbyClause());
        }

        $query = $this->getEntityManager()
            ->createNativeQuery(
                sprintf(
                    'SELECT contact_id, email, firstname, middlename, lastname, position, date_activated FROM contact WHERE contact_id IN (%s) AND date_end IS NULL %s ',
                    $queryInString,
                    $orderBy
                ),
                $resultSetMap
            );

        return $query->getResult();
    }

    public function findContactsBySelectionContact(Selection $selection, bool $toArray = false): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('contact_entity_contact', 'co', 'o', 'cy');
        $qb->from(Entity\Contact::class, 'contact_entity_contact');
        $qb->join('contact_entity_contact.selectionContact', 'sc');
        $qb->leftJoin('contact_entity_contact.contactOrganisation', 'co');
        $qb->leftJoin('co.organisation', 'o');
        $qb->leftJoin('o.country', 'cy');
        $qb->distinct('contact_entity_contact.id');
        $qb->andWhere($qb->expr()->isNull('contact_entity_contact.dateEnd'));
        $qb->andWhere('sc.selection = ?1');
        $qb->setParameter(1, $selection->getId());
        $qb->orderBy('contact_entity_contact.lastName');

        if ($toArray) {
            return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
        }

        return $qb->getQuery()->getResult();
    }

    public function findContactsByOptIn(Entity\OptIn $optIn, bool $toArray = false): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->from(Entity\Contact::class, 'contact_entity_contact');
        $qb->join('contact_entity_contact.optIn', 'optIn');
        $qb->where($qb->expr()->eq('optIn.id', $optIn->getId()));

        if ($toArray) {
            $qb->select('contact_entity_contact.id');
            return $qb->getQuery()->getArrayResult();
        }

        $qb->select('contact_entity_contact');
        $qb->orderBy('contact_entity_contact.lastName');
        return $qb->getQuery()->getResult();
    }

    public function isContactInSelectionSQL(Entity\Contact $contact, SelectionSql $sql): bool
    {
        $resultSetMap = new ResultSetMapping();
        $resultSetMap->addEntityResult(Entity\Contact::class, 'contact_entity_contact');
        $resultSetMap->addFieldResult('contact_entity_contact', 'contact_id', 'id');
        $query = $this->getEntityManager()->createNativeQuery(
            'SELECT contact_id FROM contact WHERE contact_id IN ('
            . $sql->getQuery() . ') AND contact_id = ' . $contact->getId(),
            $resultSetMap
        );

        return count($query->getResult()) > 0;
    }

    public function searchContacts(string $searchItem, int $maxResults = PHP_INT_MAX): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(
            [
                'contact_entity_contact.id',
                'contact_entity_contact.firstName',
                'contact_entity_contact.middleName',
                'contact_entity_contact.lastName',
                'contact_entity_contact.email',
                'o.organisation',
            ]
        );
        $qb->from(Entity\Contact::class, 'contact_entity_contact');
        $qb->leftJoin('contact_entity_contact.contactOrganisation', 'co');
        $qb->join('co.organisation', 'o');
        $qb->distinct('contact_entity_contact.id');

        $qb->where(
            $qb->expr()->orX(
                $qb->expr()->like(
                    $qb->expr()
                        ->concat(
                            'contact_entity_contact.firstName',
                            $qb->expr()
                                ->concat(
                                    $qb->expr()->concat($qb->expr()->literal(' '), 'contact_entity_contact.middleName'),
                                    $qb->expr()->concat($qb->expr()->literal(' '), 'contact_entity_contact.lastName')
                                )
                        ),
                    $qb->expr()->literal('%' . $searchItem . '%')
                ),
                $qb->expr()->like('contact_entity_contact.email', $qb->expr()->literal('%' . $searchItem . '%'))
            )
        );

        $qb->orderBy('contact_entity_contact.lastName', 'ASC');

        $qb->setMaxResults($maxResults);

        return $qb->getQuery()->getArrayResult();
    }

    public function findContactsInOrganisation(Organisation $organisation): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('contact_entity_contact');
        $qb->from(Entity\Contact::class, 'contact_entity_contact');
        $qb->addOrderBy('contact_entity_contact.lastName', 'ASC');

        //Select the contacts based on their organisations
        $subSelect = $this->_em->createQueryBuilder();
        $subSelect->select('contact');
        $subSelect->from(ContactOrganisation::class, 'co');
        $subSelect->join('co.organisation', 'o');
        $subSelect->join('co.contact', 'contact');
        $subSelect->where('o.id = ?1');
        $qb->setParameter(1, $organisation->getId());

        $qb->andWhere($qb->expr()->isNull('contact_entity_contact.dateEnd'));
        $qb->andWhere($qb->expr()->in('contact_entity_contact', $subSelect->getDQL()));

        return $qb->getQuery()->getResult();
    }

    public function findPossibleContactByCalendar(Calendar $calendar): array
    {
        /*
         * Use the contactQueryBuilder and exclude the ones which are already present based on the roles
         */
        $findContactByProjectIdQueryBuilder = $this->findContactByProjectIdQueryBuilder();

        //Find the reviewers
        /** @var ReviewerRepository $reviewRepository */
        $reviewRepository = $this->getEntityManager()->getRepository(Reviewer::class);

        $findReviewContactByProjectQueryBuilder = $reviewRepository->findReviewContactByProjectQueryBuilder();

        //Remove all the contacts which are already in the project as associate or otherwise affected
        $findContactByProjectIdQueryBuilder->andWhere(
            $findContactByProjectIdQueryBuilder->expr()
                ->notIn(
                    'contact_entity_contact',
                    $findReviewContactByProjectQueryBuilder->getDQL()
                )
        );

        $findContactByProjectIdQueryBuilder->setParameter(1, $calendar->getProjectCalendar()->getProject()->getId());
        $findContactByProjectIdQueryBuilder->setParameter('project', $calendar->getProjectCalendar()->getProject());
        $findContactByProjectIdQueryBuilder->addOrderBy('contact_entity_contact.lastName', 'ASC');

        return $findContactByProjectIdQueryBuilder->getQuery()->useQueryCache(true)->getResult();
    }

    public function findMergeCandidatesFor(Entity\Contact $contact): array
    {
        //Short circuit the contact when the first name and lastname are not filled in
        //The query will case an loop than and will crash
        if (empty($contact->getFirstName()) && empty($contact->getLastName())) {
            return [];
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('c');
        $qb->from(Entity\Contact::class, 'c');
        $qb->where($qb->expr()->neq('c.id', ':contactId'));
        $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->andX(
                    $qb->expr()->like('c.firstName', ':firstName'),
                    $qb->expr()->like('c.lastName', ':lastName')
                ),
                $qb->expr()->eq("REPLACE(c.email,'.','')", ':email')
            )
        );
        $qb->orderBy('c.lastName', Criteria::ASC);
        $qb->addOrderBy('c.middleName', Criteria::ASC);
        $qb->addOrderBy('c.firstName', Criteria::ASC);

        $qb->setParameter('contactId', $contact->getId());
        $qb->setParameter('firstName', '%' . $contact->getFirstName() . '%');
        $qb->setParameter('lastName', '%' . $contact->getLastName() . '%');
        $qb->setParameter('email', str_replace('.', '', $contact->getEmail()));

        return $qb->getQuery()->useQueryCache(true)->getResult();
    }

    public function findNewCommunityMembers(): array
    {
        $emConfig = $this->_em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('YEAR', Year::class);

        $qb = $this->_em->createQueryBuilder();
        $qb->select(
            'YEAR(contact_entity_contact.dateCreated) as dateYearCreated',
            $qb->expr()->count('contact_entity_contact.id') . ' AS amount'
        );
        $qb->from(Entity\Contact::class, 'contact_entity_contact');


        //Limit to the ones which have a pageview
        $pageViewsQuery = $this->_em->createQueryBuilder();
        $pageViewsQuery->select('admin_entity_pageview_contact');
        $pageViewsQuery->from(Pageview::class, 'admin_entity_pageview');
        $pageViewsQuery->join('admin_entity_pageview.contact', 'admin_entity_pageview_contact');

        $qb->where($qb->expr()->in('contact_entity_contact', $pageViewsQuery->getDQL()));

        $qb->groupBy('dateYearCreated');

        return $qb->getQuery()->getResult();
    }
}
