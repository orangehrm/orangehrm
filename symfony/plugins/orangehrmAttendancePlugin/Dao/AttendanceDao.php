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

namespace OrangeHRM\Attendance\Dao;

use DateTime;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use OrangeHRM\Attendance\Dto\AttendanceRecordSearchFilterParams;
use OrangeHRM\Attendance\Dto\EmployeeAttendanceSummarySearchFilterParams;
use OrangeHRM\Attendance\Exception\AttendanceServiceException;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\AttendanceRecord;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\ORM\QueryBuilderWrapper;
use OrangeHRM\Time\Dto\AttendanceReportSearchFilterParams;

class AttendanceDao extends BaseDao
{
    /**
     * @param  AttendanceRecord  $attendanceRecord
     * @return AttendanceRecord
     */
    public function savePunchRecord(AttendanceRecord $attendanceRecord): AttendanceRecord
    {
        $this->persist($attendanceRecord);
        return $attendanceRecord;
    }

    /**
     * @param int $employeeNumber
     * @param string[] $actionableStatesList
     * @return AttendanceRecord|null
     */
    public function getLastPunchRecordByEmployeeNumberAndActionableList(int $employeeNumber, array $actionableStatesList): ?AttendanceRecord
    {
        $q = $this->createQueryBuilder(AttendanceRecord::class, 'attendanceRecord');
        $q->andWhere('attendanceRecord.employee = :empNumber');
        $q->andWhere($q->expr()->in('attendanceRecord.state', ':state'))
            ->setParameter('state', $actionableStatesList);
        $q->setParameter('empNumber', $employeeNumber);
        $q->orderBy('attendanceRecord.id', ListSorter::DESCENDING);
        $q->setMaxResults(1);
        return $q->getQuery()->getOneOrNullResult();
    }

    /**
     * returns false if overlap found
     * @param DateTime $punchOutTime
     * @param int $employeeNumber
     * @return bool
     */
    public function checkForPunchOutOverLappingRecords(DateTime $punchOutTime, int $employeeNumber): bool
    {
        $actionableStatesList = [AttendanceRecord::STATE_PUNCHED_IN];
        $attendanceRecord = $this->getLastPunchRecordByEmployeeNumberAndActionableList($employeeNumber, $actionableStatesList);

        if (is_null($attendanceRecord)) {
            throw AttendanceServiceException::punchOutAlreadyExist();
        }
        $punchInUtcTime = $attendanceRecord->getDecorator()->getPunchInUTCDateTime();
        if ($punchInUtcTime > $punchOutTime) {
            throw AttendanceServiceException::punchOutTimeBehindThanPunchInTime();
        }

        return $this->getCommonQueryForPunchOutOverlap($punchInUtcTime, $punchOutTime, $employeeNumber);
    }

    /**
     * @param DateTime $punchInTime
     * @param int $employeeNumber
     * @return bool
     */
    public function checkForPunchInOverLappingRecords(DateTime $punchInTime, int $employeeNumber): bool
    {
        $attendanceRecord = $this->getLatestAttendanceRecordByEmployeeNumber($employeeNumber);
        if (is_null($attendanceRecord)) {
            return false;
        }
        if ($attendanceRecord->getState() === AttendanceRecord::STATE_PUNCHED_IN) {
            throw AttendanceServiceException::punchInAlreadyExist();
        }

        return $this->getCommonQueryForPunchInOverlap($punchInTime, $employeeNumber);
    }

    /**
     * @param DateTime $punchInTime
     * @param int $employeeNumber
     * @param int $recordId
     * @param DateTime|null $punchOutTime
     * @return bool
     * @throws AttendanceServiceException
     */
    public function checkForPunchInOverLappingRecordsWhenEditing(DateTime $punchInTime, int $employeeNumber, int $recordId, ?DateTime $punchOutTime = null): bool
    {
        if (!is_null($punchOutTime) && $punchInTime > $punchOutTime) {
            throw AttendanceServiceException::punchOutTimeBehindThanPunchInTime();
        }

        return $this->getCommonQueryForPunchInOverlap($punchInTime, $employeeNumber, $recordId);
    }

