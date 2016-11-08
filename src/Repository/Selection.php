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

use Contact\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

/**
 * Class Selection
 *
 * @package Contact\Repository
 */
class Selection extends EntityRepository
{
    /**
     * @param array $filter
     *
     * @return Query
     */
    public function findFiltered(array $filter)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('contact_entity_selection');
        $queryBuilder->from(Entity\Selection::class, 'contact_entity_selection');

        if (array_key_exists('search', $filter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->like(
                        'contact_entity_selection.selection',
                        ':like'
                    ),
                    $queryBuilder->expr()->like('contact_entity_selection.tag', ':like')
                )
            );


            $queryBuilder->setParameter('like', sprintf("%%%s%%", $filter['search']));
        }

        if (array_key_exists('sql', $filter)) {
            $queryBuilder->join('contact_entity_selection.sql', 'sql');
        }

        if (array_key_exists('tags', $filter)) {
            $queryBuilder->andWhere($queryBuilder->expr()->in('contact_entity_selection.tag', $filter['tags']));
        }

        if (! array_key_exists('includeDeleted', $filter)) {
            //Do not show the deleted ones
            $queryBuilder->andWhere($queryBuilder->expr()->isNull('contact_entity_selection.dateDeleted'));
        }

        $direction = 'ASC';
        if (isset($filter['direction']) && in_array(strtoupper($filter['direction']), ['ASC', 'DESC'])) {
            $direction = strtoupper($filter['direction']);
        }

        switch ($filter['order']) {
            case 'name':
                $queryBuilder->addOrderBy('contact_entity_selection.selection', $direction);
                break;
            case 'tag':
                $queryBuilder->addOrderBy('contact_entity_selection.tag', $direction);
                break;
            case 'owner':
                $queryBuilder->join('contact_entity_selection.contact', 'contact');
                $queryBuilder->addOrderBy('contact.lastName', $direction);
                break;
            case 'date':
                $queryBuilder->addOrderBy('contact_entity_selection.dateCreated', $direction);
                break;
            default:
                $queryBuilder->addOrderBy('contact_entity_selection.id', $direction);
        }

        return $queryBuilder->getQuery();
    }

    /**
     * @return array
     */
    public function findTags()
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('contact_entity_selection.tag');
        $queryBuilder->distinct();
        $queryBuilder->from("Contact\Entity\Selection", 'contact_entity_selection');
        $queryBuilder->orderBy('contact_entity_selection.tag', 'ASC');

        $queryBuilder->andWhere($queryBuilder->expr()->isNull('contact_entity_selection.dateDeleted'));

        return $queryBuilder->getQuery()->getArrayResult();
    }

    /**
     * @return array
     */
    public function findActive()
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('contact_entity_selection');
        $queryBuilder->from("Contact\Entity\Selection", 'contact_entity_selection');
        $queryBuilder->orderBy('contact_entity_selection.selection', 'ASC');

        $queryBuilder->andWhere($queryBuilder->expr()->isNull('contact_entity_selection.dateDeleted'));

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param Entity\Contact $contact
     *
     * @return null|Entity\Selection
     */
    public function findFixedSelectionsByContact(Entity\Contact $contact)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('contact_entity_selection');
        $queryBuilder->from(Entity\Selection::class, 'contact_entity_selection');

        $subSelect = $this->_em->createQueryBuilder();
        $subSelect->select('selection.id');
        $subSelect->from(Entity\SelectionContact::class, 'contact_entity_selection_contact');
        $subSelect->join('contact_entity_selection_contact.contact', 'contact');
        $subSelect->join('contact_entity_selection_contact.selection', 'selection');
        $subSelect->where('contact = :contact');
        $queryBuilder->setParameter('contact', $contact);

        $queryBuilder->andWhere($queryBuilder->expr()->in('contact_entity_selection.id', $subSelect->getDQL()));

        return $queryBuilder->getQuery()->useQueryCache(true)->getResult();
    }
}
