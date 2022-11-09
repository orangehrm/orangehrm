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

namespace OrangeHRM\Leave\Dao;

use DateTime;
use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Entity\LeaveEntitlement;
use OrangeHRM\Entity\LeaveLeaveEntitlement;
use OrangeHRM\Entity\LeaveRequest;
use OrangeHRM\Entity\LeaveRequestComment;
use OrangeHRM\Entity\LeaveStatus;
use OrangeHRM\Leave\Dto\CurrentAndChangeEntitlement;
use OrangeHRM\Leave\Dto\LeaveRequestSearchFilterParams;
use OrangeHRM\Leave\Dto\LeaveSearchFilterParams;
use OrangeHRM\Leave\Traits\Service\LeaveRequestServiceTrait;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\ORM\QueryBuilderWrapper;

class LeaveRequestDao extends BaseDao
{
    use DateTimeHelperTrait;
    use LeaveRequestServiceTrait;

    private bool $doneMarkingApprovedLeaveAsTaken = false;

    /**
     * Save leave request
     *
     * @param LeaveRequest $leaveRequest Leave request object
     * @param Leave[] $leaveList Array of leave objects linked to the leave request
     * @param CurrentAndChangeEntitlement $entitlements Array of entitlements to be modified
     * @return LeaveRequest
     * @throws TransactionException
     */
    public function saveLeaveRequest(
        LeaveRequest $leaveRequest,
        array $leaveList,
        CurrentAndChangeEntitlement $entitlements
    ): LeaveRequest {
        $this->beginTransaction();

        try {
            $this->getEntityManager()->persist($leaveRequest);
            $current = $entitlements->getCurrent();

            foreach ($leaveList as $leave) {
                $leave->setLeaveRequest($leaveRequest);
                $leave->setLeaveType($leaveRequest->getLeaveType());
                $leave->setEmployee($leaveRequest->getEmployee());

                $this->getEntityManager()->persist($leave);

                if (isset($current[$leave->getDate()->format('Y-m-d')])) {
                    $entitlementsForDate = $current[$leave->getDate()->format('Y-m-d')];
                    foreach ($entitlementsForDate as $entitlementId => $length) {
                        $le = new LeaveLeaveEntitlement();
                        $le->setLeave($leave);
                        $le->getDecorator()->setLeaveEntitlementById($entitlementId);
                        $le->setLengthDays($length);
                        $this->getEntityManager()->persist($le);

                        /** @var LeaveEntitlement|null $leaveEntitlement */
                        $leaveEntitlement = $this->getRepository(LeaveEntitlement::class)->find($entitlementId);
                        if ($leaveEntitlement instanceof LeaveEntitlement) {
                            $leaveEntitlement->setDaysUsed($leaveEntitlement->getDaysUsed() + $length);
                        }
                        $this->getEntityManager()->persist($leaveEntitlement);
                    }
                }
            }
            $this->getEntityManager()->flush();

            if (!empty($entitlements->getChange())) {
                // TODO: Need to update days_used here
                // Also need to check if we need to delete all entitlements or only have changes

                $changes = $entitlements->getChange();

                foreach ($changes as $leaveId => $change) {
                    $this->createQueryBuilder(LeaveLeaveEntitlement::class, 'l')
                        ->delete()
                        ->where('l.leave = :leaveId')
                        ->setParameter('leaveId', $leaveId)
                        ->getQuery()
                        ->execute();

                    foreach ($change as $entitlementId => $length) {
                        $le = new LeaveLeaveEntitlement();
                        $le->getDecorator()->setLeaveById($leaveId);
                        $le->getDecorator()->setLeaveEntitlementById($entitlementId);
                        $le->setLengthDays($length);
                        $this->getEntityManager()->persist($le);
                    }
                    $this->getEntityManager()->flush();
                }
            }

            $this->commitTransaction();
            $leaveRequest->setLeaves($leaveList);
            return $leaveRequest;
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }
    }