    /**
     * @param DateTime $punchInTime
     * @param DateTime $punchOutTime
     * @param int $employeeNumber
     * @param int $recordId
     * @return bool
     */
    public function checkForPunchInOutOverLappingRecordsWhenEditing(DateTime $punchInTime, DateTime $punchOutTime, int $employeeNumber, int $recordId): bool
    {
        if ($punchInTime > $punchOutTime) {
            throw AttendanceServiceException::punchOutTimeBehindThanPunchInTime();
        }

        return $this->getCommonQueryForPunchOutOverlap($punchInTime, $punchOutTime, $employeeNumber, $recordId);
    }

    /**
     * @param DateTime $punchInUtcTime
     * @param DateTime $punchOutTime
     * @param int $employeeNumber
     * @param int|null $recordId
     * @return bool
     */
    private function getCommonQueryForPunchOutOverlap(DateTime $punchInUtcTime, DateTime $punchOutTime, int $employeeNumber, ?int $recordId = null): bool
    {
        $q1 = $this->createQueryBuilder(AttendanceRecord::class, 'attendanceRecord');
        $q1->andWhere('attendanceRecord.employee = :empNumber');
        $q1->andWhere($q1->expr()->gt('attendanceRecord.punchInUtcTime', ':punchInUtcTime'))
            ->setParameter('punchInUtcTime', $punchInUtcTime);
        $q1->andWhere($q1->expr()->lt('attendanceRecord.punchInUtcTime', ':punchOutUtcTime'))
            ->setParameter('punchOutUtcTime', $punchOutTime);
        $q1->setParameter('empNumber', $employeeNumber);
        if (!is_null($recordId)) {
            $q1->andWhere('attendanceRecord.id != :recordId');
            $q1->setParameter('recordId', $recordId);
        }

        /* @var AttendanceRecord[] $attendance */
        $attendance =  $q1->getQuery()->execute();
        if ((count($attendance) > 0)) {
            return false;
        }

        $q2 = $this->createQueryBuilder(AttendanceRecord::class, 'attendanceRecord');
        $q2->andWhere('attendanceRecord.employee = :empNumber');
        $q2->andWhere($q2->expr()->lt('attendanceRecord.punchInUtcTime', ':punchInUtcTime'))
            ->setParameter('punchInUtcTime', $punchInUtcTime);
        $q2->andWhere($q2->expr()->gt('attendanceRecord.punchOutUtcTime', ':punchOutUtcTime'))
            ->setParameter('punchOutUtcTime', $punchOutTime);
        $q2->setParameter('empNumber', $employeeNumber);
        if (!is_null($recordId)) {
            $q2->andWhere('attendanceRecord.id != :recordId');
            $q2->setParameter('recordId', $recordId);
        }

        if (($this->getPaginator($q2)->count() > 0)) {
            return false;
        }

        $q3 = $this->createQueryBuilder(AttendanceRecord::class, 'attendanceRecord');
        $q3->andWhere('attendanceRecord.employee = :empNumber');
        $q3->andWhere($q3->expr()->gt('attendanceRecord.punchInUtcTime', ':punchInUtcTime'))
            ->setParameter('punchInUtcTime', $punchInUtcTime);
        $q3->andWhere($q3->expr()->lt('attendanceRecord.punchOutUtcTime', ':punchOutUtcTime'))
            ->setParameter('punchOutUtcTime', $punchOutTime);
        $q3->setParameter('empNumber', $employeeNumber);
        if (!is_null($recordId)) {
            $q3->andWhere('attendanceRecord.id != :recordId');
            $q3->setParameter('recordId', $recordId);
        }

        if (($this->getPaginator($q3)->count() > 0)) {
            return false;
        }
        return true;
    }

