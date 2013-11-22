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
class Address extends EntityRepository
{
    /**
     * @param Entity\Contact $contact
     * @param                $type
     *
     * @return null|Entity\Address
     */
    public function findAddressByContactAndType(Entity\Contact $contact, $type)
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

        $result = $queryBuilder->getQuery()->getResult();

        //Limit to 1 to have only 1 match
        if (sizeof($result) > 0) {
            return $result[0];
        } else {
            return null;
        }
    }
}