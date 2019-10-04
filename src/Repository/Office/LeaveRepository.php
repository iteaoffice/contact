<?php
/**
*
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Contact\Repository\Office;

use Contact\Entity\Office\Leave;
use Contact\Repository\FilteredObjectRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Can't be final because of unit test
 *
 * Class LeaveRepository
 * @package Contact\Office\Repository
 */
/*final*/ class LeaveRepository extends EntityRepository implements FilteredObjectRepository
{
    public function findFiltered(array $filter = []): QueryBuilder
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('l', 'lt');
        $queryBuilder->from(Leave::class, 'l');
        $queryBuilder->innerJoin('l.type', 'lt');

        if (isset($filter['officeContact'])) {
            $queryBuilder->where($queryBuilder->expr()->eq('l.officeContact', ':officeContact'));
            $queryBuilder->setParameter('officeContact', $filter['officeContact']);
        }

        if (isset($filter['type'])) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('lt.id', ':typeId'));
            $queryBuilder->setParameter('typeId', $filter['type']);
        }

        // Set the sorting
        if (isset($filter['order'])) {
            $direction = strtoupper($filter['direction']);
            switch ($filter['order']) {
                case 'description':
                    $queryBuilder->orderBy('l.description', $direction);
                    break;
                case 'hours':
                    $queryBuilder->orderBy('l.hours', $direction);
                    break;
                case 'dateStart':
                    $queryBuilder->orderBy('l.dateStart', $direction);
                    break;
                case 'dateEnd':
                    $queryBuilder->orderBy('l.dateEnd', $direction);
                    break;
            }
        } else {
            $queryBuilder->orderBy('l.dateStart', Criteria::DESC);
        }

        return $queryBuilder;
    }
}