    /**
     * @param DateTime $punchInTime
     * @param int $employeeNumber
     * @param int|null $recordId
     * @return bool
     */
    public function getCommonQueryForPunchInOverlap(DateTime $punchInTime, int $employeeNumber, ?int $recordId = null): bool
    {
        $q = $this->createQueryBuilder(AttendanceRecord::class, 'attendanceRecord');
        $q->andWhere('attendanceRecord.employee = :empNumber');

        $q->andWhere($q->expr()->lte('attendanceRecord.punchInUtcTime', ':punchInUtcTime'))
            ->setParameter('punchInUtcTime', $punchInTime);
        $q->andWhere($q->expr()->gt('attendanceRecord.punchOutUtcTime', ':punchOutUtcTime'))
            ->setParameter('punchOutUtcTime', $punchInTime);
        $q->setParameter('empNumber', $employeeNumber);
        if (!is_null($recordId)) {
            $q->andWhere('attendanceRecord.id != :recordId');
            $q->setParameter('recordId', $recordId);
        }
        // if any records found in the data source (count greater than 0) -> overlap found
        return $this->getPaginator($q)->count() > 0;
    }

    /**
     * @param  string  $workflow
     * @param  string  $state
     * @param  string  $role
     * @param  string  $action
     * @param  string  $resultingState
     * @return bool
     */
    public function hasSavedConfiguration(
        string $workflow,
        string $state,
        string $role,
        string $action,
        string $resultingState
    ): bool {
        $qb = $this->createQueryBuilder(WorkflowStateMachine::class, 'workflowStateMachine');
        $qb->where('workflowStateMachine.workflow = :workflow');
        $qb->setParameter('workflow', $workflow);
        $qb->andWhere('workflowStateMachine.state = :state');
        $qb->setParameter('state', $state);
        $qb->andWhere('workflowStateMachine.role = :role');
        $qb->setParameter('role', $role);
        $qb->andWhere('workflowStateMachine.action = :action');
        $qb->setParameter('action', $action);
        $qb->andWhere('workflowStateMachine.resultingState = :resultingState');
        $qb->setParameter('resultingState', $resultingState);

        $result = $qb->getQuery()->execute();
        if ($result) {
            return true;
        }
        return false;
    }

    /**
     * @param  int[]  $attendanceRecordIds
     * @return int
     */
    public function deleteAttendanceRecords(array $attendanceRecordIds): int
    {
        $qb = $this->createQueryBuilder(AttendanceRecord::class, 'attendanceRecord');
        $qb->delete()
            ->where($qb->expr()->in('attendanceRecord.id', ':ids'))
            ->setParameter('ids', $attendanceRecordIds);
        return $qb->getQuery()->execute();
    }

    /**
     * @param int $attendanceRecordId
     * @return AttendanceRecord|null
     */
    public function getAttendanceRecordById(int $attendanceRecordId): ?AttendanceRecord
    {
        $attendanceRecord = $this->getRepository(AttendanceRecord::class)->find($attendanceRecordId);
        if ($attendanceRecord instanceof AttendanceRecord) {
            if (is_null($attendanceRecord->getEmployee()->getPurgedAt())) {
                return $attendanceRecord;
            }
        }
        return null;
    }

    /**
     * @param  int  $empNumber
     * @param  int[]  $attendanceRecordIds
     * @return AttendanceRecord[]
     */
    public function getAttendanceRecordsByEmpNumberAndIds(int $empNumber, array $attendanceRecordIds): array
    {
        $qb = $this->createQueryBuilder(AttendanceRecord::class, 'attendanceRecord');
        $qb->andWhere('attendanceRecord.employee = :empNumber');
        $qb->setParameter('empNumber', $empNumber);
        $qb->andWhere($qb->expr()->in('attendanceRecord.id', ':ids'));
        $qb->setParameter('ids', $attendanceRecordIds);
        return $qb->getQuery()->execute();
    }

