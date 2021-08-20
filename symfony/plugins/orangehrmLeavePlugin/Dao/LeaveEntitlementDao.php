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
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Entity\LeaveEntitlement;
use OrangeHRM\Entity\LeaveLeaveEntitlement;
use OrangeHRM\Leave\Dto\LeaveEntitlementSearchFilterParams;
use OrangeHRM\Leave\Entitlement\LeaveBalance;
use OrangeHRM\Leave\Traits\Service\LeaveConfigServiceTrait;
use OrangeHRM\Leave\Traits\Service\LeaveEntitlementServiceTrait;
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
     * @throws DaoException
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
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    protected function bulkLinkLeaveToUnusedLeaveEntitlements($entitlements, $leaveTypeId, $fromDate, $toDate) {
        // TODO:: not converted
        // sort collection by $empNumber key;
        $result = ksort($entitlements, SORT_NUMERIC);
        if (!$result) {
            echo "Sort failed";
            die();
        }

        $empNumbers = array_keys($entitlements);
        $leaveList = $this->getLeaveWithoutEntitlements($empNumbers, $leaveTypeId, $fromDate, $toDate);
        $leaveListByEmployee = [];

        foreach ($leaveList as $leave) {
            $empNumber = $leave['empNumber'];

            if (isset($leaveListByEmployee[$empNumber])) {
                $leaveListByEmployee[$empNumber][] = $leave;
            } else {
                $leaveListByEmployee[$empNumber] = [$leave];
            }
        }


        $query = Doctrine_Query::create()
                    ->from('LeaveLeaveEntitlement l')
                    ->where('l.leave_id = ?')
                    ->andWhere('l.entitlement_id = ?');

        foreach ($entitlements as $empNumber => $leaveEntitlement) {

            $balance = $leaveEntitlement->getNoOfDays() - $leaveEntitlement->getDaysUsed();

            if ($balance > 0 && isset($leaveListByEmployee[$empNumber])) {
                $entitlementId = $leaveEntitlement->getId();

                foreach ($leaveListByEmployee[$empNumber] as $leave) {
                    $daysLeft = $leave['days_left'];
                    $leaveId = $leave['id'];
                    $daysToAssign = $daysLeft > $balance ? $balance : $daysLeft;

                    $leaveEntitlement->setDaysUsed($leaveEntitlement->getDaysUsed() - $daysToAssign);
                    $balance -= $daysToAssign;

                    // assign to leave
                    $entitlementAssignment = $query->fetchOne([$leaveId, $entitlementId]);

                    if ($entitlementAssignment === false) {
                        $entitlementAssignment = new LeaveLeaveEntitlement();
                        $entitlementAssignment->setLeaveId($leaveId);
                        $entitlementAssignment->setEntitlementId($entitlementId);
                        $lengthDays = NumberUtility::getPositiveDecimal($daysToAssign, 4);
                    } else {
                        $lengthDays = NumberUtility::getPositiveDecimal($entitlementAssignment->getLengthDays() + $daysToAssign, 4);
                    }
                    $entitlementAssignment->setLengthDays($lengthDays);
                    $entitlementAssignment->save();
                    $entitlementAssignment->free();

                    if ($balance <= 0) {
                        break;
                    }
                }

            }
        }

        return $leaveEntitlement;
    }

    protected function linkLeaveToUnusedLeaveEntitlement(LeaveEntitlement $leaveEntitlement) {
        // TODO:: not converted
        $balance = $leaveEntitlement->getNoOfDays() - $leaveEntitlement->getDaysUsed();
        $entitlementId = $leaveEntitlement->getId();

        if ($balance > 0) {
            $leaveList = $this->getLeaveWithoutEntitlements(
                [$leaveEntitlement->getEmployee()->getEmpNumber()],
                $leaveEntitlement->getLeaveType()->getId(),
                $leaveEntitlement->getFromDate(),
                $leaveEntitlement->getToDate());

            $q = $this->createQueryBuilder(LeaveLeaveEntitlement::class, 'l')
                ->andWhere('l.leave', ':leaveId')
                ->andWhere('l.entitlement', ':entitlementId')
                ->setParameter('entitlementId',$entitlementId);

            foreach ($leaveList as $leave) {
                $daysLeft = $leave['days_left'];
                $leaveId = $leave['id'];
                $daysToAssign = $daysLeft > $balance ? $balance : $daysLeft;

                $leaveEntitlement->setDaysUsed($leaveEntitlement->getDaysUsed() - $daysToAssign);
                $balance -= $daysToAssign;

                $q->setParameter('leaveId', $leaveId);
                // assign to leave
                $entitlementAssignment = $this->fetchOne($q);

                if ($entitlementAssignment === false) {
                    $entitlementAssignment = new LeaveLeaveEntitlement();
                    $entitlementAssignment->getDecorator()->setLeaveById($leaveId);
                    $entitlementAssignment->setEntitlement($leaveEntitlement);
                    $entitlementAssignment->setLengthDays($daysToAssign);
                } else {
                    $lengthDays = NumberUtility::getPositiveDecimal($entitlementAssignment->getLengthDays() + $daysToAssign, 4);
                }
                $this->persist($entitlementAssignment);

                if ($balance <= 0) {
                    break;
                }
            }
        }

        return $leaveEntitlement;
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

    public function bulkAssignLeaveEntitlements($employeeNumbers, LeaveEntitlement $leaveEntitlement) {
        // TODO:: not converted
        $conn = Doctrine_Manager::connection();
        $conn->beginTransaction();

        $pdo = Doctrine_Manager::getInstance()->getCurrentConnection()->getDbh();
        try {
            $allEntitlements = [];
            $updateEmpList = [];
            $updateEntitlementIdList = [];
            $savedCount = 0;
            $leaveTypeId = $leaveEntitlement->getLeaveTypeId();
            $fromDate = $leaveEntitlement->getFromDate();
            $toDate = $leaveEntitlement->getToDate();

            $leaveEntitlementSearchParameterHolder = new LeaveEntitlementSearchParameterHolder();
            $leaveEntitlementSearchParameterHolder->setFromDate($fromDate);
            $leaveEntitlementSearchParameterHolder->setLeaveTypeId($leaveTypeId);
            $leaveEntitlementSearchParameterHolder->setToDate($toDate);
            $leaveEntitlementSearchParameterHolder->setEmpIdList($employeeNumbers);
            $leaveEntitlementSearchParameterHolder->setHydrationMode(Doctrine::HYDRATE_ARRAY);

            $entitlementList = $this->searchLeaveEntitlements($leaveEntitlementSearchParameterHolder);
            if (count($entitlementList) > 0) {
                foreach ($entitlementList as $updateEntitlement) {

                    $empNumber = $updateEntitlement['emp_number'];

                    if (!isset($allEntitlements[$empNumber])) {
                        $entitlement = new LeaveEntitlement();
                        $noOfDays = $leaveEntitlement->getNoOfDays();
                        $entitlement->setEmpNumber($empNumber);
                        $entitlement->setLeaveTypeId($leaveTypeId);

                        $entitlement->setCreditedDate($leaveEntitlement->getCreditedDate());
                        $entitlement->setCreatedById($leaveEntitlement->getCreatedById());
                        $entitlement->setCreatedByName($leaveEntitlement->getCreatedByName());

                        $entitlement->setEntitlementType($leaveEntitlement->getEntitlementType());
                        $entitlement->setDeleted(0);

                        $entitlement->setNoOfDays($leaveEntitlement->getNoOfDays());
                        $entitlement->setFromDate($fromDate);
                        $entitlement->setToDate($toDate);
                        $entitlement->setId($updateEntitlement['id']);

                        $allEntitlements[$empNumber] = $entitlement;

                        $updateEmpList[] = $updateEntitlement['emp_number'];
                        $updateEntitlementIdList[] = $updateEntitlement['id'];
                        $savedCount++;
                    }
                }


                $updateQuery = sprintf(" UPDATE ohrm_leave_entitlement SET no_of_days=no_of_days+ %f WHERE id IN (%s)", $leaveEntitlement->getNoOfDays(), implode(',', $updateEntitlementIdList));
                $updateStmt = $pdo->prepare($updateQuery);
                $updateStmt->execute();
            }

            $newEmployeeList = array_diff($employeeNumbers, $updateEmpList);
            if (count($newEmployeeList) > 0) {
                $query = " INSERT INTO ohrm_leave_entitlement(`emp_number`,`leave_type_id`,`from_date`,`to_date`,`no_of_days`,`entitlement_type`) VALUES " .
                         "(?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($query);

                foreach ($newEmployeeList as $empNumber) {
                    if (!isset($allEntitlements[$empNumber])) {
                        $entitlement = new LeaveEntitlement();
                        $noOfDays = $leaveEntitlement->getNoOfDays();
                        $entitlement->setEmpNumber($empNumber);
                        $entitlement->setLeaveTypeId($leaveEntitlement->getLeaveTypeId());

                        $entitlement->setCreditedDate($leaveEntitlement->getCreditedDate());
                        $entitlement->setCreatedById($leaveEntitlement->getCreatedById());
                        $entitlement->setCreatedByName($leaveEntitlement->getCreatedByName());

                        $entitlement->setEntitlementType($leaveEntitlement->getEntitlementType());
                        $entitlement->setDeleted(0);

                        $entitlement->setNoOfDays($noOfDays);
                        $entitlement->setFromDate($fromDate);
                        $entitlement->setToDate($toDate);

                        $params = [$empNumber, $leaveEntitlement->getLeaveTypeId(), $fromDate, $toDate, $noOfDays, LeaveEntitlement::ENTITLEMENT_TYPE_ADD];
                        $stmt->execute($params);
                        $entitlement->setId($pdo->lastInsertId());

                        $allEntitlements[$empNumber] = $entitlement;
                        $savedCount++;
                    }
                }
            }

            // If leave period is forced, we can bulk assign at once, because from and to date of
            // all leave entitlements will be the same
            $leavePeriodStatus = LeavePeriodService::getLeavePeriodStatus();
            if ($leavePeriodStatus == LeavePeriodService::LEAVE_PERIOD_STATUS_FORCED) {
                $this->bulkLinkLeaveToUnusedLeaveEntitlements($allEntitlements, $leaveTypeId, $fromDate, $toDate);
            } else {
                foreach ($allEntitlements as $leaveEntitlement) {
                    $this->linkLeaveToUnusedLeaveEntitlement($leaveEntitlement);
                }
            }

            $conn->commit();
            return $savedCount;
        } catch (Exception $e) {
            $conn->rollback();
            throw new DaoException($e->getMessage(), 0, $e);
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
        // TODO
        $q = $this->createQueryBuilder(LeaveEntitlement::class, 'le');
        $q->andWhere('le.deleted = :deleted')
            ->setParameter('deleted', false);
        $q->andWhere('le.leaveType = :leaveType')
            ->setParameter('leaveType', $leaveTypeId);
        $q->andWhere('le.employee = :empNumber')
            ->setParameter('empNumber', $empNumber);
        $q->andWhere($q->expr()->gt($q->expr()->diff('le.noOfDays', 'le.daysUsed'), ':balance'))
            ->setParameter('balance', 0);
        $q->andWhere($q->expr()->between(':fromDate', 'le.fromDate', 'le.toDate'))
            ->orWhere($q->expr()->between(':toDate', 'le.fromDate', 'le.toDate'))
            ->orWhere($q->expr()->between('le.fromDate', ':fromDate', ':toDate'))
            ->setParameter('fromDate', $fromDate)
            ->setParameter('toDate', $toDate);
//            $q = Doctrine_Query::create()->from('LeaveEntitlement le')
//                    ->addWhere('le.deleted = 0')
//                    ->addWhere('le.leave_type_id = :leaveTypeId')
//                    ->addWhere('le.emp_number = :empNumber')
//                    ->addWhere('(le.no_of_days - le.days_used) > 0')
//                    ->addWhere('(:fromDate BETWEEN le.from_date AND le.to_date) OR ' .
//                    '(:toDate BETWEEN le.from_date AND le.to_date) OR ' .
//                    '(le.from_date BETWEEN :fromDate AND :toDate)');

        $q->addOrderBy($orderField, $order);

        return $q->getQuery()->execute();
    }

    public function getLinkedLeaveRequests($entitlementIds, $statuses) {
        // TODO:: not converted
        try {
            $q = Doctrine_Query::create()->from('Leave l')
                    ->leftJoin('l.LeaveEntitlements le')
                    ->andWhereIn('le.id', $entitlementIds)
                    ->andWhereIn('l.status', $statuses)
                    ->addOrderBy('l.id ASC');

            $results = $q->execute();
            return $results;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), 0, $e);
        }
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
        if ($result) {
            if ($statement->rowCount() > 0) {
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
        }
        $balance->updateBalance();

        return $balance;
    }

    public function getEntitlementUsageForLeave($leaveId) {
        // TODO:: not converted
        try {
            $conn = Doctrine_Manager::connection()->getDbh();
            $query = "SELECT e.id, e.no_of_days, e.days_used, e.from_date, e.to_date, sum(lle.length_days) as length_days from ohrm_leave_entitlement e " .
                    "left join ohrm_leave_leave_entitlement lle on lle.entitlement_id = e.id where " .
                    "lle.leave_id = ? AND e.deleted = 0 group by e.id order by e.from_date ASC";
            $statement = $conn->prepare($query);
            $result = $statement->execute([$leaveId]);

            return $statement->fetchAll();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), 0, $e);
        }
    }

    /**
     * Get Leave without entitlements sorted by empNumber ASC and leave date ASC
     *
     * @param int[] $empNumbers integer employee number or array of integer employee numbers
     * @param int $leaveTypeId Leave type ID
     * @param DateTime $fromDate From Date
     * @param DateTime $toDate To Date
     * @return array Array containing leave without entitlements. Each element of the array contains the following:
     *
     * id -> leave id
     * date -> leave date
     * length_hours -> leave length in hours
     * length_days -> leave length in days
     * status -> leave status
     * leave_type_id -> leave type id
     * emp_number -> emp number
     * days_left -> days in leave that are not yet linked to an entitlement
     *
     * @throws DaoException
     */
    public function getLeaveWithoutEntitlements(array $empNumbers, int $leaveTypeId, DateTime $fromDate, DateTime $toDate) {
        // TODO:: not converted
        try {
            $statusList = [
                Leave::LEAVE_STATUS_LEAVE_REJECTED, Leave::LEAVE_STATUS_LEAVE_CANCELLED,
                Leave::LEAVE_STATUS_LEAVE_WEEKEND, Leave::LEAVE_STATUS_LEAVE_HOLIDAY
            ];

            $params = [];

            $questionMarks = str_repeat("?,", count($empNumbers) - 1) . "?";
            $empClause = ' IN (' . $questionMarks . ') ';
            $params = $empNumbers;

            $params = array_merge($params, [$leaveTypeId, $fromDate->format('Y-m-d'), $toDate->format('Y-m-d')]);

            $conn = $this->getEntityManager()->getConnection();
            $query = "select * from (select l.id, l.date, l.length_hours, l.length_days, l.status, l.leave_type_id, l.emp_number, " .
                    "l.length_days - sum(COALESCE(lle.length_days, 0)) as days_left " .
                    "from ohrm_leave l left join ohrm_leave_leave_entitlement lle on lle.leave_id = l.id " .
                    "where l.emp_number " . $empClause . " and l.leave_type_id = ? and l.date >= ? and l.date <= ? and " .
                    "l.status not in (" . implode(',', $statusList) . ") " .
                    "group by l.id order by l.emp_number ASC, l.`date` ASC) as A where days_left > 0";

            $statement = $conn->prepare($query);
            $result = $statement->execute($params);

            return $statement->fetchAll();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), 0, $e);
        }
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
}