    /**
     * @param LeaveRequestComment $leaveRequestComment
     * @return LeaveRequestComment
     */
    public function saveLeaveRequestComment(LeaveRequestComment $leaveRequestComment): LeaveRequestComment
    {
        if (is_null($leaveRequestComment->getCreatedAt())) {
            $leaveRequestComment->setCreatedAt($this->getDateTimeHelper()->getNow());
        }
        $this->persist($leaveRequestComment);
        return $leaveRequestComment;
    }

    /**
     * @param Leave $leave
     * @return Leave
     */
    public function saveLeave(Leave $leave): Leave
    {
        $this->persist($leave);
        return $leave;
    }

    /**
     * @param Leave $leave
     * @param CurrentAndChangeEntitlement|null $entitlementChanges
     * @param bool $removeLinkedEntitlements
     * @throws TransactionException
     */
    public function changeLeaveStatus(
        Leave $leave,
        ?CurrentAndChangeEntitlement $entitlementChanges = null,
        bool $removeLinkedEntitlements = false
    ): void {
        $this->beginTransaction();
        try {
            if ($removeLinkedEntitlements) {
                $leaveId = $leave->getId();
                /** @var LeaveLeaveEntitlement[] $leaveLeaveEntitlements */
                $leaveLeaveEntitlements = $this->getRepository(LeaveLeaveEntitlement::class)
                    ->findBy(['leave' => $leaveId]);
                foreach ($leaveLeaveEntitlements as $leaveLeaveEntitlement) {
                    $leaveEntitlement = $leaveLeaveEntitlement->getEntitlement();
                    if ($leaveEntitlement->getDaysUsed() < $leaveLeaveEntitlement->getLengthDays()) {
                        $leaveEntitlement->setDaysUsed(0);
                    } else {
                        $leaveEntitlement->setDaysUsed(
                            $leaveEntitlement->getDaysUsed() - $leaveLeaveEntitlement->getLengthDays()
                        );
                    }
                }
                $this->getEntityManager()->flush();

                $this->createQueryBuilder(LeaveLeaveEntitlement::class, 'le')
                    ->delete()
                    ->where('le.leave = :leaveId')
                    ->setParameter('leaveId', $leaveId)
                    ->getQuery()
                    ->execute();
            }

            $this->persist($leave);

            if (!is_null($entitlementChanges) && !empty($entitlementChanges->getChange())) {
                // TODO: Need to update days_used here
                // Also need to check if we need to delete all entitlements or only have changes

                $changes = $entitlementChanges->getChange();

                foreach ($changes as $leaveId => $change) {
                    foreach ($change as $entitlementId => $length) {
                        $leaveEntitlement = $this->getRepository(LeaveEntitlement::class)
                            ->find($entitlementId);
                        if ($leaveEntitlement instanceof LeaveEntitlement) {
                            $leaveEntitlement->setDaysUsed($leaveEntitlement->getDaysUsed() + $length);
                        }

                        $q = $this->createQueryBuilder(LeaveLeaveEntitlement::class, 'le')
                            ->andWhere('le.leave = :leaveId')
                            ->setParameter('leaveId', $leaveId)
                            ->andWhere('le.entitlement = :entitlementId')
                            ->setParameter('entitlementId', $entitlementId);
                        $entitlementAssignment = $this->fetchOne($q);

                        if (is_null($entitlementAssignment)) {
                            $entitlementAssignment = new LeaveLeaveEntitlement();
                            $entitlementAssignment->getDecorator()->setLeaveById($leaveId);
                            $entitlementAssignment->getDecorator()->setLeaveEntitlementById($entitlementId);
                            $entitlementAssignment->setLengthDays($length);
                        } else {
                            $entitlementAssignment->setLengthDays($entitlementAssignment->getLengthDays() + $length);
                        }
                        $this->getEntityManager()->persist($entitlementAssignment);
                    }
                    $this->getEntityManager()->flush();
                }
            }

            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }
    }

