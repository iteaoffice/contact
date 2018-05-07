<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Contact\Service;

use Admin\Entity\Access;
use Admin\Entity\Permit;
use Admin\Repository\Permit\Role;
use Contact\Entity;
use Contact\Entity\EntityAbstract;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

/**
 * Class AbstractService
 *
 * @package Project\Service
 */
abstract class AbstractService
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * ActionService constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $entity
     * @param array  $filter
     *
     * @return QueryBuilder
     */
    public function findFiltered(string $entity, array $filter): QueryBuilder
    {
        return $this->entityManager->getRepository($entity)->findFiltered(
            $filter,
            AbstractQuery::HYDRATE_SIMPLEOBJECT
        );
    }

    /**
     * @param string $entity
     *
     * @return array|EntityAbstract[]
     */
    public function findAll(string $entity): array
    {
        return $this->entityManager->getRepository($entity)->findAll();
    }

    /**
     * @param string $entity
     * @param int    $id
     *
     * @return null|object|EntityAbstract
     */
    public function find(string $entity, int $id): ?EntityAbstract
    {
        return $this->entityManager->getRepository($entity)->find($id);
    }

    /**
     * @param EntityAbstract $entity
     *
     * @return EntityAbstract
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(EntityAbstract $entity): EntityAbstract
    {
        if (!$this->entityManager->contains($entity)) {
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();

        return $entity;
    }

    /**
     * @param EntityAbstract $EntityAbstract
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(EntityAbstract $EntityAbstract): void
    {
        $this->entityManager->remove($EntityAbstract);
        $this->entityManager->flush();
    }

    /**
     * @param EntityAbstract $EntityAbstract
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function refresh(EntityAbstract $EntityAbstract): void
    {
        $this->entityManager->refresh($EntityAbstract);
    }
}
