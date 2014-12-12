<?php
/**
 * DebraNova copyright message placeholder
 *
 * @category    Contact
 * @package     Repository
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Contact\Repository;

use Contact\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @category    Contact
 * @package     Repository
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
        $queryBuilder->select('f');
        $queryBuilder->from('Contact\Entity\Facebook', 'f');
        $queryBuilder->leftJoin('f.access', 'a');

        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->in('a.access', $contact->getRoles()),
                $queryBuilder->expr()->in('f.public', [Entity\Facebook::IS_PUBLIC])
            )
        );

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param  Entity\Contact  $contact
     * @param  Entity\Facebook $facebook
     * @return bool
     */
    public function isContactInFacebook(Entity\Contact $contact, Entity\Facebook $facebook)
    {
        $resultSetMap = new ResultSetMapping();
        $resultSetMap->addEntityResult('Contact\Entity\Contact', 'c');
        /**
         * Don't map the contact_id because that will overwrite the existing contact object leaving an emtpy one
         */
        $resultSetMap->addFieldResult('c', 'email', 'email');

        $queryInString = sprintf(
            "SELECT %s FROM %s WHERE %s",
            $facebook->getContactKey(),
            $facebook->getFromClause(),
            $facebook->getWhereClause()
        );

        $query = $this->getEntityManager()->createNativeQuery(
            sprintf(
                "SELECT email FROM contact WHERE contact_id = %s AND contact_id IN (%s) AND date_end IS NULL",
                $contact->getId(),
                $queryInString
            ),
            $resultSetMap
        );

        return sizeof($query->getResult()) > 0;
    }

}
