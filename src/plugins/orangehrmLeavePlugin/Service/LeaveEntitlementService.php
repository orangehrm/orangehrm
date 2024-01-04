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

namespace OrangeHRM\Leave\Service;

use DateTime;
use OrangeHRM\Core\Traits\ClassHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\LeaveEntitlement;
use OrangeHRM\Leave\Dao\LeaveEntitlementDao;
use OrangeHRM\Leave\Entitlement\EntitlementConsumptionStrategy;
use OrangeHRM\Leave\Entitlement\FIFOEntitlementConsumptionStrategy;
use OrangeHRM\Leave\Entitlement\LeaveBalance;
use OrangeHRM\Leave\Traits\Service\LeaveConfigServiceTrait;
use OrangeHRM\ORM\Exception\TransactionException;

class LeaveEntitlementService
{
    use LeaveConfigServiceTrait;
    use UserRoleManagerTrait;
    use DateTimeHelperTrait;
    use ClassHelperTrait;

    /**
     * @var LeaveEntitlementDao|null
     */
    protected ?LeaveEntitlementDao $leaveEntitlementDao = null;

    /**
     * @var EntitlementConsumptionStrategy|null
     */
    protected ?EntitlementConsumptionStrategy $leaveEntitlementStrategy = null;

    /**
     * @return EntitlementConsumptionStrategy|FIFOEntitlementConsumptionStrategy
     */
    public function getLeaveEntitlementStrategy(): EntitlementConsumptionStrategy
    {
        if (!$this->leaveEntitlementStrategy instanceof EntitlementConsumptionStrategy) {
            $strategyClass = $this->getLeaveConfigService()->getLeaveEntitlementConsumptionStrategy();
            $strategyClass = $this->getClassHelper()->getClass($strategyClass, 'OrangeHRM\\Leave\\Entitlement\\');
            $this->leaveEntitlementStrategy = new $strategyClass();
        }
        return $this->leaveEntitlementStrategy;
    }

    /**
     * @return LeaveEntitlementDao
     */
    public function getLeaveEntitlementDao(): LeaveEntitlementDao
    {
        if (!($this->leaveEntitlementDao instanceof LeaveEntitlementDao)) {
            $this->leaveEntitlementDao = new LeaveEntitlementDao();
        }
        return $this->leaveEntitlementDao;
    }

    /**
     * @param int[] $ids
     * @return int[]
     */
    public function getDeletableIdsFromEntitlementIds(array $ids): array
    {
        $deletableIds = [];
        $entitlementList = $this->getLeaveEntitlementDao()->getLeaveEntitlementsByIds($ids);
        foreach ($entitlementList as $entitlement) {
            if (!$this->isDeletable($entitlement)) {
                continue;
            }
            $deletableIds[] = $entitlement->getId();
        }
        return $deletableIds;
    }

    /**
     * @param LeaveEntitlement $entitlement
     * @return bool
     */
    public function isDeletable(LeaveEntitlement $entitlement): bool
    {
        return !$entitlement->getDaysUsed() > 0;
    }

    /**
     * @param int $empNumber
     * @param int $leaveTypeId
     * @param DateTime|null $asAtDate
     * @param DateTime|null $date
     * @return LeaveBalance
     */
    public function getLeaveBalance(
        int $empNumber,
        int $leaveTypeId,
        ?DateTime $asAtDate = null,
        ?DateTime $date = null
    ): LeaveBalance {
        if (is_null($asAtDate)) {
            $asAtDate = $this->getDateTimeHelper()->getNow();
        }
        // If end date is not defined, and leave period is forced, use end date of current leave period
        // as the end date for leave balance calculation
        if (empty($date)) {
            $leavePeriodStatus = $this->getLeaveConfigService()->getLeavePeriodStatus();
            if ($leavePeriodStatus == LeavePeriodService::LEAVE_PERIOD_STATUS_FORCED) {
                $leavePeriod = $this->getLeaveEntitlementStrategy()->getLeavePeriod(
                    $asAtDate,
                    $empNumber,
                    $leaveTypeId
                );

                if (!is_null($leavePeriod) && !is_null($leavePeriod->getEndDate())) {
                    $date = $leavePeriod->getEndDate();
                }
            }
        }

        return $this->getLeaveEntitlementDao()->getLeaveBalance($empNumber, $leaveTypeId, $asAtDate, $date);
    }

