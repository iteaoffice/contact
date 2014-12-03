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
        $queryBuilder->join('f.access', 'a');

        $queryBuilder->andWhere($queryBuilder->expr()->in('a.access', $contact->getRoles()));

        return $queryBuilder->getQuery()->getResult();
    }
}