    /**
     * @param DateTime $leaveStartDate
     * @param DateTime $leaveEndDate
     * @param int $empNumber
     * @param DateTime|null $startDayStartTime
     * @param DateTime|null $startDayEndTime
     * @param bool $allDaysPartial
     * @param DateTime|null $endDayStartTime
     * @param DateTime|null $endDayEndTime
     * @return Leave[]
     */
    public function getOverlappingLeave(
        DateTime $leaveStartDate,
        DateTime $leaveEndDate,
        int $empNumber,
        DateTime $startDayStartTime = null,
        DateTime $startDayEndTime = null,
        bool $allDaysPartial = false,
        DateTime $endDayStartTime = null,
        DateTime $endDayEndTime = null
    ): array {
        $q = $this->createQueryBuilder(Leave::class, 'l')
            ->andWhere('l.employee = :empNumber')
            ->setParameter('empNumber', $empNumber);
        $q->andWhere($q->expr()->notIn('l.status', ':notInStatuses'))
            ->setParameter('notInStatuses', [
                Leave::LEAVE_STATUS_LEAVE_CANCELLED,
                Leave::LEAVE_STATUS_LEAVE_REJECTED,
                Leave::LEAVE_STATUS_LEAVE_WEEKEND,
                Leave::LEAVE_STATUS_LEAVE_HOLIDAY
            ]);

        $fullDayExpr = $q->expr()->orX(
            $q->expr()->andX(
                $q->expr()->eq('l.startTime', ':defaultStartTime'),
                $q->expr()->eq('l.endTime', ':defaultStartTime')
            ),
            $q->expr()->andX(
                $q->expr()->isNull('l.startTime'),
                $q->expr()->isNull('l.endTime')
            ),
        );

        $startDayStartTime = $this->getDateTimeHelper()->formatDateTimeToTimeString($startDayStartTime, true);
        $startDayEndTime = $this->getDateTimeHelper()->formatDateTimeToTimeString($startDayEndTime, true);
        $endDayStartTime = $this->getDateTimeHelper()->formatDateTimeToTimeString($endDayStartTime, true);
        $endDayEndTime = $this->getDateTimeHelper()->formatDateTimeToTimeString($endDayEndTime, true);

        if ($leaveStartDate == $leaveEndDate) {
            if (is_null($startDayStartTime)) {
                $startDayStartTime = '00:00:00';
            }

            if (is_null($startDayEndTime)) {
                $startDayEndTime = '23:59:00';
            }

            $startDateAndTime = $this->getDateTimeHelper()->formatDateTimeToYmd($leaveStartDate) . ' '
                . $startDayStartTime;
            $endDateAndTime = $this->getDateTimeHelper()->formatDateTimeToYmd($leaveEndDate) . ' '
                . $startDayEndTime;

            $leaveDateAndStartTime = $q->expr()->concat('l.date', $q->expr()->literal(' '), 'l.startTime');
            $leaveDateAndEndTime = $q->expr()->concat('l.date', $q->expr()->literal(' '), 'l.endTime');
            $orClauses = $q->expr()->orX();
            $orClauses->add(
                $q->expr()->andX(
                    $q->expr()->lte(':startDateAndTime', $leaveDateAndStartTime),
                    $q->expr()->lte($leaveDateAndEndTime, ':endDateAndTime')
                )
            );
            $orClauses->add(
                $q->expr()->andX(
                    $q->expr()->lte($leaveDateAndStartTime, ':startDateAndTime'),
                    $q->expr()->lte(':endDateAndTime', $leaveDateAndEndTime)
                )
            );
            $orClauses->add(
                $q->expr()->andX(
                    $q->expr()->lt(':startDateAndTime', $leaveDateAndStartTime),
                    $q->expr()->lt($leaveDateAndStartTime, ':endDateAndTime')
                )
            );
            $orClauses->add(
                $q->expr()->andX(
                    $q->expr()->lt(':startDateAndTime', $leaveDateAndEndTime),
                    $q->expr()->lt($leaveDateAndEndTime, ':endDateAndTime')
                )
            );
            $orClauses->add(
                $q->expr()->andX(
                    $q->expr()->eq(':startDateAndTime', $leaveDateAndEndTime),
                    $q->expr()->eq($leaveDateAndEndTime, ':endDateAndTime')
                )
            );
            $orClauses->add($q->expr()->andX($q->expr()->eq('l.date', ':leaveEndDate'), $fullDayExpr));
            $q->setParameter('startDateAndTime', $startDateAndTime)
                ->setParameter('endDateAndTime', $endDateAndTime)
                ->setParameter('leaveEndDate', $leaveEndDate)
                ->setParameter('defaultStartTime', '00:00:00');
            $q->andWhere($orClauses);
        } else {
            // first get all overlapping leave, disregarding time periods
            $q->andWhere(
                $q->expr()->andX(
                    $q->expr()->lte('l.date', ':leaveEndDate'),
                    $q->expr()->gte('l.date', ':leaveStartDate')
                )
            );
            $q->setParameter('leaveEndDate', $leaveEndDate)
                ->setParameter('leaveStartDate', $leaveStartDate);

            if ($allDaysPartial) {
                // will overlap with full days or if time period overlaps
                $fullDayExpr->add(
                    $q->expr()->andX(
                        $q->expr()->lt(':startDayStartTime', 'l.endTime'),
                        $q->expr()->gt(':startDayEndTime', 'l.startTime')
                    )
                );
                $q->andWhere($fullDayExpr);
                $q->setParameter('startDayStartTime', $startDayStartTime)
                    ->setParameter('startDayEndTime', $startDayEndTime)
                    ->setParameter('defaultStartTime', '00:00:00');
            } else {
                // Start Day condition
                if (!is_null($startDayStartTime) && !is_null($startDayEndTime)) {
                    $orClauses = $q->expr()->orX();
                    $orClauses->add($q->expr()->neq('l.date', ':leaveStartDate'));
                    $orClauses->add(
                        $q->expr()->andX(
                            $q->expr()->lt(':startDayStartTime', 'l.endTime'),
                            $q->expr()->gt(':startDayEndTime', 'l.startTime')
                        )
                    );
                    $orClauses->addMultiple($fullDayExpr->getParts());
                    $q->andWhere($orClauses);

                    $q->setParameter('leaveStartDate', $leaveStartDate)
                        ->setParameter('startDayStartTime', $startDayStartTime)
                        ->setParameter('startDayEndTime', $startDayEndTime)
                        ->setParameter('defaultStartTime', '00:00:00');
                }

                // End Day condition
                if (!is_null($endDayStartTime) && !is_null($endDayEndTime)) {
                    $orClauses = $q->expr()->orX();
                    $orClauses->add($q->expr()->neq('l.date', ':leaveEndDate'));
                    $orClauses->add(
                        $q->expr()->andX(
                            $q->expr()->lt(':endDayStartTime', 'l.endTime'),
                            $q->expr()->gt(':endDayEndTime', 'l.startTime')
                        )
                    );
                    $orClauses->addMultiple($fullDayExpr->getParts());
                    $q->andWhere($orClauses);

                    $q->setParameter('leaveEndDate', $leaveEndDate)
                        ->setParameter('endDayStartTime', $endDayStartTime)
                        ->setParameter('endDayEndTime', $endDayEndTime)
                        ->setParameter('defaultStartTime', '00:00:00');
                }
            }
        }

        return $q->getQuery()->execute();
    }

