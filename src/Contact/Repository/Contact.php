<?php
/**
 * DebraNova copyright message placeholder
 *
 * @category    Contact
 * @package     Repository
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
namespace Contact\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

use Contact\Entity;

/**
 * @category    Contact
 * @package     Repository
 */
class Contact extends EntityRepository
{
    /**
     * @param $email
     *
     * @return Contact|null
     */
    public function findContactByEmail($email)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('c');
        $queryBuilder->from('Contact\Entity\Contact', 'c');
        $queryBuilder->leftJoin('c.emailAddress', 'e');

        $queryBuilder->orWhere('c.email = ?1');
        $queryBuilder->orWhere('e.email = ?2');
        $queryBuilder->setParameter(1, $email);
        $queryBuilder->setParameter(2, $email);
        $queryBuilder->setMaxResults(1);

        //Limit to 1 to have only 1 match
        return $queryBuilder->getQuery()->getSingleResult();
    }
}
