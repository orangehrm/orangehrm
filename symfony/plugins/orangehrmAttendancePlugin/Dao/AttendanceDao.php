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
use Doctrine\ORM\QueryBuilder;
use OrangeHRM\Attendance\Dto\AttendanceRecordSearchFilterParams;
use OrangeHRM\Attendance\Exception\AttendanceServiceException;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\AttendanceRecord;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\ORM\QueryBuilderWrapper;
use OrangeHRM\Time\Dto\AttendanceReportSearchFilterParams;
use Respect\Validation\Rules\Date;

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
     * Get Attendance Record
     * @param $$employeeId,$date
     * @return attendance records
     */
    public function getAttendanceRecord($employeeId, $date)
    {
        $from = $date . " " . "00:" . "00:" . "00";
        $end = $date . " " . "23:" . "59:" . "59";

        try {
            $query = Doctrine_Query::create()
                    ->from("attendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere("punchInUserTime >= ?", $from)
                    ->andWhere("punchInUserTime <= ?", $end);
            $records = $query->execute();
            if (is_null($records[0]->getId())) {
                return null;
            } else {
                return $records;
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * get delete attendance records
     * @param $attendanceRecordId
     * @return boolean
     */
    public function deleteAttendanceRecords($attendanceRecordId)
    {
        try {
            $q = Doctrine_Query::create()
                    ->delete('AttendanceRecord')
                    ->where("id = ?", $attendanceRecordId);


            $result = $q->execute();

            if (!empty($result)) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param int $attendanceRecordId
     * @return AttendanceRecord|null
     */
    public function getAttendanceRecordById(int $attendanceRecordId): ?AttendanceRecord
    {
        $attendanceRecord = $this->getRepository(AttendanceRecord::class)->find($attendanceRecordId);
        return ($attendanceRecord instanceof AttendanceRecord) ? $attendanceRecord : null;
    }

    /**
     * checkForPunchOutOverLappingRecordsWhenEditing
     * @param $punchInTime,$punchOutTime,$employeeId
     * @return string 1,0
     */
    public function checkForPunchOutOverLappingRecordsWhenEditing($punchInTime, $punchOutTime, $employeeId, $recordId)
    {
        $isValid = "1";


        try {
            $query1 = Doctrine_Query::create()
                    ->from("AttendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere("punchInUtcTime > ?", $punchInTime)
                    ->andWhere("punchInUtcTime < ?", $punchOutTime);
            $records1 = $query1->execute();

            if ((count($records1) == 1) && ($records1[0]->getId() == $recordId)) {
            } elseif ((count($records1) > 0)) {
                $isValid = "0";
            }



            $query3 = Doctrine_Query::create()
                    ->from("attendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere("punchInUtcTime < ?", $punchInTime)
                    ->andWhere("punchOutUtcTime > ?", $punchOutTime);
            $records3 = $query3->execute();

            if ((count($records3) == 1) && ($records3[0]->getId() == $recordId)) {
            } elseif ((count($records3) > 0)) {
                $isValid = "0";
            }

            $query4 = Doctrine_Query::create()
                    ->from("attendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere('punchInUtcTime > ?', $punchInTime)
                    ->andWhere('punchOutUtcTime < ?', $punchOutTime);
            $records4 = $query4->execute();


            if ((count($records4) == 1) && ($records4[0]->getId() == $recordId)) {
            } elseif ((count($records4) > 0)) {
                $isValid = "0";
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
        return $isValid;
    }

    /**
    *
    * @param int $employeeId
    * @param string $employeementStatus
    * @param int $subDivision
    * @param date $dateFrom
    * @param date $dateTo
    * @return array
    */

    public function searchAttendanceRecords($employeeIds = null, $employeementStatus = null, $subDivision = null, $dateFrom = null, $dateTo = null)
    {
        $q = Doctrine_Query::create()
                 ->select("e.emp_number, e.termination_id, e.emp_firstname, e.emp_middle_name, e.emp_lastname, a.punch_in_user_time as in_date_time, a.punch_out_user_time as out_date_time, punch_in_note, punch_out_note, TIMESTAMPDIFF(MINUTE, a.punch_in_user_time, a.punch_out_user_time) as duration")
                ->from("AttendanceRecord a")
                ->leftJoin("a.Employee e")
                ->orderBy('a.punch_in_user_time DESC');

        if ($employeeIds != null) {
            if (is_array($employeeIds)) {
                $q->andWhereIn("e.emp_number", $employeeIds);
            } else {
                $q->andWhere(" e.emp_number = ?", $employeeIds);
            }
        }

        if ($employeementStatus != null) {
            $q->andWhere("e.emp_status = ?", $employeementStatus);
        } else {
            if ($employeeIds <= 0) {
                $q->andWhere("(e.termination_id IS NULL)");
            }
        }

        if ($subDivision > 0) {
            $companyService = new CompanyStructureService();
            $subDivisions = $companyService->getCompanyStructureDao()->getSubunitById($subDivision);

            $subUnitIds = [$subDivision];
            if (!empty($subDivisions)) {
                $descendents = $subDivisions->getNode()->getDescendants();

                foreach ($descendents as $descendent) {
                    $subUnitIds[] = $descendent->id;
                }
            }

            $q->andWhereIn("e.work_station", $subUnitIds);
        }

        if ($dateFrom != null) {
            $q->andWhere("a.punch_in_user_time >=?", $dateFrom);
        }

        if ($dateTo != null) {
            $q->andWhere("a.punch_out_user_time <=?", $dateTo);
        }

        $result = $q->execute([], Doctrine::HYDRATE_SCALAR);
        return $result;
    }

    /**
     * @param int $employeeId
     * @param string $state  // PUNCHED_IN or PUNCHED_OUT
     * @return array|bool|Doctrine_Record|float|int|mixed|string|null
     * @throws DaoException
     */
    public function getLatestPunchInRecord(int $employeeId, $state)
    {
        if ($state == PluginAttendanceRecord::STATE_PUNCHED_IN) {
            try {
                $query = Doctrine_Query::create()
                    ->from("attendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere("state = ?", $state)
                    ->orderBy('punchInUtcTime DESC');
                return $query->fetchOne();
            } catch (Exception $ex) {
                throw new DaoException($ex->getMessage());
            }
        } elseif ($state == PluginAttendanceRecord::STATE_PUNCHED_OUT) {
            try {
                $query = Doctrine_Query::create()
                    ->from("attendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere("state = ?", $state)
                    ->orderBy('punchInUtcTime DESC');
                return $query->fetchOne();
            } catch (Exception $ex) {
                throw new DaoException($ex->getMessage());
            }
        }
    }

    /**
     * @param string $fromDate
     * @param string $toDate
     * @param int $employeeId
     * @param string $state
     * @return array|Doctrine_Collection|Doctrine_Collection_OnDemand|int
     * @throws DaoException
     */
    public function getAttendanceRecordsBetweenTwoDays(string $fromDate, string $toDate, int $employeeId, string $state)
    {
        try {
            $query = Doctrine_Query::create()
                ->from("attendanceRecord")
                ->where("employeeId = ?", $employeeId)
                ->andWhere('punchInUserTime >= ?', $fromDate)
                ->andWhere('punchInUserTime <= ?', $toDate)
                ->orderBy('punchInUtcTime');
            if ($state!='ALL') {
                $query->andWhere("state = ?", $state);
            }
            return $query->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param $empNumbers
     * @param null $dateFrom
     * @param null $dateTo
     * @return array|Doctrine_Collection|Doctrine_Collection_OnDemand|int
     * @throws DaoException
     */
    public function getAttendanceRecordsByEmpNumbers($empNumbers, $dateFrom = null, $dateTo = null)
    {
        try {
            $q = Doctrine_Query::create()
                ->from("AttendanceRecord a")
                ->leftJoin("a.Employee e")
                ->orderBy('e.firstName ASC');

            if (is_array($empNumbers)) {
                $q->andWhereIn("e.emp_number", $empNumbers);
            } else {
                $q->andWhere(" e.emp_number = ?", $empNumbers);
            }

            if ($dateFrom != null) {
                $q->andWhere("a.punchInUserTime >=?", $dateFrom);
            }

            if ($dateTo != null) {
                $q->andWhere("a.punchInUserTime <=?", $dateTo);
            }

            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
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
        $q->leftJoin('employee.attendanceRecords', 'attendanceRecord');

        $this->setSortingAndPaginationParams($q, $attendanceReportSearchFilterParams);

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

        if (!is_null($attendanceReportSearchFilterParams->getFromDate())) {
            $q->andWhere($q->expr()->orX(
                $q->expr()->isNull('attendanceRecord.id'),
                $q->expr()->gte('attendanceRecord.punchInUserTime', ':fromDate')
            ))
                ->setParameter('fromDate', $attendanceReportSearchFilterParams->getFromDate());
        }

        if (!is_null($attendanceReportSearchFilterParams->getToDate())) {
            $q->andWhere($q->expr()->orX(
                $q->expr()->isNull('attendanceRecord.id'),
                $q->expr()->isNull('attendanceRecord.punchOutUserTime'),
                $q->expr()->lte('attendanceRecord.punchOutUserTime', ':toDate')
            ))
                ->setParameter('toDate', $attendanceReportSearchFilterParams->getToDate());
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
    public function getAttendanceRecordList(AttendanceRecordSearchFilterParams $attendanceRecordSearchFilterParams
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
}
