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
class Selection extends EntityRepository
{
    /**
     * @param Entity\Contact $contact
     *
     * @return null|Entity\Selection
     */
    public function findFixedSelectionsByContact(Entity\Contact $contact)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('s');
        $queryBuilder->from('Contact\Entity\Selection', 's');

        $subSelect = $this->_em->createQueryBuilder();
        $subSelect->select('selection.id');
        $subSelect->from('Contact\Entity\Selection', 'selection');
        $subSelect->join('selection.contact', 'contact');
        $subSelect->where('contact = :contact');
        $queryBuilder->setParameter('contact', $contact);

        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->in('s.id', $subSelect->getDQL())
            )
        );

        return $queryBuilder->getQuery()->getResult();
    }
}