    /**
     * @param int $employeeNumber
     * @return AttendanceRecord|null
     */
    public function getLatestAttendanceRecordByEmployeeNumber(int $employeeNumber): ?AttendanceRecord
    {
        $q = $this->createQueryBuilder(AttendanceRecord::class, 'attendanceRecord');
        $q->andWhere('attendanceRecord.employee = :empNumber');
        $q->setParameter('empNumber', $employeeNumber);
        $q->orderBy('attendanceRecord.id', ListSorter::DESCENDING);
        $q->setMaxResults(1);

        return $q->getQuery()->getOneOrNullResult();
    }

    /**
     * @param AttendanceReportSearchFilterParams $attendanceReportSearchFilterParams
     * @return array
     * Example [ Employee full name (first name and last name), attendance record id, termination id, employee number, total hours in sec ]
     */
    public function getAttendanceReportCriteriaList(
        AttendanceReportSearchFilterParams $attendanceReportSearchFilterParams
    ): array {
        $paginator = $this->getAttendanceReportPaginator($attendanceReportSearchFilterParams);
        return $paginator->getQuery()->execute();
    }

    /**
     * @param AttendanceReportSearchFilterParams $attendanceReportSearchFilterParams
     * @return Paginator
     */
    public function getAttendanceReportPaginator(AttendanceReportSearchFilterParams $attendanceReportSearchFilterParams): Paginator
    {
        $q = $this->getAttendanceReportQueryBuilderWrapper($attendanceReportSearchFilterParams)->getQueryBuilder();
        $q->select(
            'CONCAT(employee.firstName, \' \', employee.lastName) AS fullName',
            'attendanceRecord.id',
            'IDENTITY(employee.employeeTerminationRecord) AS terminationId',
            'employee.empNumber as empNumber',
            "SUM(TIME_DIFF(COALESCE(attendanceRecord.punchOutUtcTime, 0), COALESCE(attendanceRecord.punchInUtcTime, 0),'second')) AS total"
        );
        $q->groupBy('employee.empNumber');
        return $this->getPaginator($q);
    }
    /**
     * @param AttendanceReportSearchFilterParams $attendanceReportSearchFilterParams
     * @return QueryBuilderWrapper
     */
    private function getAttendanceReportQueryBuilderWrapper(
        AttendanceReportSearchFilterParams $attendanceReportSearchFilterParams
    ): QueryBuilderWrapper {
        $q = $this->createQueryBuilder(Employee::class, 'employee');
        $q->leftJoin('employee.jobTitle', 'jobTitle');
        $q->leftJoin('employee.subDivision', 'subunit');
        $q->leftJoin('employee.empStatus', 'empStatus');

        $this->setSortingAndPaginationParams($q, $attendanceReportSearchFilterParams);

        if (is_null($attendanceReportSearchFilterParams->getFromDate()) && is_null($attendanceReportSearchFilterParams->getToDate())) {
            // both from date and to date is null
            $q->leftJoin('employee.attendanceRecords', 'attendanceRecord');
        } elseif (!is_null($attendanceReportSearchFilterParams->getFromDate()) && is_null($attendanceReportSearchFilterParams->getToDate())) {
            // from date is not null and to date is null
            $q->leftJoin('employee.attendanceRecords', 'attendanceRecord', Expr\Join::WITH, $q->expr()->andX(
                $q->expr()->gte('attendanceRecord.punchInUserTime', ':fromDate')
            ));
            $q->setParameter('fromDate', $attendanceReportSearchFilterParams->getFromDate());
        } elseif (is_null($attendanceReportSearchFilterParams->getFromDate()) && !is_null($attendanceReportSearchFilterParams->getToDate())) {
            // from date is null and to date is not null
            $q->leftJoin('employee.attendanceRecords', 'attendanceRecord', Expr\Join::WITH, $q->expr()->andX(
                $q->expr()->lte('attendanceRecord.punchOutUserTime', ':toDate')
            ));
            $q->setParameter('toDate', $attendanceReportSearchFilterParams->getToDate());
        } elseif (!is_null($attendanceReportSearchFilterParams->getFromDate()) && !is_null($attendanceReportSearchFilterParams->getToDate())) {
            // both from date and to date is not null
            $q->leftJoin('employee.attendanceRecords', 'attendanceRecord', Expr\Join::WITH, $q->expr()->andX(
                $q->expr()->gte('attendanceRecord.punchInUserTime', ':fromDate'),
                $q->expr()->lte('attendanceRecord.punchOutUserTime', ':toDate')
            ));
            $q->setParameter('fromDate', $attendanceReportSearchFilterParams->getFromDate());
            $q->setParameter('toDate', $attendanceReportSearchFilterParams->getToDate());
        }

        if (!is_null($attendanceReportSearchFilterParams->getEmployeeNumbers())) {
            $q->andWhere($q->expr()->in('employee.empNumber', ':empNumbers'))
                ->setParameter('empNumbers', $attendanceReportSearchFilterParams->getEmployeeNumbers());
        }

        if (!is_null($attendanceReportSearchFilterParams->getJobTitleId())) {
            $q->andWhere('jobTitle.id = :jobTitleId')
                ->setParameter('jobTitleId', $attendanceReportSearchFilterParams->getJobTitleId());
        }

        if (!is_null($attendanceReportSearchFilterParams->getSubunitId())) {
            $q->andWhere($q->expr()->in('subunit.id', ':subunitIds'))
                ->setParameter('subunitIds', $attendanceReportSearchFilterParams->getSubunitIdChain());
        }

        if (!is_null($attendanceReportSearchFilterParams->getEmploymentStatusId())) {
            $q->andWhere('empStatus.id = :empStatusId')
                ->setParameter('empStatusId', $attendanceReportSearchFilterParams->getEmploymentStatusId());
        }

        return $this->getQueryBuilderWrapper($q);
    }

