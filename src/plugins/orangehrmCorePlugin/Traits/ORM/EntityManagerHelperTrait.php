<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\Core\Traits\ORM;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\ORM\QueryBuilderWrapper;

trait EntityManagerHelperTrait
{
    use EntityManagerTrait;

    /**
     * @param string $entityClass
     * @return ObjectRepository|EntityRepository
     *
     * @template T
     * @psalm-param class-string<T> $entityClass
     * @psalm-return EntityRepository<T>
     */
    protected function getRepository(string $entityClass)
    {
        return $this->getEntityManager()->getRepository($entityClass);
    }

    /**
     * @param $entity
     */
    protected function persist($entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * @param $entity
     */
    protected function remove($entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * @param string $entityClass
     * @param string $alias
     * @param string|null $indexBy
     * @return QueryBuilder
     */
    protected function createQueryBuilder(string $entityClass, string $alias, ?string $indexBy = null): QueryBuilder
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select($alias)
            ->from($entityClass, $alias, $indexBy);
    }

    /**
     * @param QueryBuilder $qb
     * @return Paginator
     */
    protected function getPaginator(QueryBuilder $qb): Paginator
    {
        return new Paginator($qb);
    }

    /**
     * @param QueryBuilder $qb
     * @return QueryBuilderWrapper
     */
    protected function getQueryBuilderWrapper(QueryBuilder $qb): QueryBuilderWrapper
    {
        return new QueryBuilderWrapper($qb);
    }

    /**
     * @param QueryBuilder $qb
     * @param int $offset
     * @return object|null
     */
    protected function fetchOne(QueryBuilder $qb, int $offset = 0): ?object
    {
        $result = $qb->setFirstResult($offset)
            ->setMaxResults(1)
            ->getQuery()
            ->execute();
        return $result[0] ?? null;
    }

    /**
     * @param string $entityName The name of the entity type.
     * @param mixed $id The entity identifier.
     * @return object|null The entity reference.
     *
     * @template T
     * @psalm-param class-string<T> $entityName
     * @psalm-return ?T
     */
    protected function getReference(string $entityName, $id)
    {
        return $this->getEntityManager()->getReference($entityName, $id);
    }

    /**
     * @return bool
     */
    protected function beginTransaction(): bool
    {
        return $this->getEntityManager()->getConnection()->beginTransaction();
    }

    /**
     * @return bool
     */
    protected function commitTransaction(): bool
    {
        return $this->getEntityManager()->getConnection()->commit();
    }

    /**
     * @return bool
     */
    protected function rollBackTransaction(): bool
    {
        return $this->getEntityManager()->getConnection()->rollBack();
    }
}
