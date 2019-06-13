<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Repository;

use Contact\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use function array_key_exists;
use function in_array;

/**
 * Class Selection
 *
 * @package Contact\Repository
 */
final class Selection extends EntityRepository
{
    public function findFiltered(array $filter): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('contact_entity_selection');
        $qb->from(Entity\Selection::class, 'contact_entity_selection');

        if (array_key_exists('search', $filter)) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like(
                        'contact_entity_selection.selection',
                        ':like'
                    ),
                    $qb->expr()->like('contact_entity_selection.tag', ':like')
                )
            );


            $qb->setParameter('like', sprintf("%%%s%%", $filter['search']));
        }

        if (array_key_exists('sql', $filter)) {
            $qb->join('contact_entity_selection.sql', 'sql');
        }

        if (array_key_exists('tags', $filter)) {
            $qb->andWhere($qb->expr()->in('contact_entity_selection.tag', $filter['tags']));
        }

        if (!array_key_exists('includeDeleted', $filter)) {
            //Do not show the deleted ones
            $qb->andWhere($qb->expr()->isNull('contact_entity_selection.dateDeleted'));
        }

        if (array_key_exists('core', $filter)) {
            $qb->andWhere($qb->expr()->in('contact_entity_selection.core', $filter['core']));
        }

        $direction = 'ASC';
        if (isset($filter['direction']) && in_array(strtoupper($filter['direction']), ['ASC', 'DESC'], true)) {
            $direction = strtoupper($filter['direction']);
        }

        switch ($filter['order']) {
            case 'name':
                $qb->addOrderBy('contact_entity_selection.selection', $direction);
                break;
            case 'tag':
                $qb->addOrderBy('contact_entity_selection.tag', $direction);
                break;
            case 'core':
                $qb->addOrderBy('contact_entity_selection.core', $direction);
                break;
            case 'owner':
                $qb->join('contact_entity_selection.contact', 'contact');
                $qb->addOrderBy('contact.lastName', $direction);
                break;
            case 'date':
                $qb->addOrderBy('contact_entity_selection.dateCreated', $direction);
                break;
            default:
                $qb->addOrderBy('contact_entity_selection.id', $direction);
        }

        return $qb;
    }

    public function findSqlSelections(): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('contact_entity_selection');
        $qb->from(Entity\Selection::class, 'contact_entity_selection');
        $qb->innerJoin('contact_entity_selection.sql', 'contact_entity_selection_sql');
        $qb->orderBy('contact_entity_selection.selection', 'ASC');

        $qb->andWhere($qb->expr()->isNull('contact_entity_selection.dateDeleted'));

        return $qb->getQuery()->getResult();
    }

    public function findNonSqlSelections(): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('contact_entity_selection');
        $qb->from(Entity\Selection::class, 'contact_entity_selection');
        $qb->orderBy('contact_entity_selection.selection', 'ASC');

        $subSelect = $this->_em->createQueryBuilder();
        $subSelect->select('contact_entity_selection_sub.id');
        $subSelect->from(Entity\SelectionSql::class, 'contact_entity_selection_sql');
        $subSelect->join('contact_entity_selection_sql.selection', 'contact_entity_selection_sub');

        $qb->andWhere($qb->expr()->notIn('contact_entity_selection.id', $subSelect->getDQL()));

        $qb->andWhere($qb->expr()->isNull('contact_entity_selection.dateDeleted'));

        return $qb->getQuery()->getResult();
    }

    public function findTags(): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('contact_entity_selection.tag');
        $qb->distinct();
        $qb->from(Entity\Selection::class, 'contact_entity_selection');
        $qb->orderBy('contact_entity_selection.tag', 'ASC');

        $qb->andWhere($qb->expr()->isNull('contact_entity_selection.dateDeleted'));

        return $qb->getQuery()->getArrayResult();
    }

    public function findActive(): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('contact_entity_selection');
        $qb->from(Entity\Selection::class, 'contact_entity_selection');
        $qb->orderBy('contact_entity_selection.selection', 'ASC');

        $qb->andWhere($qb->expr()->isNull('contact_entity_selection.dateDeleted'));

        return $qb->getQuery()->getResult();
    }

    public function findFixedSelectionsByContact(Entity\Contact $contact): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('contact_entity_selection');
        $qb->from(Entity\Selection::class, 'contact_entity_selection');

        $subSelect = $this->_em->createQueryBuilder();
        $subSelect->select('selection.id');
        $subSelect->from(Entity\SelectionContact::class, 'contact_entity_selection_contact');
        $subSelect->join('contact_entity_selection_contact.contact', 'contact');
        $subSelect->join('contact_entity_selection_contact.selection', 'selection');
        $subSelect->where('contact = :contact');
        $qb->setParameter('contact', $contact);

        $qb->andWhere($qb->expr()->in('contact_entity_selection.id', $subSelect->getDQL()));

        return $qb->getQuery()->useQueryCache(true)->getResult();
    }
}