    /**
     * @param AttendanceReportSearchFilterParams $attendanceReportSearchFilterParams
     * @return int
     */
    public function getAttendanceReportCriteriaListCount(
        AttendanceReportSearchFilterParams $attendanceReportSearchFilterParams
    ): int {
        $paginator = $this->getAttendanceReportPaginator($attendanceReportSearchFilterParams);
        return $paginator->count();
    }

    /**
     * @param AttendanceReportSearchFilterParams $attendanceReportSearchFilterParams
     * @return int
     */
    public function getTotalAttendanceDuration(
        AttendanceReportSearchFilterParams $attendanceReportSearchFilterParams
    ): int {
        $q = $this->getAttendanceReportQueryBuilderWrapper($attendanceReportSearchFilterParams)->getQueryBuilder();
        $q->select(
            "SUM(TIME_DIFF(COALESCE(attendanceRecord.punchOutUtcTime, 0), COALESCE(attendanceRecord.punchInUtcTime, 0),'second')) AS total"
        );
        return $q->getQuery()->getSingleScalarResult() === null ? 0 : $q->getQuery()->getSingleScalarResult();
    }

    /**
     * @param AttendanceRecordSearchFilterParams $attendanceRecordSearchFilterParams
     * @return array
     */
    public function getAttendanceRecordList(
        AttendanceRecordSearchFilterParams $attendanceRecordSearchFilterParams
    ): array {
        return $this->getAttendanceRecordListPaginator($attendanceRecordSearchFilterParams)->getQuery()->execute();
    }

