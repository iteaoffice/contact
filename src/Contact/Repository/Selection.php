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
 * @category    Contact
 */
class Selection extends EntityRepository
{
    /**
     * @param array $filter
     * @return Query
     */
    public function findFiltered(array $filter)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('s');
        $queryBuilder->from('Contact\Entity\Selection', 's');

        if (array_key_exists('search', $filter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->like('s.selection', ':like'),
                    $queryBuilder->expr()->like('s.tag', ':like')
                )
            );

            $queryBuilder->setParameter('like', sprintf("%%%s%%", $filter['search']));
        }

        if (array_key_exists('sql', $filter)) {
            $queryBuilder->join('s.sql', 'sql');
        }

        if (array_key_exists('tags', $filter)) {
            $queryBuilder->andWhere($queryBuilder->expr()->in('s.tag', $filter['tags']));
        }

        $direction = 'ASC';
        if (isset($filter['direction']) && in_array(strtoupper($filter['direction']), ['ASC', 'DESC'])) {
            $direction = strtoupper($filter['direction']);
        }

        switch ($filter['order']) {
            case 'name':
                $queryBuilder->addOrderBy('s.selection', $direction);
                break;
            case 'tag':
                $queryBuilder->addOrderBy('s.tag', $direction);
                break;
            case 'owner':
                $queryBuilder->join('s.contact', 'contact');
                $queryBuilder->addOrderBy('contact.lastName', $direction);
                break;
            case 'date':
                $queryBuilder->addOrderBy('s.dateCreated', $direction);
                break;
            default:
                $queryBuilder->addOrderBy('s.id', $direction);

        }

        return $queryBuilder->getQuery();
    }

    /**
     * @return array
     */
    public function findTags()
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('s.tag');
        $queryBuilder->distinct();
        $queryBuilder->from("Contact\\Entity\\Selection", 's');
        $queryBuilder->orderBy('s.tag', 'ASC');

        return $queryBuilder->getQuery()->getArrayResult();
    }

    /**
     * @param Entity\Contact $contact
     *
     * @return null|Entity\Selection
     */
    public function findFixedSelectionsByContact(Entity\Contact $contact)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('s');
        $queryBuilder->from('Contact\Entity\Selection', 's');

        $subSelect = $this->_em->createQueryBuilder();
        $subSelect->select('selection.id');
        $subSelect->from('Contact\Entity\Selection', 'selection');
        $subSelect->join('selection.contact', 'contact');
        $subSelect->where('contact = :contact');
        $queryBuilder->setParameter('contact', $contact);

        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->in('s.id', $subSelect->getDQL())
            )
        );

        return $queryBuilder->getQuery()->useQueryCache(true)->getResult();
    }
}
