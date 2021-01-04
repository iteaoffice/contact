<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\Repository\Selection;

use Contact\Entity;
use Contact\Repository\FilteredObjectRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

use function array_key_exists;
use function in_array;

/**
 * Class OptIn
 * @package Contact\Repository
 */
final class Type extends EntityRepository implements FilteredObjectRepository
{
    public function findFiltered(array $filter = []): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('contact_entity_selection_type');
        $qb->from(Entity\Selection\Type::class, 'contact_entity_selection_type');

        if (array_key_exists('search', $filter)) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('contact_entity_selection_type.name', ':like'),
                    $qb->expr()->like('contact_entity_selection_type.description', ':like')
                )
            );

            $qb->setParameter('like', sprintf('%%%s%%', $filter['search']));
        }

        $direction = Criteria::ASC;
        if (isset($filter['direction']) && in_array(strtoupper($filter['direction']), [Criteria::ASC, Criteria::DESC], true)) {
            $direction = strtoupper($filter['direction']);
        }

        switch ($filter['order']) {
            case 'name':
                $qb->addOrderBy('contact_entity_selection_type.name', $direction);
                break;
            case 'description':
                $qb->addOrderBy('contact_entity_selection_type.description', $direction);
                break;
            default:
                $qb->addOrderBy('contact_entity_selection_type.name', $direction);
        }

        return $qb;
    }
}
