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

namespace OrangeHRM\Claim\Dao;

use OrangeHRM\Claim\Dto\ClaimEventSearchFilterParams;
use OrangeHRM\Claim\Dto\ClaimExpenseTypeSearchFilterParams;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\ClaimEvent;
use OrangeHRM\Entity\ClaimRequest;
use OrangeHRM\Entity\ExpenseType;
use OrangeHRM\ORM\Paginator;

class ClaimDao extends BaseDao
{
    /**
     * @param ClaimEvent $claimEvent
     * @return ClaimEvent
     */
    public function saveEvent(ClaimEvent $claimEvent): ClaimEvent
    {
        $this->persist($claimEvent);
        return $claimEvent;
    }

    /**
     * @param ClaimEventSearchFilterParams $claimEventSearchFilterParams
     * @return array
     */
    public function getClaimEventList(ClaimEventSearchFilterParams $claimEventSearchFilterParams): array
    {
        $qb = $this->getClaimEventPaginator($claimEventSearchFilterParams);
        return $qb->getQuery()->execute();
    }

    /**
     * @param ClaimEventSearchFilterParams $claimEventSearchFilterParams
     * @return Paginator
     */
    protected function getClaimEventPaginator(ClaimEventSearchFilterParams $claimEventSearchFilterParams): Paginator
    {
        $q = $this->createQueryBuilder(ClaimEvent::class, 'claimEvent');
        $this->setSortingAndPaginationParams($q, $claimEventSearchFilterParams);
        if (!is_null($claimEventSearchFilterParams->getName())) {
            $q->andWhere($q->expr()->like('claimEvent.name', ':name'));
            $q->setParameter('name', '%' . $claimEventSearchFilterParams->getName() . '%');
        }
        if (!is_null($claimEventSearchFilterParams->getStatus())) {
            $q->andWhere('claimEvent.status = :status');
            $q->setParameter('status', $claimEventSearchFilterParams->getStatus());
        }
        if (!is_null($claimEventSearchFilterParams->getId())) {
            $q->andWhere('claimEvent.id = :id');
            $q->setParameter('id', $claimEventSearchFilterParams->getId());
        }
        $q->andWhere('claimEvent.isDeleted = :isDeleted');
        $q->setParameter('isDeleted', false);
        return $this->getPaginator($q);
    }

    /**
     * @param int $id
     * @return ClaimEvent|null
     */
    public function getClaimEventById(int $id): ?ClaimEvent
    {
        return $this->getRepository(ClaimEvent::class)->findOneBy(['id' => $id, 'isDeleted' => false]);
    }

    /**
     * @param array int[] $ids
     * @return int
     */
    public function deleteClaimEvents(array $ids): int
    {
        $q = $this->createQueryBuilder(ClaimEvent::class, 'claimEvent');
        $q->update()
            ->set('claimEvent.isDeleted', ':isDeleted')
            ->where($q->expr()->in('claimEvent.id', ':ids'))
            ->setParameter('ids', $ids)
            ->setParameter('isDeleted', true);
        return $q->getQuery()->execute();
    }

    /**
     * @param ClaimEventSearchFilterParams $claimEventSearchFilterParams
     * @return int
     */
    public function getClaimEventCount(ClaimEventSearchFilterParams $claimEventSearchFilterParams): int
    {
        return $this->getClaimEventPaginator($claimEventSearchFilterParams)->count();
    }

    /**
     * @param ExpenseType $expenseType
     * @return ExpenseType
     */
    public function saveExpenseType(ExpenseType $expenseType): ExpenseType
    {
        $this->persist($expenseType);
        return $expenseType;
    }

    /**
     * @param ClaimRequest $claimRequest
     * @return ClaimRequest
     */
    public function saveClaimRequest(ClaimRequest $claimRequest): ClaimRequest
    {
        $this->persist($claimRequest);
        return $claimRequest;
    }

    /**
     * @return int
     */
    public function getNextId(): int
    {
        $q = $this->createQueryBuilder(ClaimRequest::class, 'request')
            ->select('MAX(request.id)');
        $id = $q->getQuery()->execute();
        $id[0][1]++;

        return $id[0][1];
    }

    /**
     * @param ClaimExpenseTypeSearchFilterParams $claimExpenseTypeSearchFilterParams
     * @return array
     */
    public function getExpenseTypeList(ClaimExpenseTypeSearchFilterParams $claimExpenseTypeSearchFilterParams): array
    {
        $qb = $this->getClaimExpenseTypePaginator($claimExpenseTypeSearchFilterParams);
        return $qb->getQuery()->execute();
    }

    /**
     * @param ClaimExpenseTypeSearchFilterParams $claimExpenseTypeSearchFilterParams
     * @return Paginator
     */
    protected function getClaimExpenseTypePaginator(ClaimExpenseTypeSearchFilterParams $claimExpenseTypeSearchFilterParams): Paginator
    {
        $q = $this->createQueryBuilder(ExpenseType::class, 'expenseType');
        $this->setSortingAndPaginationParams($q, $claimExpenseTypeSearchFilterParams);
        if (!is_null($claimExpenseTypeSearchFilterParams->getName())) {
            $q->andWhere($q->expr()->like('expenseType.name', ':name'));
            $q->setParameter('name', '%' . $claimExpenseTypeSearchFilterParams->getName() . '%');
        }
        if (!is_null($claimExpenseTypeSearchFilterParams->getStatus())) {
            $q->andWhere('expenseType.status = :status');
            $q->setParameter('status', $claimExpenseTypeSearchFilterParams->getStatus());
        }
        if (!is_null($claimExpenseTypeSearchFilterParams->getId())) {
            $q->andWhere('expenseType.id = :id');
            $q->setParameter('id', $claimExpenseTypeSearchFilterParams->getId());
        }
        $q->andWhere('expenseType.isDeleted = :isDeleted');
        $q->setParameter('isDeleted', false);
        return $this->getPaginator($q);
    }

    public function getClaimExpenseTypeCount(ClaimExpenseTypeSearchFilterParams $claimExpenseTypeSearchFilterParams): int
    {
        return $this->getClaimExpenseTypePaginator($claimExpenseTypeSearchFilterParams)->count();
    }

    /**
     * @param int $id
     * @return ExpenseType|null
     */
    public function getExpenseTypeById(int $id): ?ExpenseType
    {
        return $this->getRepository(ExpenseType::class)->findOneBy(['id' => $id, 'isDeleted' => false]);
    }

    public function deleteExpenseTypes(array $ids): int
    {
        $q = $this->createQueryBuilder(ExpenseType::class, 'expenseType');
        $q->update()
            ->set('expenseType.isDeleted', ':isDeleted')
            ->where($q->expr()->in('expenseType.id', ':ids'))
            ->setParameter('ids', $ids)
            ->setParameter('isDeleted', true);
        return $q->getQuery()->execute();
    }
}
