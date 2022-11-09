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
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Entity\LeaveEntitlement;
use OrangeHRM\Entity\LeaveLeaveEntitlement;
use OrangeHRM\Entity\LeaveType;
use OrangeHRM\Leave\Dto\EmployeeLeaveEntitlementUsageReportSearchFilterParams;
use OrangeHRM\Leave\Dto\LeaveEntitlementSearchFilterParams;
use OrangeHRM\Leave\Dto\LeaveEntitlementUsage;
use OrangeHRM\Leave\Dto\LeaveTypeLeaveEntitlementUsageReportSearchFilterParams;
use OrangeHRM\Leave\Dto\LeaveWithDaysLeft;
use OrangeHRM\Leave\Entitlement\LeaveBalance;
use OrangeHRM\Leave\Service\LeavePeriodService;
use OrangeHRM\Leave\Traits\Service\LeaveConfigServiceTrait;
use OrangeHRM\Leave\Traits\Service\LeaveEntitlementServiceTrait;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\ORM\QueryBuilderWrapper;

class LeaveEntitlementDao extends BaseDao
{
    use LeaveConfigServiceTrait;
    use LeaveEntitlementServiceTrait;
    use DateTimeHelperTrait;

    /**
     * @return int[]
     */
    public function getPendingStatusIds(): array
    {
        return [Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL];
    }

    /**
     * @param LeaveEntitlementSearchFilterParams $entitlementSearchFilterParams
     * @return LeaveEntitlement[]
     */
    public function getLeaveEntitlements(LeaveEntitlementSearchFilterParams $entitlementSearchFilterParams): array
    {
        return $this->getLeaveEntitlementsQueryBuilderWrapper($entitlementSearchFilterParams)
            ->getQueryBuilder()
            ->getQuery()
            ->execute();
    }

    /**
     * @param LeaveEntitlementSearchFilterParams $entitlementSearchFilterParams
     * @return int
     */
    public function getLeaveEntitlementsCount(LeaveEntitlementSearchFilterParams $entitlementSearchFilterParams): int
    {
        return $this->getPaginator(
            $this->getLeaveEntitlementsQueryBuilderWrapper($entitlementSearchFilterParams)->getQueryBuilder()
        )->count();
    }

    /**
     * @param LeaveEntitlementSearchFilterParams $entitlementSearchFilterParams
     * @return float|null
     */
    public function getLeaveEntitlementsSum(LeaveEntitlementSearchFilterParams $entitlementSearchFilterParams): ?float
    {
        $q = $this->getLeaveEntitlementsQueryBuilderWrapper($entitlementSearchFilterParams)->getQueryBuilder();
        $q->select('SUM(entitlement.noOfDays)');
        return $q->getQuery()->getSingleScalarResult();
    }

    /**
     * @param LeaveEntitlementSearchFilterParams $entitlementSearchFilterParams
     * @return QueryBuilderWrapper
     */
    private function getLeaveEntitlementsQueryBuilderWrapper(
        LeaveEntitlementSearchFilterParams $entitlementSearchFilterParams
    ): QueryBuilderWrapper {
        $q = $this->createQueryBuilder(LeaveEntitlement::class, 'entitlement')
            ->andWhere('entitlement.deleted = :deleted')
            ->setParameter('deleted', false);
        $q->leftJoin('entitlement.leaveType', 'leaveType');
        $this->setSortingAndPaginationParams($q, $entitlementSearchFilterParams);

        if (!empty($entitlementSearchFilterParams->getEmpNumber())) {
            $q->andWhere('entitlement.employee = :empNumber')
                ->setParameter('empNumber', $entitlementSearchFilterParams->getEmpNumber());
        }

        if (!empty($entitlementSearchFilterParams->getEmpNumbers())) {
            $q->andWhere($q->expr()->in('entitlement.employee', ':empNumbers'))
                ->setParameter('empNumbers', $entitlementSearchFilterParams->getEmpNumbers());
        }

        if (!empty($entitlementSearchFilterParams->getLeaveTypeId())) {
            $q->andWhere('entitlement.leaveType = :leaveTypeId')
                ->setParameter('leaveTypeId', $entitlementSearchFilterParams->getLeaveTypeId());
        }

        if (!empty($entitlementSearchFilterParams->getFromDate())) {
            $q->andWhere($q->expr()->gte('entitlement.fromDate', ':fromDate'))
                ->setParameter('fromDate', $entitlementSearchFilterParams->getFromDate());
        }

        if (!empty($entitlementSearchFilterParams->getToDate())) {
            $q->andWhere($q->expr()->lte('entitlement.toDate', ':toDate'))
                ->setParameter('toDate', $entitlementSearchFilterParams->getToDate());
        }

        // get predictable sorting
        $q->addOrderBy('entitlement.id');

        return $this->getQueryBuilderWrapper($q);
    }