    /**
     * @param int $empNumber
     * @param DateTime $date
     * @return float|null
     */
    public function getTotalLeaveDuration(int $empNumber, DateTime $date): ?float
    {
        $this->_markApprovedLeaveAsTaken();

        $leaveStatusNotConsider = [
            Leave::LEAVE_STATUS_LEAVE_CANCELLED,
            Leave::LEAVE_STATUS_LEAVE_REJECTED,
            Leave::LEAVE_STATUS_LEAVE_WEEKEND,
            Leave::LEAVE_STATUS_LEAVE_HOLIDAY
        ];

        $q = $this->createQueryBuilder(Leave::class, 'l')
            ->select('SUM(l.lengthHours)')
            ->andWhere('l.employee = :empNumber')
            ->setParameter('empNumber', $empNumber)
            ->andWhere('l.date = :date')
            ->setParameter('date', $date);
        $q->andWhere($q->expr()->notIn('l.status', ':statusNotConsider'))
            ->setParameter('statusNotConsider', $leaveStatusNotConsider);
        return $q->getQuery()->getSingleScalarResult();
    }

    /**
     * @param int $leaveId
     * @return null|Leave
     */
    public function getLeaveById(int $leaveId): ?Leave
    {
        return $this->getRepository(Leave::class)->find($leaveId);
    }

