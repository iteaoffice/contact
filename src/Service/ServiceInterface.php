<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

namespace Contact\Service;

use Contact\Entity\EntityAbstract;

interface ServiceInterface
{
    /**
     * @param EntityAbstract $entity
     *
     * @return EntityAbstract $entity
     */
    public function updateEntity(EntityAbstract $entity);

    /**
     * @param EntityAbstract $entity
     *
     * @return EntityAbstract
     */
    public function newEntity(EntityAbstract $entity);

    /**
     * @param $entity
     *
     * @return EntityAbstract[]
     */
    public function findAll($entity);
}
