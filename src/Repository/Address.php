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
use Doctrine\ORM\EntityRepository;

/**
 * Class Address
 *
 * @package Contact\Repository
 */
class Address extends EntityRepository
{
    public function findAddressByContactAndType(Entity\Contact $contact, Entity\AddressType $type)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('contact_entity_address');
        $qb->from(Entity\Address::class, 'contact_entity_address');
        $qb->join('contact_entity_address.type', 'contact_entity_address_type');
        $qb->join('contact_entity_address_type.subSort', 'contact_entity_address_type_subsort');
        $qb->where('contact_entity_address.contact = :contact');
        $qb->andWhere('contact_entity_address_type_subsort.mainType = :maintype');
        $qb->setParameter('contact', $contact);
        $qb->setParameter('maintype', $type);
        $qb->orderBy('contact_entity_address_type_subsort.sort', 'ASC');
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
