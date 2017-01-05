<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

namespace Contact\Repository;

use Contact\Entity;
use Doctrine\ORM\EntityRepository;

/**
 * @category    Contact
 */
class Address extends EntityRepository
{
    /**
     * @param Entity\Contact     $contact
     * @param Entity\AddressType $type
     *
     * @return null|Entity\Address
     */
    public function findAddressByContactAndType(Entity\Contact $contact, Entity\AddressType $type)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('a');
        $queryBuilder->from('Contact\Entity\Address', 'a');
        $queryBuilder->join('a.type', 't');
        $queryBuilder->join('t.subSort', 's');
        $queryBuilder->where('a.contact = ?1');
        $queryBuilder->andWhere('s.mainType = ?2');
        $queryBuilder->setParameter(1, $contact);
        $queryBuilder->setParameter(2, $type);
        $queryBuilder->orderBy('s.sort', 'ASC');
        $queryBuilder->setMaxResults(1);

        return $queryBuilder->getQuery()->useQueryCache(true)->getOneOrNullResult();
    }
}
