<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\Repository\Office;

use Contact\Entity\Office\Contact as OfficeContact;
use Contact\Repository\FilteredObjectRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Can't be final because of unit test
 *
 * Class ContactRepository
 * @package Contact\Office\Repository
 */
/*final*/ class ContactRepository extends EntityRepository implements FilteredObjectRepository
{
    public function findFiltered(array $filter = []): QueryBuilder
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('oc', 'c');
        $queryBuilder->from(OfficeContact::class, 'oc');
        $queryBuilder->innerJoin('oc.contact', 'c');

        // Add active filter when present
        if (array_key_exists('active', $filter) && ($filter['active'] === 'active')) {
            $queryBuilder->where($queryBuilder->expr()->isNull('oc.dateEnd'));
        }

        // Set the sorting
        if (isset($filter['order'])) {
            $direction = strtoupper($filter['direction']);
            switch ($filter['order']) {
                case 'contact':
                    $queryBuilder->orderBy('c.firstName', $direction);
                    $queryBuilder->addOrderBy('c.middleName', $direction);
                    $queryBuilder->addOrderBy('c.lastName', $direction);
                    break;
                case 'hours':
                    $queryBuilder->orderBy('oc.hours', $direction);
                    break;
                case 'dateEnd':
                    $queryBuilder->orderBy('oc.dateEnd', $direction);
                    break;
            }
        } else {
            $queryBuilder->orderBy('c.id', Criteria::DESC);
        }

        return $queryBuilder;
    }
}