    /**
     * @param int $empNumber
     * @param int $leaveTypeId
     * @param DateTime $fromDate
     * @param DateTime $toDate
     * @param float $entitlement
     * @return LeaveEntitlement
     * @throws TransactionException
     */
    public function addEntitlementForEmployee(
        int $empNumber,
        int $leaveTypeId,
        DateTime $fromDate,
        DateTime $toDate,
        float $entitlement
    ): LeaveEntitlement {
        $leaveEntitlement = null;
        if ($this->getLeaveConfigService()->getLeavePeriodStatus() == LeavePeriodService::LEAVE_PERIOD_STATUS_FORCED) {
            $entitlementList = $this->getLeaveEntitlementDao()->getMatchingEntitlements(
                $empNumber,
                $fromDate,
                $toDate,
                $leaveTypeId
            );

            // See if there is an added type entitlement
            foreach ($entitlementList as $existingEntitlement) {
                if (LeaveEntitlement::ENTITLEMENT_TYPE_ADD == $existingEntitlement->getEntitlementType()->getId()) {
                    $leaveEntitlement = $existingEntitlement;
                    $newValue = $leaveEntitlement->getNoOfDays() + $entitlement;
                    $leaveEntitlement->setNoOfDays($newValue);
                    break;
                }
            }
        }

        if (is_null($leaveEntitlement)) {
            $leaveEntitlement = new LeaveEntitlement();
            $leaveEntitlement->setNoOfDays($entitlement);
            $leaveEntitlement->getDecorator()->setEmployeeByEmpNumber($empNumber);
            $leaveEntitlement->getDecorator()->setLeaveTypeById($leaveTypeId);
        }

        $leaveEntitlement->setCreditedDate($this->getDateTimeHelper()->getNow());
        $leaveEntitlement->setCreatedBy($this->getUserRoleManager()->getUser());
        $leaveEntitlement->getDecorator()->setEntitlementTypeById(LeaveEntitlement::ENTITLEMENT_TYPE_ADD);
        $leaveEntitlement->setFromDate($fromDate);
        $leaveEntitlement->setToDate($toDate);

        return $this->getLeaveEntitlementDao()->saveLeaveEntitlement($leaveEntitlement);
    }

    /**
     * @param int[] $empNumbers
     * @param int $leaveTypeId
     * @param DateTime $fromDate
     * @param DateTime $toDate
     * @param float $entitlement
     * @return array array(LeaveEntitlement[], int)
     * @throws TransactionException
     */
    public function bulkAssignLeaveEntitlements(
        array $empNumbers,
        int $leaveTypeId,
        DateTime $fromDate,
        DateTime $toDate,
        float $entitlement
    ): array {
        // Use as DTO
        $leaveEntitlement = new LeaveEntitlement();
        $leaveEntitlement->setNoOfDays($entitlement);
        $leaveEntitlement->getDecorator()->setLeaveTypeById($leaveTypeId);
        $leaveEntitlement->setCreditedDate($this->getDateTimeHelper()->getNow());
        $leaveEntitlement->setCreatedBy($this->getUserRoleManager()->getUser());
        $leaveEntitlement->getDecorator()->setEntitlementTypeById(LeaveEntitlement::ENTITLEMENT_TYPE_ADD);
        $leaveEntitlement->setFromDate($fromDate);
        $leaveEntitlement->setToDate($toDate);

        return $this->getLeaveEntitlementDao()->bulkAssignLeaveEntitlements($empNumbers, $leaveEntitlement);
    }
}
