<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\Service;

use Contact\Entity;
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
    protected EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

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
     * @return Entity\AbstractEntity[]
     */
    public function findAll(string $entity): array
    {
        return $this->entityManager->getRepository($entity)->findAll();
    }

    public function find(string $entity, int $id): ?Entity\AbstractEntity
    {
        return $this->entityManager->getRepository($entity)->find($id);
    }

    public function findBy(
        string $entity,
        array $criteria,
        array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null
    ): array {
        return $this->entityManager->getRepository($entity)->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function save(Entity\AbstractEntity $entity): Entity\AbstractEntity
    {
        if (! $this->entityManager->contains($entity)) {
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();

        return $entity;
    }

    public function delete(Entity\AbstractEntity $abstractEntity): void
    {
        $this->entityManager->remove($abstractEntity);
        $this->entityManager->flush();
    }

    public function refresh(Entity\AbstractEntity $abstractEntity): void
    {
        $this->entityManager->refresh($abstractEntity);
    }
}