    public function markApprovedLeaveAsTaken(): void
    {
        $this->_markApprovedLeaveAsTaken();
    }

    private function _markApprovedLeaveAsTaken(): void
    {
        if ($this->doneMarkingApprovedLeaveAsTaken) {
            return;
        }
        $now = $this->getDateTimeHelper()->getNow();
        $q = $this->createQueryBuilder(Leave::class, 'l')
            ->update()
            ->set('l.status', ':takenStatus')
            ->setParameter('takenStatus', Leave::LEAVE_STATUS_LEAVE_TAKEN)
            ->andWhere('l.status = :approvedStatus')
            ->setParameter('approvedStatus', Leave::LEAVE_STATUS_LEAVE_APPROVED);
        $q->andWhere($q->expr()->lt('l.date', ':date'))
            ->setParameter('date', $now);

        $affectedRows = $q->getQuery()->execute();
        // TODO
        if ($affectedRows > 1) {
            $this->doneMarkingApprovedLeaveAsTaken = true;
        }
    }

    /**
     * @param int $empNumber
     * @param DateTime|null $fromDate
     * @param DateTime|null $toDate
     * @return QueryBuilderWrapper
     */
    private function getLeaveRequestsByEmpNumberAndDateRangeQueryBuilderWrapper(
        int $empNumber,
        ?DateTime $fromDate = null,
        ?DateTime $toDate = null
    ): QueryBuilderWrapper {
        $q = $this->createQueryBuilder(LeaveRequest::class, 'lr')
            ->andWhere('lr.employee = :empNumber')
            ->setParameter('empNumber', $empNumber);
        $q->leftJoin('lr.leaves', 'l');

        if ($fromDate) {
            $q->andWhere($q->expr()->gte('l.date', ':fromDate'))
                ->setParameter('fromDate', $fromDate);
        }

        if ($toDate) {
            $q->andWhere($q->expr()->lte('l.date', ':toDate'))
                ->setParameter('toDate', $toDate);
        }
        return $this->getQueryBuilderWrapper($q);
    }

    /**
     * @param int $empNumber
     * @param DateTime|null $fromDate
     * @param DateTime|null $toDate
     * @return LeaveRequest[]
     */
    public function getLeaveRequestsByEmpNumberAndDateRange(
        int $empNumber,
        ?DateTime $fromDate = null,
        ?DateTime $toDate = null
    ): array {
        $q = $this->getLeaveRequestsByEmpNumberAndDateRangeQueryBuilderWrapper(
            $empNumber,
            $fromDate,
            $toDate
        )->getQueryBuilder();
        return $q->getQuery()->execute();
    }

    /**
     * @param int $empNumber
     * @param DateTime[] $dates
     * @return Leave[]
     */
    public function getLeavesByEmpNumberAndDates(int $empNumber, array $dates): array
    {
        $dates = array_map(fn (DateTime $date) => $this->getDateTimeHelper()->formatDateTimeToYmd($date), $dates);
        $q = $this->createQueryBuilder(Leave::class, 'l')
            ->andWhere('l.employee = :empNumber')
            ->setParameter('empNumber', $empNumber);
        $q->andWhere($q->expr()->in('l.date', ':dates'))
            ->setParameter('dates', $dates);
        return $q->getQuery()->execute();
    }

    /**
     * @param LeaveRequestSearchFilterParams $leaveRequestSearchFilterParams
     * @return LeaveRequest[]
     */
    public function getLeaveRequests(LeaveRequestSearchFilterParams $leaveRequestSearchFilterParams): array
    {
        $this->_markApprovedLeaveAsTaken();
        return $this->getLeaveRequestsPaginator($leaveRequestSearchFilterParams)->getQuery()->execute();
    }

