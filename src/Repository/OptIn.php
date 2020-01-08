<?php

/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Repository;

use Contact\Entity;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

use function array_key_exists;
use function in_array;

/**
 * Class OptIn
 *
 * @package Contact\Repository
 */
class OptIn extends EntityRepository
{
    public function findFiltered(array $filter): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('contact_entity_optin');
        $qb->from(Entity\OptIn::class, 'contact_entity_optin');

        if (array_key_exists('search', $filter)) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('contact_entity_optin.optIn', ':like'),
                    $qb->expr()->like('contact_entity_optin.description', ':like')
                )
            );

            $qb->setParameter('like', sprintf('%%%s%%', $filter['search']));
        }

        if (array_key_exists('active', $filter)) {
            $qb->andWhere($qb->expr()->in('contact_entity_optin.active', $filter['active']));
        }


        $direction = Criteria::ASC;
        if (isset($filter['direction']) && in_array(strtoupper($filter['direction']), ['ASC', 'DESC'], true)) {
            $direction = strtoupper($filter['direction']);
        }

        switch ($filter['order']) {
            case 'name':
                $qb->addOrderBy('contact_entity_optin.optIn', $direction);
                break;
            case 'description':
                $qb->addOrderBy('contact_entity_optin.description', $direction);
                break;
            case 'active':
                $qb->addOrderBy('contact_entity_optin.active', $direction);
                break;
            default:
                $qb->addOrderBy('contact_entity_optin.id', $direction);
        }

        return $qb;
    }

    public function getAmountOfContactsInOptIn(Entity\OptIn $optIn): int
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select($qb->expr()->count('contact_entity_contact') . ' amount');
        $qb->from(Entity\OptIn::class, 'contact_entity_optin');
        $qb->join('contact_entity_optin.contact', 'contact_entity_contact');
        $qb->where('contact_entity_optin = :optIn');
        $qb->setParameter('optIn', $optIn);

        return (int)$qb->getQuery()->getSingleScalarResult();
    }
}
