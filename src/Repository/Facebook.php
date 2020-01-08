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
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * Class Facebook
 *
 * @package Contact\Repository
 */
class Facebook extends EntityRepository
{
    public function findFacebookByRoles(array $roles): array
    {
        //Select projects based on a type
        $qb = $this->_em->createQueryBuilder();
        $qb->select('contact_entity_facebook');
        $qb->from(Entity\Facebook::class, 'contact_entity_facebook');
        $qb->leftJoin('contact_entity_facebook.access', 'admin_entity_access');

        $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->in('admin_entity_access.access', $roles),
                $qb->expr()->in('contact_entity_facebook.public', [Entity\Facebook::IS_PUBLIC])
            )
        );

        return $qb->getQuery()->useQueryCache(true)->getResult();
    }

    public function isContactInFacebook(Entity\Contact $contact, Entity\Facebook $facebook): bool
    {
        $resultSetMap = new ResultSetMapping();
        $resultSetMap->addEntityResult(Entity\Contact::class, 'contact_entity_contact');
        /*
         * Don't map the contact_id because that will overwrite the existing contact object leaving an empty one
         */
        $resultSetMap->addFieldResult('contact_entity_contact', 'email', 'email');

        $queryInString = sprintf(
            'SELECT %s FROM %s WHERE %s',
            $facebook->getContactKey(),
            $facebook->getFromClause(),
            $facebook->getWhereClause()
        );

        $query = $this->getEntityManager()->createNativeQuery(
            sprintf(
                'SELECT email FROM contact WHERE contact_id = %s AND contact_id IN (%s) AND date_end IS NULL',
                $contact->getId(),
                $queryInString
            ),
            $resultSetMap
        );

        return count($query->getResult(AbstractQuery::HYDRATE_ARRAY)) > 0;
    }
}
