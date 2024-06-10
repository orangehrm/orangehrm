<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Claim\Dao;

use OrangeHRM\Claim\Dto\ClaimAttachmentSearchFilterParams;
use OrangeHRM\Claim\Dto\ClaimEventSearchFilterParams;
use OrangeHRM\Claim\Dto\ClaimExpenseSearchFilterParams;
use OrangeHRM\Claim\Dto\ClaimExpenseTypeSearchFilterParams;
use OrangeHRM\Claim\Dto\ClaimRequestSearchFilterParams;
use OrangeHRM\Claim\Dto\PartialClaimAttachment;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\ClaimAttachment;
use OrangeHRM\Entity\ClaimEvent;
use OrangeHRM\Entity\ClaimExpense;
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
     * @param int[] $ids
     * @return int[]
     */
    public function getExistingClaimEventIds(array $ids): array
    {
        $qb = $this->createQueryBuilder(ClaimEvent::class, 'claimEvent');

        $qb->select('claimEvent.id')
            ->andWhere($qb->expr()->in('claimEvent.id', ':ids'))
            ->andWhere($qb->expr()->eq('claimEvent.isDeleted', ':isDeleted'))
            ->setParameter('ids', $ids)
            ->setParameter('isDeleted', false);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param int[] $ids
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
        $q = $this->createQueryBuilder(ClaimRequest::class, 'request');
        $q->select($q->expr()->max('request.id'));
        $id = $q->getQuery()->getSingleScalarResult();
        return ++$id;
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
    protected function getClaimExpenseTypePaginator(
        ClaimExpenseTypeSearchFilterParams $claimExpenseTypeSearchFilterParams
    ): Paginator {
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

    /**
     * @param ClaimExpenseTypeSearchFilterParams $claimExpenseTypeSearchFilterParams
     * @return int
     */
    public function getClaimExpenseTypeCount(
        ClaimExpenseTypeSearchFilterParams $claimExpenseTypeSearchFilterParams
    ): int {
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

    /**
     * @param int[] $ids
     * @return int[]
     */
    public function getExistingExpenseTypeIds(array $ids): array
    {
        $qb = $this->createQueryBuilder(ExpenseType::class, 'expenseType');

        $qb->select('expenseType.id')
            ->andWhere($qb->expr()->in('expenseType.id', ':ids'))
            ->andWhere($qb->expr()->eq('expenseType.isDeleted', ':isDeleted'))
            ->setParameter('ids', $ids)
            ->setParameter('isDeleted', false);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param int[] $ids
     * @return int
     */
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

    /**
     * @param ClaimExpense $claimExpense
     * @return ClaimExpense
     */
    public function saveClaimExpense(ClaimExpense $claimExpense): ClaimExpense
    {
        $this->persist($claimExpense);
        return $claimExpense;
    }

    /**
     * @param ClaimExpenseSearchFilterParams $claimExpenseSearchFilterParams
     * @return array
     */
    public function getClaimExpenseList(ClaimExpenseSearchFilterParams $claimExpenseSearchFilterParams): array
    {
        $qb = $this->getClaimExpensePaginator($claimExpenseSearchFilterParams);
        return $qb->getQuery()->execute();
    }

    /**
     * @param ClaimExpenseSearchFilterParams $claimExpenseSearchFilterParams
     * @return Paginator
     */
    protected function getClaimExpensePaginator(
        ClaimExpenseSearchFilterParams $claimExpenseSearchFilterParams
    ): Paginator {
        $q = $this->createQueryBuilder(ClaimExpense::class, 'claimExpense');
        $this->setSortingAndPaginationParams($q, $claimExpenseSearchFilterParams);
        $claimRequest = new ClaimRequest();
        $claimRequest->setId($claimExpenseSearchFilterParams->getRequestId());
        $q->andWhere('claimExpense.claimRequest = :claimRequest');
        $q->setParameter('claimRequest', $claimRequest);
        $q->andWhere('claimExpense.isDeleted = :isDeleted');
        $q->setParameter('isDeleted', false);
        return $this->getPaginator($q);
    }

    /**
     * @param ClaimExpenseSearchFilterParams $claimExpenseSearchFilterParams
     * @return int
     */
    public function getClaimExpenseCount(ClaimExpenseSearchFilterParams $claimExpenseSearchFilterParams): int
    {
        return $this->getClaimExpensePaginator($claimExpenseSearchFilterParams)->count();
    }

    /**
     * @param ClaimExpenseSearchFilterParams $claimExpenseSearchFilterParams
     * @return float
     */
    public function getClaimExpenseTotal(ClaimExpenseSearchFilterParams $claimExpenseSearchFilterParams): float
    {
        $items = $this->getClaimExpensePaginator($claimExpenseSearchFilterParams)->getIterator();
        $total = 0;
        foreach ($items as $item) {
            $total += $item->getAmount();
        }
        return $total;
    }

    /**
     * @param int $id
     * @return ClaimExpense|null
     */
    public function getClaimExpenseById(int $id): ?ClaimExpense
    {
        return $this->getRepository(ClaimExpense::class)->findOneBy(['id' => $id, 'isDeleted' => false]);
    }

    /**
     * @param int $requestId
     * @param int $expenseId
     * @return ClaimExpense|null
     */
    public function getClaimRequestExpense(int $requestId, int $expenseId): ?ClaimExpense
    {
        $claimRequest = $this->getClaimRequestById($requestId);
        return $this->getRepository(ClaimExpense::class)->findOneBy(
            ['id' => $expenseId, 'claimRequest' => $claimRequest, 'isDeleted' => false]
        );
    }

    /**
     * @param int $id
     * @return ClaimRequest|null
     */
    public function getClaimRequestById(int $id): ?ClaimRequest
    {
        return $this->getRepository(ClaimRequest::class)->findOneBy(['id' => $id, 'isDeleted' => false]);
    }

    /**
     * @param int[] $ids
     * @return int[]
     */
    public function getExistingClaimExpenseIdsForRequestId(array $ids, int $requestId): array
    {
        $qb = $this->createQueryBuilder(ClaimExpense::class, 'claimExpense');

        $qb->select('claimExpense.id')
            ->andWhere($qb->expr()->in('claimExpense.id', ':ids'))
            ->andWhere($qb->expr()->eq('claimExpense.claimRequest', ':requestId'))
            ->andWhere($qb->expr()->eq('claimExpense.isDeleted', ':isDeleted'))
            ->setParameter('ids', $ids)
            ->setParameter('requestId', $requestId)
            ->setParameter('isDeleted', false);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param int $requestId
     * @param int[] $ids
     * @return int
     */
    public function deleteClaimExpense(int $requestId, array $ids): int
    {
        $claimRequest = $this->getReference(ClaimRequest::class, $requestId);
        $q = $this->createQueryBuilder(ClaimExpense::class, 'claimExpense');
        $q->update()
            ->set('claimExpense.isDeleted', ':isDeleted')
            ->andWhere($q->expr()->in('claimExpense.id', ':ids'))
            ->andWhere('claimExpense.claimRequest = :claimRequest')
            ->setParameter('claimRequest', $claimRequest)
            ->setParameter('ids', $ids)
            ->setParameter('isDeleted', true);
        return $q->getQuery()->execute();
    }

    /**
     * @param ClaimAttachment $claimAttachment
     * @return ClaimAttachment
     */
    public function saveClaimAttachment(ClaimAttachment $claimAttachment): ClaimAttachment
    {
        $this->persist($claimAttachment);
        return $claimAttachment;
    }

    /**
     * @param int $requestId
     * @return int
     */
    public function getNextAttachmentId(int $requestId): int
    {
        $q = $this->createQueryBuilder(ClaimAttachment::class, 'attachment');
        $q->select($q->expr()->max('attachment.attachId'))
            ->andWhere('attachment.requestId = :requestId')
            ->setParameter('requestId', $requestId);
        $id = $q->getQuery()->getSingleScalarResult();
        $id = $id === null ? 0 : intval($id);
        return ++$id;
    }

    /**
     * @param ClaimAttachmentSearchFilterParams $claimAttachmentSearchFilterParams
     * @return array
     */
    public function getClaimAttachmentList(ClaimAttachmentSearchFilterParams $claimAttachmentSearchFilterParams): array
    {
        $qb = $this->getClaimAttachmentPaginator($claimAttachmentSearchFilterParams);
        return $qb->getQuery()->execute();
    }

    /**
     * @param ClaimAttachmentSearchFilterParams $claimAttachmentSearchFilterParams
     * @return Paginator
     */
    protected function getClaimAttachmentPaginator(
        ClaimAttachmentSearchFilterParams $claimAttachmentSearchFilterParams
    ): Paginator {
        $select = 'NEW ' . PartialClaimAttachment::class
            . '(claimAttachment.requestId,
                 claimAttachment.attachId,
                 claimAttachment.size,
                 claimAttachment.description,
                 claimAttachment.filename,
                 claimAttachment.fileType,
                 Identity(claimAttachment.user),
                 claimAttachment.attachedDate)';
        $q = $this->createQueryBuilder(ClaimAttachment::class, 'claimAttachment')
            ->select($select);
        $this->setSortingAndPaginationParams($q, $claimAttachmentSearchFilterParams);
        if (!is_null($claimAttachmentSearchFilterParams->getRequestId())) {
            $requestId = $claimAttachmentSearchFilterParams->getRequestId();
            $q->andWhere('claimAttachment.requestId = :requestId');
            $q->setParameter('requestId', $requestId);
        }
        $this->setSortingAndPaginationParams($q, $claimAttachmentSearchFilterParams);
        return $this->getPaginator($q);
    }

    /**
     * @param int $requestId
     * @param int $attachId
     * @return PartialClaimAttachment|null
     */
    public function getPartialClaimAttachment(int $requestId, int $attachId): ?PartialClaimAttachment
    {
        $select = 'NEW ' . PartialClaimAttachment::class
            . '(claimAttachment.requestId,
                 claimAttachment.attachId,
                 claimAttachment.size,
                 claimAttachment.description,
                 claimAttachment.filename,
                 claimAttachment.fileType,
                 Identity(claimAttachment.user),
                 claimAttachment.attachedDate)';
        $q = $this->createQueryBuilder(ClaimAttachment::class, 'claimAttachment')
            ->select($select);
        $q->andWhere('claimAttachment.requestId = :requestId');
        $q->setParameter('requestId', $requestId);
        $q->andWhere('claimAttachment.attachId = :attachId');
        $q->setParameter('attachId', $attachId);
        return $q->getQuery()->getOneOrNullResult();
    }

    /**
     * @param ClaimAttachmentSearchFilterParams $claimAttachmentSearchFilterParams
     * @return int
     */
    public function getClaimAttachmentCount(ClaimAttachmentSearchFilterParams $claimAttachmentSearchFilterParams): int
    {
        $q = $this->createQueryBuilder(ClaimAttachment::class, 'claimAttachment');
        $this->setSortingAndPaginationParams($q, $claimAttachmentSearchFilterParams);
        if (!is_null($claimAttachmentSearchFilterParams->getRequestId())) {
            $q->andWhere('claimAttachment.requestId = :requestId');
            $q->setParameter('requestId', $claimAttachmentSearchFilterParams->getRequestId());
        }
        $this->setSortingAndPaginationParams($q, $claimAttachmentSearchFilterParams);
        return $this->getPaginator($q)->count();
    }

    /**
     * @param int $requestId
     * @param int $attachId
     * @return ClaimAttachment|null
     */
    public function getClaimAttachment(int $requestId, int $attachId): ?ClaimAttachment
    {
        return $this->getRepository(ClaimAttachment::class)->findOneBy(
            ['requestId' => $requestId, 'attachId' => $attachId]
        );
    }

    /**
     * @param int[] $ids
     * @param int $requestId
     * @return int[]
     */
    public function getExistingClaimAttachmentIdsForRequestId(array $ids, int $requestId): array
    {
        $qb = $this->createQueryBuilder(ClaimAttachment::class, 'claimAttachment');

        $qb->select('claimAttachment.attachId')
            ->andWhere($qb->expr()->eq('claimAttachment.requestId', ':requestId'))
            ->andWhere($qb->expr()->in('claimAttachment.attachId', ':ids'))
            ->setParameter('requestId', $requestId)
            ->setParameter('ids', $ids);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param int $requestId
     * @param int[] $ids
     */
    public function deleteClaimAttachments(int $requestId, array $ids): void
    {
        $q = $this->createQueryBuilder(ClaimAttachment::class, 'claimAttachment');
        $q->delete()
            ->andWhere('claimAttachment.requestId = :requestId')
            ->andWhere($q->expr()->in('claimAttachment.attachId', ':ids'))
            ->setParameter('requestId', $requestId)
            ->setParameter('ids', $ids);
        $q->getQuery()->execute();
    }

    /**
     * @param ClaimRequestSearchFilterParams $myClaimRequestSearchFilterParams
     * @return array
     */
    public function getClaimRequestList(ClaimRequestSearchFilterParams $myClaimRequestSearchFilterParams): array
    {
        $qb = $this->getClaimRequestPaginator($myClaimRequestSearchFilterParams);
        return $qb->getQuery()->execute();
    }

    /**
     * @param ClaimRequestSearchFilterParams $myClaimRequestSearchFilterParams
     * @return Paginator
     */
    protected function getClaimRequestPaginator(ClaimRequestSearchFilterParams $myClaimRequestSearchFilterParams): Paginator
    {
        $q = $this->createQueryBuilder(ClaimRequest::class, 'claimRequest');
        $q->leftJoin('claimRequest.claimEvent', 'claimEvent');
        $q->leftJoin('claimRequest.employee', 'employee');
        $this->setSortingAndPaginationParams($q, $myClaimRequestSearchFilterParams);

        if (!is_null($myClaimRequestSearchFilterParams->getEmpNumbers())) {
            $q->andWhere($q->expr()->in('claimRequest.employee', ':empNumbers'))
                ->setParameter('empNumbers', $myClaimRequestSearchFilterParams->getEmpNumbers());
        }

        if (!is_null($myClaimRequestSearchFilterParams->getReferenceId())) {
            $q->andWhere($q->expr()->like('claimRequest.referenceId', ':referenceId'));
            $q->setParameter('referenceId', '%' . $myClaimRequestSearchFilterParams->getReferenceId() . '%');
        }

        if (!is_null($myClaimRequestSearchFilterParams->getEventId())) {
            $q->andWhere('claimRequest.claimEvent = :eventId');
            $q->setParameter('eventId', $myClaimRequestSearchFilterParams->getEventId());
        }

        if (!is_null($myClaimRequestSearchFilterParams->getStatus())) {
            $q->andWhere($q->expr()->like('claimRequest.status', ':status'));
            $q->setParameter('status', '%' . $myClaimRequestSearchFilterParams->getStatus() . '%');
        }

        if (!is_null($myClaimRequestSearchFilterParams->getFromDate())) {
            $q->andWhere($q->expr()->gte('claimRequest.submittedDate', ':fromDate'));
            $q->setParameter('fromDate', $myClaimRequestSearchFilterParams->getFromDate());
        }

        if (!is_null($myClaimRequestSearchFilterParams->getToDate())) {
            $q->andWhere($q->expr()->lte('claimRequest.submittedDate', ':toDate'));
            $q->setParameter('toDate', $myClaimRequestSearchFilterParams->getToDate());
        }

        if ($myClaimRequestSearchFilterParams->getIncludeEmployees() === null ||
            $myClaimRequestSearchFilterParams->getIncludeEmployees() ===
            ClaimRequestSearchFilterParams::INCLUDE_EMPLOYEES_ONLY_CURRENT
        ) {
            $q->andWhere($q->expr()->isNull('employee.employeeTerminationRecord'));
        } elseif (
            $myClaimRequestSearchFilterParams->getIncludeEmployees() ===
            ClaimRequestSearchFilterParams::INCLUDE_EMPLOYEES_ONLY_PAST
        ) {
            $q->andWhere($q->expr()->isNotNull('employee.employeeTerminationRecord'));
        }

        $q->andWhere('claimRequest.isDeleted = :isDeleted');
        $q->setParameter('isDeleted', false);

        return $this->getPaginator($q);
    }

    /**
     * @param ClaimRequestSearchFilterParams $myClaimRequestSearchFilterParams
     * @return int
     */
    public function getClaimRequestCount(ClaimRequestSearchFilterParams $myClaimRequestSearchFilterParams): int
    {
        return $this->getClaimRequestPaginator($myClaimRequestSearchFilterParams)->count();
    }

    /**
     * @param int $requestId
     * @param int $attachId
     * @return ClaimAttachment|null
     */
    public function getClaimAttachmentFile(int $requestId, int $attachId): ?ClaimAttachment
    {
        $criteria = ['requestId' => $requestId, 'attachId' => $attachId];
        return $this->getRepository(ClaimAttachment::class)->findOneBy($criteria);
    }

    /**
     * @param int $eventId
     * @return bool
     */
    public function isClaimEventUsed(int $eventId): bool
    {
        $qb = $this->createQueryBuilder(ClaimRequest::class, 'claimRequest');
        $qb->andWhere('claimRequest.claimEvent = :eventId');
        $qb->setParameter('eventId', $eventId);
        return $this->getPaginator($qb)->count() > 0;
    }

    /**
     * @param int $expenseTypeId
     * @return bool
     */
    public function isExpenseTypeUsed(int $expenseTypeId): bool
    {
        $qb = $this->createQueryBuilder(ClaimExpense::class, 'claimExpense');
        $qb->leftJoin('claimExpense.expenseType', 'expenseType');
        $qb->andWhere('claimExpense.expenseType = :expenseTypeId');
        $qb->setParameter('expenseTypeId', $expenseTypeId);
        return $this->getPaginator($qb)->count() > 0;
    }
}