    /**
     * @param int $id
     * @return LeaveEntitlement|null
     */
    public function getLeaveEntitlement(int $id): ?LeaveEntitlement
    {
        return $this->getRepository(LeaveEntitlement::class)->find($id);
    }

    /**
     * @param LeaveEntitlement $leaveEntitlement
     * @return LeaveEntitlement
     * @throws TransactionException
     */
    public function saveLeaveEntitlement(LeaveEntitlement $leaveEntitlement): LeaveEntitlement
    {
        $this->beginTransaction();

        try {
            $this->persist($leaveEntitlement);
            $this->linkLeaveToUnusedLeaveEntitlement($leaveEntitlement);

            $this->commitTransaction();
            return $leaveEntitlement;
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }
    }

    /**
     * @param LeaveEntitlement[] $entitlements
     * @param int $leaveTypeId
     * @param DateTime $fromDate
     * @param DateTime $toDate
     */
    protected function bulkLinkLeaveToUnusedLeaveEntitlements(
        array $entitlements,
        int $leaveTypeId,
        DateTime $fromDate,
        DateTime $toDate
    ): void {
        $empNumbers = array_keys($entitlements);
        $leaveList = $this->getLeaveWithoutEntitlements($empNumbers, $leaveTypeId, $fromDate, $toDate);
        /** @var array<int, LeaveWithDaysLeft> $leaveListByEmployee */
        $leaveListByEmployee = [];

        foreach ($leaveList as $leave) {
            $empNumber = $leave->getEmpNumber();
            if (isset($leaveListByEmployee[$empNumber])) {
                $leaveListByEmployee[$empNumber][] = $leave;
            } else {
                $leaveListByEmployee[$empNumber] = [$leave];
            }
        }

        $q = $this->createQueryBuilder(LeaveLeaveEntitlement::class, 'lle')
            ->andWhere('lle.leave = :leaveId')
            ->andWhere('lle.entitlement = :entitlementId');

        foreach ($entitlements as $empNumber => $leaveEntitlement) {
            $balance = $leaveEntitlement->getNoOfDays() - $leaveEntitlement->getDaysUsed();

            if ($balance > 0 && isset($leaveListByEmployee[$empNumber])) {
                $entitlementId = $leaveEntitlement->getId();

                foreach ($leaveListByEmployee[$empNumber] as $leave) {
                    $daysLeft = $leave->getDaysLeft();
                    $leaveId = $leave->getId();
                    $daysToAssign = $daysLeft > $balance ? $balance : $daysLeft;

                    $leaveEntitlement->setDaysUsed($leaveEntitlement->getDaysUsed() - $daysToAssign);
                    $balance -= $daysToAssign;

                    // assign to leave
                    $q->setParameter('leaveId', $leaveId);
                    $q->setParameter('entitlementId', $entitlementId);
                    $entitlementAssignment = $this->fetchOne($q);

                    if ($entitlementAssignment instanceof LeaveLeaveEntitlement) {
                        $entitlementAssignment->setLengthDays($entitlementAssignment->getLengthDays() + $daysToAssign);
                    } else {
                        $entitlementAssignment = new LeaveLeaveEntitlement();
                        $entitlementAssignment->getDecorator()->setLeaveById($leaveId);
                        $entitlementAssignment->getDecorator()->setLeaveEntitlementById($entitlementId);
                        $entitlementAssignment->setLengthDays($daysToAssign);
                    }

                    $this->getEntityManager()->persist($entitlementAssignment);

                    if ($balance <= 0) {
                        break;
                    }
                }
            }
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @param LeaveEntitlement $leaveEntitlement
     */
    protected function linkLeaveToUnusedLeaveEntitlement(LeaveEntitlement $leaveEntitlement): void
    {
        $balance = $leaveEntitlement->getNoOfDays() - $leaveEntitlement->getDaysUsed();
        $entitlementId = $leaveEntitlement->getId();

        if ($balance > 0) {
            $leaveList = $this->getLeaveWithoutEntitlements(
                [$leaveEntitlement->getEmployee()->getEmpNumber()],
                $leaveEntitlement->getLeaveType()->getId(),
                $leaveEntitlement->getFromDate(),
                $leaveEntitlement->getToDate()
            );

            $q = $this->createQueryBuilder(LeaveLeaveEntitlement::class, 'l')
                ->andWhere('l.leave = :leaveId')
                ->andWhere('l.entitlement = :entitlementId')
                ->setParameter('entitlementId', $entitlementId);

            foreach ($leaveList as $leave) {
                $daysLeft = $leave->getDaysLeft();
                $leaveId = $leave->getId();
                $daysToAssign = $daysLeft > $balance ? $balance : $daysLeft;

                $leaveEntitlement->setDaysUsed($leaveEntitlement->getDaysUsed() - $daysToAssign);
                $balance -= $daysToAssign;

                $q->setParameter('leaveId', $leaveId);
                // assign to leave
                $entitlementAssignment = $this->fetchOne($q);

                if ($entitlementAssignment instanceof LeaveLeaveEntitlement) {
                    $entitlementAssignment->setLengthDays($entitlementAssignment->getLengthDays() + $daysToAssign);
                } else {
                    $entitlementAssignment = new LeaveLeaveEntitlement();
                    $entitlementAssignment->getDecorator()->setLeaveById($leaveId);
                    $entitlementAssignment->setEntitlement($leaveEntitlement);
                    $entitlementAssignment->setLengthDays($daysToAssign);
                }

                $this->getEntityManager()->persist($entitlementAssignment);

                if ($balance <= 0) {
                    break;
                }
            }
        }
        $this->getEntityManager()->flush();
    }

    /**
     * @param int[] $ids
     * @return LeaveEntitlement[]
     */
    public function getLeaveEntitlementsByIds(array $ids): array
    {
        $q = $this->createQueryBuilder(LeaveEntitlement::class, 'le');
        $q->where($q->expr()->in('le.id', ':ids'))
            ->setParameter('ids', $ids);
        return $q->getQuery()->execute();
    }

    /**
     * @param int[] $ids
     * @return int
     */
    public function deleteLeaveEntitlements(array $ids): int
    {
        $q = $this->createQueryBuilder(LeaveEntitlement::class, 'le')
            ->update()
            ->set('le.deleted', ':deleted')
            ->setParameter('deleted', true);

        $q->where($q->expr()->in('le.id', ':ids'))
            ->setParameter('ids', $ids);
        return $q->getQuery()->execute();
    }

    /**
     * @param int[] $employeeNumbers
     * @param LeaveEntitlement $leaveEntitlement
     * @return array array(LeaveEntitlement[], int)
     * @throws TransactionException
     */
    public function bulkAssignLeaveEntitlements(array $employeeNumbers, LeaveEntitlement $leaveEntitlement): array
    {
        $this->beginTransaction();
        try {
            $allEntitlements = [];
            $updateEmpList = [];
            $savedCount = 0;
            $leaveType = $leaveEntitlement->getLeaveType();
            $leaveTypeId = $leaveType->getId();
            $fromDate = $leaveEntitlement->getFromDate();
            $toDate = $leaveEntitlement->getToDate();

            $leaveEntitlementSearchFilterParams = new LeaveEntitlementSearchFilterParams();
            $leaveEntitlementSearchFilterParams->setLeaveTypeId($leaveTypeId);
            $leaveEntitlementSearchFilterParams->setFromDate($fromDate);
            $leaveEntitlementSearchFilterParams->setToDate($toDate);
            $leaveEntitlementSearchFilterParams->setLimit(0);

            $q = $this->getLeaveEntitlementsQueryBuilderWrapper($leaveEntitlementSearchFilterParams)->getQueryBuilder();
            $q->andWhere($q->expr()->in('entitlement.employee', ':empNumbers'))
                ->setParameter('empNumbers', $employeeNumbers);

            /** @var LeaveEntitlement[] $entitlementList */
            $entitlementList = $q->getQuery()->execute();
            if (!empty($entitlementList)) {
                foreach ($entitlementList as $updateEntitlement) {
                    $employee = $updateEntitlement->getEmployee();
                    $empNumber = $employee->getEmpNumber();

                    if (!isset($allEntitlements[$empNumber])) {
                        $updateEntitlement->setEntitlementType($leaveEntitlement->getEntitlementType());
                        $updateEntitlement->setNoOfDays(
                            $updateEntitlement->getNoOfDays() + $leaveEntitlement->getNoOfDays()
                        );
                        $updateEntitlement->setCreditedDate($leaveEntitlement->getCreditedDate());
                        $updateEntitlement->setCreatedBy($leaveEntitlement->getCreatedBy());

                        $this->getEntityManager()->persist($updateEntitlement);

                        $updateEmpList[] = $empNumber;
                        $allEntitlements[$empNumber] = $updateEntitlement;
                        $savedCount++;
                    }
                }
            }

            $newEmployeeList = array_diff($employeeNumbers, $updateEmpList);
            if (!empty($newEmployeeList)) {
                foreach ($newEmployeeList as $empNumber) {
                    if (!isset($allEntitlements[$empNumber])) {
                        $entitlement = new LeaveEntitlement();
                        $entitlement->getDecorator()->setEmployeeByEmpNumber($empNumber);
                        $entitlement->setLeaveType($leaveType);

                        $entitlement->setCreditedDate($leaveEntitlement->getCreditedDate());
                        $entitlement->setCreatedBy($leaveEntitlement->getCreatedBy());
                        $entitlement->setEntitlementType($leaveEntitlement->getEntitlementType());

                        $entitlement->setNoOfDays($leaveEntitlement->getNoOfDays());
                        $entitlement->setFromDate($fromDate);
                        $entitlement->setToDate($toDate);

                        $this->getEntityManager()->persist($entitlement);

                        $allEntitlements[$empNumber] = $entitlement;
                        $savedCount++;
                    }
                }
            }

            $this->getEntityManager()->flush();

            // If leave period is forced, we can bulk assign at once, because from and to date of
            // all leave entitlements will be the same
            $leavePeriodStatus = $this->getLeaveConfigService()->getLeavePeriodStatus();
            if ($leavePeriodStatus == LeavePeriodService::LEAVE_PERIOD_STATUS_FORCED) {
                $this->bulkLinkLeaveToUnusedLeaveEntitlements($allEntitlements, $leaveTypeId, $fromDate, $toDate);
            } else {
                foreach ($allEntitlements as $leaveEntitlement) {
                    $this->linkLeaveToUnusedLeaveEntitlement($leaveEntitlement);
                }
            }

            $this->commitTransaction();
            return [$allEntitlements, $savedCount];
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }
    }

    /**
     * @param int $empNumber
     * @param int $leaveTypeId
     * @param DateTime $fromDate
     * @param DateTime $toDate
     * @param string $orderField
     * @param string $order
     * @return LeaveEntitlement[]
     */
    public function getValidLeaveEntitlements(
        int $empNumber,
        int $leaveTypeId,
        DateTime $fromDate,
        DateTime $toDate,
        string $orderField,
        string $order
    ): array {
        $q = $this->createQueryBuilder(LeaveEntitlement::class, 'le');
        $q->andWhere($q->expr()->between(':fromDate', 'le.fromDate', 'le.toDate'))
            ->orWhere($q->expr()->between(':toDate', 'le.fromDate', 'le.toDate'))
            ->orWhere($q->expr()->between('le.fromDate', ':fromDate', ':toDate'))
            ->setParameter('fromDate', $fromDate)
            ->setParameter('toDate', $toDate);
        $q->andWhere('le.deleted = :deleted')
            ->setParameter('deleted', false);
        $q->andWhere('le.leaveType = :leaveType')
            ->setParameter('leaveType', $leaveTypeId);
        $q->andWhere('le.employee = :empNumber')
            ->setParameter('empNumber', $empNumber);
        $q->andWhere($q->expr()->gt($q->expr()->diff('le.noOfDays', 'le.daysUsed'), ':balance'))
            ->setParameter('balance', 0);

        $q->addOrderBy($orderField, $order);

        return $q->getQuery()->execute();
    }

    /**
     * Get leave balance as a LeaveBalance object with the following components
     *    * entitlements
     *    * used (taken)
     *    * scheduled
     *    * pending approval
     *    * leave without entitlements
     *
     * @param int $empNumber Employee Number
     * @param int $leaveTypeId Leave Type ID
     * @param DateTime $asAtDate Balance as at given date
     * @param DateTime|null $date
     * @return LeaveBalance Returns leave balance object
     */
    public function getLeaveBalance(
        int $empNumber,
        int $leaveTypeId,
        DateTime $asAtDate,
        ?DateTime $date = null
    ): LeaveBalance {
        $formattedAsAtDate = $this->getDateTimeHelper()->formatDateTimeToYmd($asAtDate);
        $formattedDate = $this->getDateTimeHelper()->formatDateTimeToYmd($date);
        $conn = $this->getEntityManager()->getConnection();

        $pendingIds = $this->getPendingStatusIds();

        $pendingIdList = is_array($pendingIds) ? implode(',', $pendingIds) : $pendingIds;

        // TODO:: re-write using doctrine query builder
        $sql = 'SELECT le.no_of_days AS entitled, ' .
            'le.days_used AS used, ' .
            'sum(IF(l.status = 2, lle.length_days, 0)) AS scheduled, ' .
            'sum(IF(l.status IN (' . $pendingIdList . '), lle.length_days, 0)) AS pending, ' .
            'sum(IF(l.status = 3, l.length_days, 0)) AS taken ' .
            'FROM ohrm_leave_entitlement le LEFT JOIN ' .
            'ohrm_leave_leave_entitlement lle ON le.id = lle.entitlement_id LEFT JOIN ' .
            'ohrm_leave l ON l.id = lle.leave_id ' .
            'WHERE le.deleted = 0 AND le.emp_number = ? AND le.leave_type_id = ? ' .
            ' AND le.to_date >= ?';

        $parameters = [$empNumber, $leaveTypeId, $formattedAsAtDate];

        if (!empty($date)) {
            $sql .= ' AND ? BETWEEN le.from_date AND le.to_date ';
            $parameters[] = $formattedDate;
        }

        $sql .= ' GROUP BY le.id';

        $dateLimits = $this->getLeaveEntitlementService()
            ->getLeaveEntitlementStrategy()
            ->getLeaveWithoutEntitlementDateLimitsForLeaveBalance($asAtDate, $date, $empNumber, $leaveTypeId);

        if (is_array($dateLimits) && count($dateLimits) > 0) {
            $sql .= ' UNION ALL ' .
                'SELECT ' .
                '0 AS entitled, ' .
                'SUM(l.length_days) AS used, ' .
                'sum(IF(l.status = 2, l.length_days, 0)) AS scheduled, ' .
                'sum(IF(l.status IN (' . $pendingIdList . '), l.length_days, 0)) AS pending, ' .
                'sum(IF(l.status = 3, l.length_days, 0)) AS taken ' .
                'FROM ohrm_leave l ' .
                'LEFT JOIN ohrm_leave_leave_entitlement lle ON (lle.leave_id = l.id) ' .
                'WHERE (lle.leave_id IS NULL) AND l.emp_number = ? AND l.leave_type_id = ? AND l.status NOT IN (-1, 0) ' .
                ' AND l.date BETWEEN ? AND ? ';

            $parameters[] = $empNumber;
            $parameters[] = $leaveTypeId;
            $parameters[] = $this->getDateTimeHelper()->formatDateTimeToYmd($dateLimits[0]);
            $parameters[] = $this->getDateTimeHelper()->formatDateTimeToYmd($dateLimits[1]);

            $sql .= 'GROUP BY l.leave_type_id';
        }

        $sql = 'SELECT sum(a.entitled) as entitled, sum(a.used) as used, sum(a.scheduled) as scheduled, ' .
            'sum(a.pending) as pending, sum(a.taken) as taken  ' .
            ' FROM (' . $sql . ') as a';

        $statement = $conn->prepare($sql);
        $result = $statement->executeQuery($parameters);

        $balance = new LeaveBalance();
        $balance->setAsAtDate($asAtDate);
        $balance->setEndDate($date);
        if ($result->rowCount() > 0) {
            $result = $result->fetchAssociative();
            if (!empty($result['entitled'])) {
                $balance->setEntitled($result['entitled']);
            }
            if (!empty($result['used'])) {
                $balance->setUsed($result['used']);
            }
            if (!empty($result['scheduled'])) {
                $balance->setScheduled($result['scheduled']);
            }
            if (!empty($result['pending'])) {
                $balance->setPending($result['pending']);
            }
            if (!empty($result['taken'])) {
                $balance->setTaken($result['taken']);
            }
        }
        $balance->updateBalance();

        return $balance;
    }

    /**
     * @param int $leaveId
     * @return LeaveEntitlementUsage[]
     */
    public function getEntitlementUsageForLeave(int $leaveId): array
    {
        $select = 'NEW ' . LeaveEntitlementUsage::class .
            '(e.id, e.noOfDays, e.daysUsed, e.fromDate, e.toDate, SUM(lle.lengthDays))';
        $q = $this->createQueryBuilder(LeaveEntitlement::class, 'e')
            ->leftJoin('e.leaveLeaveEntitlements', 'lle')
            ->select($select);
        $q->andWhere('lle.leave = :leaveId')
            ->setParameter('leaveId', $leaveId);
        $q->andWhere('e.deleted = :deleted')
            ->setParameter('deleted', false);
        $q->addGroupBy('e.id')
            ->addOrderBy('e.fromDate');

        return $q->getQuery()->getResult();
    }

    /**
     * Get Leave without entitlements sorted by empNumber ASC and leave date ASC
     *
     * @param int[] $empNumbers array of integer employee numbers
     * @param int $leaveTypeId Leave type ID
     * @param DateTime $fromDate From Date
     * @param DateTime $toDate To Date
     * @return LeaveWithDaysLeft[] Array containing leave without entitlements.
     *
     * id -> leave id
     * date -> leave date
     * lengthHours -> leave length in hours
     * lengthDays -> leave length in days
     * status -> leave status
     * leaveTypeId -> leave type id
     * empNumber -> emp number
     * daysLeft -> days in leave that are not yet linked to an entitlement
     */
    public function getLeaveWithoutEntitlements(
        array $empNumbers,
        int $leaveTypeId,
        DateTime $fromDate,
        DateTime $toDate
    ): array {
        $statusList = [
            Leave::LEAVE_STATUS_LEAVE_REJECTED,
            Leave::LEAVE_STATUS_LEAVE_CANCELLED,
            Leave::LEAVE_STATUS_LEAVE_WEEKEND,
            Leave::LEAVE_STATUS_LEAVE_HOLIDAY
        ];

        // TODO:: use daysLeft result variable in new operator syntax
        $select = 'NEW ' . LeaveWithDaysLeft::class .
            '(l.id, l.date, l.lengthHours, l.lengthDays, l.status, IDENTITY(l.leaveType), IDENTITY(l.employee), l.lengthDays - SUM(COALESCE(lle.lengthDays, 0)))';
        $q = $this->createQueryBuilder(Leave::class, 'l')
            ->leftJoin('l.leaveLeaveEntitlements', 'lle')
            ->select('l.lengthDays - SUM(COALESCE(lle.lengthDays, 0)) AS HIDDEN daysLeft')
            ->addSelect($select);
        if (count($empNumbers) === 1) {
            $q->andWhere('l.employee = :empNumber')
                ->setParameter('empNumber', $empNumbers[0]);
        } else {
            $q->andWhere($q->expr()->in('l.employee', ':empNumbers'))
                ->setParameter('empNumbers', $empNumbers);
        }
        $q->andWhere($q->expr()->notIn('l.status', ':statuses'))
            ->setParameter('statuses', $statusList);
        $q->andWhere('l.leaveType = :leaveTypeId')
            ->setParameter('leaveTypeId', $leaveTypeId);
        $q->andWhere($q->expr()->gte('l.date', ':fromDate'))
            ->setParameter('fromDate', $fromDate);
        $q->andWhere($q->expr()->lte('l.date', ':toDate'))
            ->setParameter('toDate', $toDate);
        $q->addGroupBy('l.id')
            ->addOrderBy('l.employee')
            ->addOrderBy('l.date');
        $q->andHaving($q->expr()->gt('daysLeft', ':daysLeft'))
            ->setParameter('daysLeft', 0);

        return $q->getQuery()->getResult();
    }

    /**
     * @param int $empNumber
     * @param DateTime|null $fromDate
     * @param DateTime|null $toDate
     * @param int|null $leaveTypeId
     * @return LeaveEntitlement[]
     */
    public function getMatchingEntitlements(
        int $empNumber,
        ?DateTime $fromDate = null,
        ?DateTime $toDate = null,
        ?int $leaveTypeId = null
    ): array {
        $q = $this->createQueryBuilder(LeaveEntitlement::class, 'le')
            ->andWhere('le.deleted = :deleted')
            ->setParameter('deleted', false)
            ->andWhere('le.employee = :empNumber')
            ->setParameter('empNumber', $empNumber);

        if ($fromDate) {
            $q->andWhere('le.fromDate = :fromDate')
                ->setParameter('fromDate', $fromDate);
        }
        if ($toDate) {
            $q->andWhere('le.toDate = :toDate')
                ->setParameter('toDate', $toDate);
        }
        if ($leaveTypeId) {
            $q->andWhere('le.leaveType = :leaveTypeId')
                ->setParameter('leaveTypeId', $leaveTypeId);
        }

        return $q->getQuery()->execute();
    }

    /**
     * @param EmployeeLeaveEntitlementUsageReportSearchFilterParams $filterParams
     * @return LeaveType[]
     */
    public function getLeaveTypesForEntitlementUsageReport(
        EmployeeLeaveEntitlementUsageReportSearchFilterParams $filterParams
    ): array {
        return $this->getLeaveTypesPaginatorForEntitlementUsageReport($filterParams)->getQuery()->execute();
    }

    /**
     * @param EmployeeLeaveEntitlementUsageReportSearchFilterParams $filterParams
     * @return int
     */
    public function getLeaveTypesCountForEntitlementUsageReport(
        EmployeeLeaveEntitlementUsageReportSearchFilterParams $filterParams
    ): int {
        return $this->getLeaveTypesPaginatorForEntitlementUsageReport($filterParams)->count();
    }

    /**
     * @param EmployeeLeaveEntitlementUsageReportSearchFilterParams $filterParams
     * @return Paginator
     */
    private function getLeaveTypesPaginatorForEntitlementUsageReport(
        EmployeeLeaveEntitlementUsageReportSearchFilterParams $filterParams
    ): Paginator {
        $q = $this->createQueryBuilder(LeaveType::class, 'leaveType')
            ->leftJoin('leaveType.leaveEntitlement', 'leaveEntitlement');
        $this->setSortingAndPaginationParams($q, $filterParams);

        $orClauses = $q->expr()->orX();
        $orClauses->add(
            $q->expr()->andX(
                $q->expr()->lte('leaveEntitlement.fromDate', ':fromDate'),
                $q->expr()->gte('leaveEntitlement.toDate', ':fromDate')
            )
        );
        $orClauses->add(
            $q->expr()->andX(
                $q->expr()->lte('leaveEntitlement.fromDate', ':toDate'),
                $q->expr()->gte('leaveEntitlement.toDate', ':toDate')
            )
        );
        $orClauses->add(
            $q->expr()->andX(
                $q->expr()->gte('leaveEntitlement.fromDate', ':fromDate'),
                $q->expr()->lte('leaveEntitlement.toDate', ':toDate')
            )
        );
        $q->andWhere(
            $q->expr()->orX(
                $q->expr()->andX(
                    'leaveType.situational = :situational',
                    'leaveEntitlement.employee = :empNumber',
                    $orClauses,
                ),
                'leaveType.situational = :notSituational'
            )
        );
        $q->setParameter('situational', true)
            ->setParameter('notSituational', false)
            ->setParameter('empNumber', $filterParams->getEmpNumber())
            ->setParameter('fromDate', $filterParams->getFromDate())
            ->setParameter('toDate', $filterParams->getToDate());
        $q->groupBy('leaveType.id');

        return $this->getPaginator($q);
    }

    /**
     * @param LeaveTypeLeaveEntitlementUsageReportSearchFilterParams $filterParams
     * @return Employee[]
     */
    public function getEmployeesForEntitlementUsageReport(
        LeaveTypeLeaveEntitlementUsageReportSearchFilterParams $filterParams
    ): array {
        return $this->getEmployeesPaginatorForEntitlementUsageReport($filterParams)->getQuery()->execute();
    }

    /**
     * @param LeaveTypeLeaveEntitlementUsageReportSearchFilterParams $filterParams
     * @return int
     */
    public function getEmployeesCountForEntitlementUsageReport(
        LeaveTypeLeaveEntitlementUsageReportSearchFilterParams $filterParams
    ): int {
        return $this->getEmployeesPaginatorForEntitlementUsageReport($filterParams)->count();
    }

    /**
     * @param LeaveTypeLeaveEntitlementUsageReportSearchFilterParams $filterParams
     * @return Paginator
     */
    private function getEmployeesPaginatorForEntitlementUsageReport(
        LeaveTypeLeaveEntitlementUsageReportSearchFilterParams $filterParams
    ): Paginator {
        $q = $this->createQueryBuilder(Employee::class, 'employee');
        $q->andWhere($q->expr()->isNull('employee.purgedAt'));
        $q->leftJoin('employee.locations', 'location');
        $this->setSortingAndPaginationParams($q, $filterParams);

        if ($filterParams->getIncludeEmployees() ===
            LeaveTypeLeaveEntitlementUsageReportSearchFilterParams::INCLUDE_EMPLOYEES_ONLY_CURRENT) {
            $q->andWhere($q->expr()->isNull('employee.employeeTerminationRecord'));
        } elseif ($filterParams->getIncludeEmployees() ===
            LeaveTypeLeaveEntitlementUsageReportSearchFilterParams::INCLUDE_EMPLOYEES_ONLY_PAST) {
            $q->andWhere($q->expr()->isNotNull('employee.employeeTerminationRecord'));
        }

        if (!is_null($filterParams->getSubunitId())) {
            $q->andWhere($q->expr()->in('employee.subDivision', ':subunitIds'))
                ->setParameter('subunitIds', $filterParams->getSubunitIdChain());
        }

        if (!is_null($filterParams->getLocationId())) {
            $q->andWhere('location.id = :locationId')
                ->setParameter('locationId', $filterParams->getLocationId());
        }

        if (!is_null($filterParams->getJobTitleId())) {
            $q->andWhere('employee.jobTitle = :jobTitleId')
                ->setParameter('jobTitleId', $filterParams->getJobTitleId());
        }

        if (!is_null($filterParams->getEmpNumbers())) {
            $q->andWhere($q->expr()->in('employee.empNumber', ':empNumbers'))
                ->setParameter('empNumbers', $filterParams->getEmpNumbers());
        }
        return $this->getPaginator($q);
    }
}