    /**
     * @param AttendanceRecordSearchFilterParams $attendanceRecordSearchFilterParams
     * @return Paginator
     */
    private function getAttendanceRecordListPaginator(
        AttendanceRecordSearchFilterParams $attendanceRecordSearchFilterParams
    ): Paginator {
        $q = $this->getAttendanceRecordListQueryBuilderWrapper($attendanceRecordSearchFilterParams)->getQueryBuilder();
        $q->select(
            'CONCAT(employee.firstName, \' \', employee.lastName) AS fullName',
            'attendanceRecord.id',
            'attendanceRecord.punchInUserTime AS punchInTime',
            'attendanceRecord.punchInNote AS punchInNote',
            'attendanceRecord.punchInTimeOffset AS punchInTimeOffset',
            'attendanceRecord.punchOutUserTime AS punchOutTime',
            'attendanceRecord.punchOutNote AS punchOutNote',
            'attendanceRecord.punchOutTimeOffset AS punchOutTimeOffset',
            'IDENTITY(employee.employeeTerminationRecord) AS terminationId',
            'employee.empNumber as empNumber',
            "SUM(TIME_DIFF(COALESCE(attendanceRecord.punchOutUtcTime, 0), COALESCE(attendanceRecord.punchInUtcTime, 0),'second')) AS total"
        );
        $q->groupBy('attendanceRecord.id');
        return $this->getPaginator($q);
    }

    /**
     * @param AttendanceRecordSearchFilterParams $attendanceRecordSearchFilterParams
     * @return QueryBuilderWrapper
     */
    private function getAttendanceRecordListQueryBuilderWrapper(
        AttendanceRecordSearchFilterParams $attendanceRecordSearchFilterParams
    ): QueryBuilderWrapper {
        $q = $this->createQueryBuilder(Employee::class, 'employee');
        $q->leftJoin('employee.attendanceRecords', 'attendanceRecord');
        $this->setSortingAndPaginationParams($q, $attendanceRecordSearchFilterParams);

        return $this->getCommonQueryBuilderWrapper($attendanceRecordSearchFilterParams, $q);
    }

    /**
     * @param AttendanceRecordSearchFilterParams $attendanceRecordSearchFilterParams
     * @param QueryBuilder $q
     * @return QueryBuilderWrapper
     */
    public function getCommonQueryBuilderWrapper(
        AttendanceRecordSearchFilterParams $attendanceRecordSearchFilterParams,
        QueryBuilder $q
    ): QueryBuilderWrapper {
        if (!is_null($attendanceRecordSearchFilterParams->getEmployeeNumbers())) {
            $q->andWhere($q->expr()->in('employee.empNumber', ':empNumbers'))
                ->setParameter('empNumbers', $attendanceRecordSearchFilterParams->getEmployeeNumbers());
        }

        if (!is_null($attendanceRecordSearchFilterParams->getFromDate())) {
            $q->andWhere($q->expr()->gte('attendanceRecord.punchInUserTime', ':fromDate'))
                ->setParameter('fromDate', $attendanceRecordSearchFilterParams->getFromDate());
        }

        if (!is_null($attendanceRecordSearchFilterParams->getToDate())) {
            $q->andWhere($q->expr()->lte('attendanceRecord.punchInUserTime', ':toDate'))
                ->setParameter('toDate', $attendanceRecordSearchFilterParams->getToDate());
        }
        return $this->getQueryBuilderWrapper($q);
    }

    /**
     * @param AttendanceRecordSearchFilterParams $attendanceRecordSearchFilterParams
     * @return int
     */
    public function getAttendanceRecordListCount(
        AttendanceRecordSearchFilterParams $attendanceRecordSearchFilterParams
    ): int {
        $paginator = $this->getAttendanceRecordListPaginator($attendanceRecordSearchFilterParams);
        return $paginator->count();
    }

    /**
     * @param AttendanceRecordSearchFilterParams $attendanceRecordSearchFilterParams
     * @return array|null
     */
    public function getTotalWorkingTime(AttendanceRecordSearchFilterParams $attendanceRecordSearchFilterParams): ?array
    {
        $q = $this->getAttendanceRecordListQueryBuilderWrapper($attendanceRecordSearchFilterParams)->getQueryBuilder();
        $q->select(
            "SUM(TIME_DIFF(COALESCE(attendanceRecord.punchOutUtcTime, 0), COALESCE(attendanceRecord.punchInUtcTime, 0),'second')) AS total"
        );
        $q->groupBy('employee.empNumber');

        return $q->getQuery()->getOneOrNullResult();
    }