    /**
     * @param LeaveRequestSearchFilterParams $leaveRequestSearchFilterParams
     * @return int
     */
    public function getLeaveRequestsCount(LeaveRequestSearchFilterParams $leaveRequestSearchFilterParams): int
    {
        $this->_markApprovedLeaveAsTaken();
        return $this->getLeaveRequestsPaginator($leaveRequestSearchFilterParams)->count();
    }

    /**
     * @param LeaveRequestSearchFilterParams $leaveRequestSearchFilterParams
     * @return Paginator
     */
    private function getLeaveRequestsPaginator(
        LeaveRequestSearchFilterParams $leaveRequestSearchFilterParams
    ): Paginator {
        $q = $this->createQueryBuilder(LeaveRequest::class, 'leaveRequest')
            ->leftJoin('leaveRequest.leaves', 'leave')
            ->leftJoin('leaveRequest.employee', 'employee');
        $this->setSortingAndPaginationParams($q, $leaveRequestSearchFilterParams);
        $q->addOrderBy('employee.lastName', ListSorter::ASCENDING)
            ->addOrderBy('employee.firstName', ListSorter::ASCENDING);

        if (!is_null($leaveRequestSearchFilterParams->getEmpNumber())) {
            $q->andWhere('leaveRequest.employee = :empNumber')
                ->setParameter('empNumber', $leaveRequestSearchFilterParams->getEmpNumber());
        } elseif (!is_null($leaveRequestSearchFilterParams->getEmpNumbers())) {
            $q->andWhere($q->expr()->in('leaveRequest.employee', ':empNumbers'))
                ->setParameter('empNumbers', $leaveRequestSearchFilterParams->getEmpNumbers());
        }

        if (!is_null($leaveRequestSearchFilterParams->getFromDate())) {
            $q->andWhere($q->expr()->gte('leave.date', ':fromDate'))
                ->setParameter('fromDate', $leaveRequestSearchFilterParams->getFromDate());
        }

        if (!is_null($leaveRequestSearchFilterParams->getToDate())) {
            $q->andWhere($q->expr()->lte('leave.date', ':toDate'))
                ->setParameter('toDate', $leaveRequestSearchFilterParams->getToDate());
        }

        if (!is_null($leaveRequestSearchFilterParams->getSubunitId())) {
            $q->leftJoin('employee.subDivision', 'subunit');
            $q->andWhere($q->expr()->in('subunit.id', ':subunitIds'))
                ->setParameter('subunitIds', $leaveRequestSearchFilterParams->getSubunitIdChain());
        }

        if (!is_null($leaveRequestSearchFilterParams->getLeaveTypeId())) {
            $q->andWhere('leaveRequest.leaveType = :leaveTypeId')
                ->setParameter('leaveTypeId', $leaveRequestSearchFilterParams->getLeaveTypeId());
        }

        if (is_null($leaveRequestSearchFilterParams->getIncludeEmployees()) ||
            $leaveRequestSearchFilterParams->getIncludeEmployees() ===
            LeaveRequestSearchFilterParams::INCLUDE_EMPLOYEES_ONLY_CURRENT
        ) {
            $q->andWhere($q->expr()->isNull('employee.employeeTerminationRecord'));
        } elseif (
            $leaveRequestSearchFilterParams->getIncludeEmployees() ===
            LeaveRequestSearchFilterParams::INCLUDE_EMPLOYEES_ONLY_PAST
        ) {
            $q->andWhere($q->expr()->isNotNull('employee.employeeTerminationRecord'));
        }

        if (!is_null($leaveRequestSearchFilterParams->getStatuses())) {
            $statuses = $this->getLeaveRequestService()
                ->getLeaveStatusesByNames($leaveRequestSearchFilterParams->getStatuses());
            $q->andWhere($q->expr()->in('leave.status', ':statuses'))
                ->setParameter('statuses', $statuses);
        }
        $q->addGroupBy('leaveRequest.id');

        $q->andWhere($q->expr()->isNull('employee.purgedAt'));

        return $this->getPaginator($q);
    }

