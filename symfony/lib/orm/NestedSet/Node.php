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

namespace OrangeHRM\ORM\NestedSet;

use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use OrangeHRM\ORM\Doctrine;
use OrangeHRM\ORM\ListSorter;

class Node implements NodeInterface
{
    /**
     * @var NestedSetInterface
     */
    protected NestedSetInterface $entity;

    /**
     * @param NestedSetInterface $entity
     */
    public function __construct(NestedSetInterface $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return NestedSetInterface
     */
    protected function getEntity(): NestedSetInterface
    {
        return $this->entity;
    }

    /**
     * @return int|null
     */
    protected function getLeftValue(): ?int
    {
        return $this->getEntity()->getLft();
    }

    /**
     * @return int|null
     */
    protected function getRightValue(): ?int
    {
        return $this->getEntity()->getRgt();
    }

    /**
     * @inheritDoc
     */
    public function hasChildren(): bool
    {
        return !empty($this->getChildren());
    }

    /**
     * @inheritDoc
     */
    public function getChildren(int $depth = 1): array
    {
        $q = Doctrine::getEntityManager()->getRepository(get_class($this->getEntity()))->createQueryBuilder('e');
        $q->andWhere('e.lft > :lft');
        $q->setParameter('lft', $this->getLeftValue());
        $q->andWhere('e.rgt < :rgt');
        $q->setParameter('rgt', $this->getRightValue());
        $q->andWhere('e.level <= :level');
        $q->setParameter('level', $this->getLevel() + $depth);
        $q->addOrderBy('e.lft', ListSorter::ASCENDING);

        return $q->getQuery()->execute();
    }

    /**
     * @inheritDoc
     * @throws NonUniqueResultException
     */
    public function hasParent(): bool
    {
        return !is_null($this->getParent());
    }

    /**
     * @inheritDoc
     * @throws NonUniqueResultException
     */
    public function getParent(): ?NestedSetInterface
    {
        $q = Doctrine::getEntityManager()->getRepository(get_class($this->getEntity()))->createQueryBuilder('e');
        $q->andWhere('e.lft < :lft');
        $q->setParameter('lft', $this->getLeftValue());
        $q->andWhere('e.rgt > :rgt');
        $q->setParameter('rgt', $this->getRightValue());
        $q->andWhere('e.level >= :level');
        $q->setParameter('level', $this->getLevel() - 1);
        $q->addOrderBy('e.rgt', ListSorter::ASCENDING);

        $result = $q->getQuery()->getOneOrNullResult();
        if ($result instanceof NestedSetInterface) {
            return $result;
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getLevel(): int
    {
        return $this->getEntity()->getLevel() ?? 0;
    }

    /**
     * @inheritDoc
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ConnectionException
     */
    public function insertAsLastChildOf(NestedSetInterface $parent): void
    {
        $newLeft = $parent->getRgt();
        $newRight = $parent->getRgt() + 1;

        $conn = Doctrine::getEntityManager()->getConnection();
        try {
            $conn->beginTransaction();

            $this->shiftRlValues($newLeft, 2);
            $this->getEntity()->setLevel(($parent->getLevel() ?? 0) + 1);
            $this->persistNode($newLeft, $newRight);

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            throw $e;
        }
    }

    /**
     * @param int $first
     * @param int $delta
     */
    protected function shiftRlValues(int $first, int $delta): void
    {
        $qLeft = Doctrine::getEntityManager()->createQueryBuilder();
        $qLeft->update(get_class($this->getEntity()), 'e')
            ->set('e.lft', 'e.lft + :delta')
            ->setParameter('delta', $delta)
            ->where('e.lft >= :lft')
            ->setParameter('lft', $first);
        $qLeft->getQuery()->execute();

        $qRight = Doctrine::getEntityManager()->createQueryBuilder();
        $qRight->update(get_class($this->getEntity()), 'e')
            ->set('e.rgt', 'e.rgt + :delta')
            ->setParameter('delta', $delta)
            ->where('e.rgt >= :rgt')
            ->setParameter('rgt', $first);
        $qRight->getQuery()->execute();
    }

    /**
     * @param int $lft
     * @param int $rgt
     * @throws ORMException
     * @throws OptimisticLockException
     */
    protected function persistNode($lft = 0, $rgt = 0): void
    {
        $this->getEntity()->setLft($lft);
        $this->getEntity()->setRgt($rgt);
        Doctrine::getEntityManager()->persist($this->getEntity());
        Doctrine::getEntityManager()->flush();
    }

    /**
     * @inheritDoc
     */
    public function addChild(NestedSetInterface $child): void
    {
        $child->getNode()->insertAsLastChildOf($this->getEntity());
    }

    /**
     * @inheritDoc
     */
    public function isLeaf(): bool
    {
        return (($this->getRightValue() - $this->getLeftValue()) == 1);
    }

    /**
     * @inheritDoc
     */
    public function isRoot(): bool
    {
        return ($this->getLeftValue() == 1);
    }

    /**
     * @inheritDoc
     */
    public function delete()
    {
        $em = Doctrine::getEntityManager();
        $q = $em->getRepository(get_class($this->getEntity()))->createQueryBuilder('e');
        $q->andWhere('e.lft >= :lft');
        $q->setParameter('lft', $this->getLeftValue());
        $q->andWhere('e.rgt <= :rgt');
        $q->setParameter('rgt', $this->getRightValue());

        $conn = $em->getConnection();
        try {
            $conn->beginTransaction();
            $nodes = $q->getQuery()->execute();
            foreach ($nodes as $node) {
                $em->remove($node);
            }
            $em->flush();

            $first = $this->getRightValue() + 1;
            $delta = $this->getLeftValue() - $this->getRightValue() - 1;
            $this->shiftRlValues($first, $delta);

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            throw $e;
        }
    }
}