    /**
     * @param EmployeeAttendanceSummarySearchFilterParams $employeeAttendanceSummarySearchFilterParams
     * @return array
     */
    public function getEmployeeAttendanceSummaryList(EmployeeAttendanceSummarySearchFilterParams $employeeAttendanceSummarySearchFilterParams): array
    {
        return $this->getEmployeeAttendanceSummaryPaginator($employeeAttendanceSummarySearchFilterParams)->getQuery()->execute();
    }

    /**
     * @param EmployeeAttendanceSummarySearchFilterParams $employeeAttendanceSummarySearchFilterParams
     * @return int
     */
    public function getEmployeeAttendanceSummaryListCount(
        EmployeeAttendanceSummarySearchFilterParams $employeeAttendanceSummarySearchFilterParams
    ): int {
        $paginator = $this->getEmployeeAttendanceSummaryPaginator($employeeAttendanceSummarySearchFilterParams);
        return $paginator->count();
    }

    /**
     * @param EmployeeAttendanceSummarySearchFilterParams $employeeAttendanceSummarySearchFilterParams
     * @return Paginator
     */
    private function getEmployeeAttendanceSummaryPaginator(EmployeeAttendanceSummarySearchFilterParams $employeeAttendanceSummarySearchFilterParams): Paginator
    {
        $q = $this->getEmployeeAttendanceSummaryQueryBuilderWrapper($employeeAttendanceSummarySearchFilterParams)->getQueryBuilder();
        $q->select(
            'employee.lastName AS lastName',
            'employee.firstName AS firstName',
            'employee.middleName AS middleName',
            'employee.employeeId AS employeeId',
            'IDENTITY(employee.employeeTerminationRecord) AS terminationId',
            'employee.empNumber',
            "SUM(TIME_DIFF(COALESCE(attendanceRecord.punchOutUtcTime, 0), COALESCE(attendanceRecord.punchInUtcTime, 0),'second')) AS total"
        );
        $q->andWhere($q->expr()->isNull('employee.purgedAt'));
        $q->groupBy('employee.empNumber');
        $q->addOrderBy('total', ListSorter::DESCENDING);
        return $this->getPaginator($q);
    }

    /**
     * @param EmployeeAttendanceSummarySearchFilterParams $employeeAttendanceSummarySearchFilterParams
     * @return QueryBuilderWrapper
     */
    private function getEmployeeAttendanceSummaryQueryBuilderWrapper(EmployeeAttendanceSummarySearchFilterParams $employeeAttendanceSummarySearchFilterParams): QueryBuilderWrapper
    {
        $q = $this->createQueryBuilder(Employee::class, 'employee');
        $q->leftJoin('employee.attendanceRecords', 'attendanceRecord', Expr\Join::WITH, $q->expr()->andX(
            $q->expr()->gte('attendanceRecord.punchInUserTime', ':fromDate'),
            $q->expr()->lte('attendanceRecord.punchInUserTime', ':toDate')
        ));
        $q->setParameter('fromDate', $employeeAttendanceSummarySearchFilterParams->getFromDate());
        $q->setParameter('toDate', $employeeAttendanceSummarySearchFilterParams->getToDate());

        if (!is_null($employeeAttendanceSummarySearchFilterParams->getEmployeeNumbers())) {
            $q->andWhere($q->expr()->in('employee.empNumber', ':empNumbers'))
                ->setParameter('empNumbers', $employeeAttendanceSummarySearchFilterParams->getEmployeeNumbers());
        }

        $this->setSortingAndPaginationParams($q, $employeeAttendanceSummarySearchFilterParams);
        return $this->getQueryBuilderWrapper($q);
    }
}