    /**
     * @param int[] $leaveRequestIds
     * @return Leave[]
     */
    public function getLeavesByLeaveRequestIds(array $leaveRequestIds): array
    {
        $q = $this->createQueryBuilder(Leave::class, 'l')
            ->addOrderBy('l.leaveRequest')
            ->addOrderBy('l.date');
        $q->andWhere($q->expr()->in('l.leaveRequest', ':leaveRequestIds'))
            ->setParameter('leaveRequestIds', $leaveRequestIds);

        return $q->getQuery()->execute();
    }

    /**
     * @return LeaveStatus[]
     */
    public function getAllLeaveStatuses(): array
    {
        return $this->getRepository(LeaveStatus::class)->findAll();
    }

    /**
     * @param int $leaveRequestId
     * @return LeaveRequest|null
     */
    public function getLeaveRequestById(int $leaveRequestId): ?LeaveRequest
    {
        return $this->getRepository(LeaveRequest::class)->find($leaveRequestId);
    }

    /**
     * @param int $leaveRequestId
     * @return Leave[]
     */
    public function getLeavesByLeaveRequestId(int $leaveRequestId): array
    {
        $q = $this->createQueryBuilder(Leave::class, 'l')
            ->addOrderBy('l.leaveRequest')
            ->addOrderBy('l.date');
        $q->andWhere('l.leaveRequest = :leaveRequestId')
            ->setParameter('leaveRequestId', $leaveRequestId);

        return $q->getQuery()->execute();
    }

    /**
     * @param LeaveSearchFilterParams $leaveSearchFilterParams
     * @return Paginator
     */
    private function getLeavesPaginator(
        LeaveSearchFilterParams $leaveSearchFilterParams
    ): Paginator {
        $q = $this->createQueryBuilder(Leave::class, 'leave')
            ->leftJoin('leave.employee', 'employee');
        $this->setSortingAndPaginationParams($q, $leaveSearchFilterParams);

        if (!is_null($leaveSearchFilterParams->getLeaveRequestId())) {
            $q->andWhere('leave.leaveRequest = :leaveRequestId')
                ->setParameter('leaveRequestId', $leaveSearchFilterParams->getLeaveRequestId());
        }

        return $this->getPaginator($q);
    }

    /**
     * @param LeaveSearchFilterParams $leaveSearchFilterParams
     * @return Leave[]
     */
    public function getLeaves(LeaveSearchFilterParams $leaveSearchFilterParams): array
    {
        $this->_markApprovedLeaveAsTaken();
        return $this->getLeavesPaginator($leaveSearchFilterParams)->getQuery()->execute();
    }

    /**
     * @param LeaveSearchFilterParams $leaveSearchFilterParams
     * @return int
     */
    public function getLeavesCount(LeaveSearchFilterParams $leaveSearchFilterParams): int
    {
        $this->_markApprovedLeaveAsTaken();
        return $this->getLeavesPaginator($leaveSearchFilterParams)->count();
    }

    /**
     * @param int[] $leaveRequestIds
     * @return LeaveRequest[]
     */
    public function getLeaveRequestsByLeaveRequestIds(array $leaveRequestIds): array
    {
        $q = $this->createQueryBuilder(LeaveRequest::class, 'lr');
        $q->andWhere($q->expr()->in('lr.id', ':leaveRequestIds'))
            ->setParameter('leaveRequestIds', $leaveRequestIds);

        return $q->getQuery()->execute();
    }

    /**
     * @param int[] $leaveIds
     * @return Leave[]
     */
    public function getLeavesByLeaveIds(array $leaveIds): array
    {
        $q = $this->createQueryBuilder(Leave::class, 'l');
        $q->andWhere($q->expr()->in('l.id', ':leaveIds'))
            ->setParameter('leaveIds', $leaveIds);

        return $q->getQuery()->execute();
    }
}
