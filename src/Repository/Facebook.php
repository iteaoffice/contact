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
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @category    Contact
 */
class Facebook extends EntityRepository
{
    /**
     * @param Entity\Contact $contact
     *
     * @return Entity\Facebook[]
     */
    public function findFacebookByContact(Entity\Contact $contact)
    {
        //Select projects based on a type
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('contact_entity_facebook');
        $queryBuilder->from(Entity\Facebook::class, 'contact_entity_facebook');
        $queryBuilder->leftJoin('contact_entity_facebook.access', 'admin_entity_access');

        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->in('admin_entity_access.access', $contact->getRoles()),
                $queryBuilder->expr()->in('contact_entity_facebook.public', [Entity\Facebook::IS_PUBLIC])
            )
        );

        return $queryBuilder->getQuery()->useQueryCache(true)->getResult();
    }

    /**
     * @param Entity\Contact $contact
     * @param Entity\Facebook $facebook
     *
     * @return bool
     */
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

        /*
         * Use a simple array hydration to avoid a complex mapping of objects
         */

        return count($query->getResult(AbstractQuery::HYDRATE_ARRAY)) > 0;
    }
}
