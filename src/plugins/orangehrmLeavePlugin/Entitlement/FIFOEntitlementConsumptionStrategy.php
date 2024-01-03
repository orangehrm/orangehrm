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

namespace OrangeHRM\Leave\Entitlement;

use DateTime;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Entity\LeaveEntitlement;
use OrangeHRM\Leave\Dao\FIFOEntitlementConsumptionStrategyDao;
use OrangeHRM\Leave\Dto\CurrentAndChangeEntitlement;
use OrangeHRM\Leave\Dto\LeavePeriod;
use OrangeHRM\Leave\Dto\LeaveWithDaysLeft;
use OrangeHRM\Leave\Traits\Service\LeaveEntitlementServiceTrait;
use OrangeHRM\Leave\Traits\Service\LeavePeriodServiceTrait;
use OrangeHRM\ORM\ListSorter;

class FIFOEntitlementConsumptionStrategy implements EntitlementConsumptionStrategy
{
    use LeavePeriodServiceTrait;
    use LeaveEntitlementServiceTrait;

    /**
     * @var FIFOEntitlementConsumptionStrategyDao|null
     */
    protected ?FIFOEntitlementConsumptionStrategyDao $dao = null;

    /**
     * @return FIFOEntitlementConsumptionStrategyDao
     */
    public function getDao(): FIFOEntitlementConsumptionStrategyDao
    {
        if (empty($this->dao)) {
            $this->dao = new FIFOEntitlementConsumptionStrategyDao();
        }

        return $this->dao;
    }

    /**
     * @param int $empNumber
     * @param int $leaveTypeId
     * @param Leave[] $leaveDates
     * @param bool $allowNoEntitlements
     * @return CurrentAndChangeEntitlement|null
     */
    public function handleLeaveCreate(
        int $empNumber,
        int $leaveTypeId,
        array $leaveDates,
        bool $allowNoEntitlements = false
    ): ?CurrentAndChangeEntitlement {
        $result = null;
        $current = [];
        $change = [];

        $numDates = count($leaveDates);
        $leaveLength = 0;
        if ($numDates > 0) {
            $fromDate = null;
            $toDate = null;

            foreach ($leaveDates as $leaveDate) {
                $length = $leaveDate->getLengthDays();
                if ($length > 0) {
                    if (is_null($fromDate)) {
                        $fromDate = $leaveDate->getDate();
                    }
                    $toDate = $leaveDate->getDate();
                }
                $leaveLength += $length;
            }

            $entitlementsOk = true;

            if (!is_null($fromDate)) {
                $newEntitlements = $this->getLeaveEntitlementService()
                    ->getLeaveEntitlementDao()
                    ->getValidLeaveEntitlements(
                        $empNumber,
                        $leaveTypeId,
                        $fromDate,
                        $toDate,
                        'le.fromDate',
                        ListSorter::ASCENDING
                    );

                // TODO Get currently assigned leave dates and add to $leaveDates
                $entitlements = [];
                foreach ($newEntitlements as $entitlement) {
                    // use clone to avoid update Doctrine unit of work
                    $entitlements[] = clone $entitlement;
                }

                reset($leaveDates);

                $getNextDate = true;
                $entitlementsOk = false;
                $leaveDate = null;
                $leaveLength = 0;

                $leaveWithoutEntitlement = [];

                /** @var LeaveEntitlement $entitlement */
                $entitlement = array_shift($entitlements);

                if (!is_null($entitlement)) {
                    $availableDays = $entitlement->getDecorator()->getAvailableDays();
                }

                while (!is_null($entitlement)) {
                    if ($availableDays > 0) {
                        if ($getNextDate) {
                            $leaveDate = array_shift($leaveDates);

                            if (is_null($leaveDate)) {
                                $entitlementsOk = empty($leaveWithoutEntitlement);
                                $leaveLength = 0;
                                break;
                            } else {
                                $leaveLength = $leaveDate->getLengthDays();
                                $getNextDate = false;
                            }
                        }
                        if ($leaveLength <= 0) {
                            $getNextDate = true;
                        } elseif (!$entitlement->getDecorator()->withinPeriod($leaveDate->getDate())) {
                            if ($leaveDate->getDate() < $entitlement->getFromDate()) {
                                $getNextDate = true;
                                $leaveWithoutEntitlement[] = $leaveDate;
                            } elseif ($leaveDate->getDate() > $entitlement->getToDate()) {
                                $availableDays = 0;
                            }
                        } elseif ($leaveLength <= $availableDays) {
                            $entitlement->setDaysUsed($entitlement->getDaysUsed() + $leaveLength);
                            $availableDays -= $leaveLength;

                            $leaveId = $leaveDate->getId();

                            if (is_null($leaveId)) {
                                if (!isset($current[$leaveDate->getDate()->format('Y-m-d')])) {
                                    $current[$leaveDate->getDate()->format('Y-m-d')] = [];
                                }
                                $current[$leaveDate->getDate()->format('Y-m-d')][$entitlement->getId()] = $leaveLength;
                            } else {
                                if (!isset($change[$leaveId])) {
                                    $change[$leaveId] = [];
                                }
                                $change[$leaveId][$entitlement->getId()] = $leaveLength;
                            }
                            $getNextDate = true;
                        } else {
                            $entitlement->setDaysUsed($entitlement->getNoOfDays());
                            $leaveLength -= $availableDays;

                            $leaveId = $leaveDate->getId();

                            $leaveDateYmd = $leaveDate->getDate()->format('Y-m-d');
                            if (is_null($leaveId)) {
                                if (!isset($current[$leaveDateYmd])) {
                                    $current[$leaveDateYmd] = [];
                                }
                                $current[$leaveDateYmd][$entitlement->getId()] = $availableDays;
                            } else {
                                if (!isset($change[$leaveId])) {
                                    $change[$leaveId] = [];
                                }
                                $change[$leaveId][$entitlement->getId()] = $availableDays;
                            }

                            $availableDays = 0;
                            $getNextDate = false;
                        }
                    } else {
                        /** @var LeaveEntitlement $entitlement */
                        $entitlement = array_shift($entitlements);
                        if (is_null($entitlement)) {
                            if (empty($leaveDates) && empty($leaveWithoutEntitlement) && $getNextDate) {
                                $entitlementsOk = true;
                            }
                        } else {
                            $availableDays = $entitlement->getDecorator()->getAvailableDays();
                        }
                    }
                }
            }
        }


        if ($allowNoEntitlements || ($entitlementsOk && (count($change) > 0 || count($current) > 0))) {
            $result = new CurrentAndChangeEntitlement($current, $change);
        }

        return $result;
    }

    public function handleEntitlementStatusChange()
    {
        // TODO
    }

    /**
     * @param Leave $leave
     * @return CurrentAndChangeEntitlement
     */
    public function handleLeaveCancel(Leave $leave): CurrentAndChangeEntitlement
    {
        $current = [];
        $change = [];

        $entitlementArray = $this->getLeaveEntitlementService()
            ->getLeaveEntitlementDao()
            ->getEntitlementUsageForLeave($leave->getId());

        if (count($entitlementArray) > 0) {
            $minDate = null;
            $maxDate = null;

            // reduce entitlement usage for this leave

            foreach ($entitlementArray as $entitlementItem) {
                // use clone to avoid update Doctrine unit of work
                $entitlementItem = clone $entitlementItem;

                $entitlementItem->setDaysUsed($entitlementItem->getDaysUsed() - $entitlementItem->getLengthDays());
                if ($entitlementItem->getDaysUsed() < 0) {
                    $entitlementItem->setDaysUsed(0);
                }
                if (is_null($minDate)) {
                    $minDate = $entitlementItem->getFromDate();
                    $maxDate = $entitlementItem->getToDate();
                } else {
                    if ($minDate > $entitlementItem->getFromDate()) {
                        $minDate = $entitlementItem->getFromDate();
                    }
                    if ($maxDate < $entitlementItem->getToDate()) {
                        $maxDate = $entitlementItem->getToDate();
                    }
                }
            }

            // Get leave without entitlements between from_date and to_date
            $leaveList = $this->getLeaveEntitlementService()
                ->getLeaveEntitlementDao()
                ->getLeaveWithoutEntitlements(
                    [$leave->getEmployee()->getEmpNumber()],
                    $leave->getLeaveType()->getId(),
                    $minDate,
                    $maxDate
                );

            // remove current leave from list
            /** @var LeaveWithDaysLeft[] $leaveDates */
            $leaveDates = [];
            foreach ($leaveList as $leaveDateTemp) {
                if ($leaveDateTemp->getId() != $leave->getId()) {
                    $leaveDates[] = $leaveDateTemp;
                }
            }

            /** @var LeaveEntitlement[] $entitlements */
            $entitlements = [];
            foreach ($entitlementArray as $entitlementItem) {
                $entitlement = new LeaveEntitlement();
                $entitlement->setId($entitlementItem->getId());
                $entitlement->setNoOfDays($entitlementItem->getNoOfDays());
                $newDaysUsed = $entitlementItem->getDaysUsed() - $entitlementItem->getLengthDays();
                if ($newDaysUsed < 0) {
                    $newDaysUsed = 0;
                }

                $entitlement->setDaysUsed($newDaysUsed);
                $entitlement->setFromDate($entitlementItem->getFromDate());
                $entitlement->setToDate($entitlementItem->getToDate());

                $entitlements[] = $entitlement;
            }

            reset($leaveDates);

            $getNextDate = true;
            $entitlementsOk = false;
            $leaveDate = null;
            $leaveLength = 0;

            $leaveWithoutEntitlement = [];

            $entitlement = array_shift($entitlements);

            if (!is_null($entitlement)) {
                $availableDays = $entitlement->getDecorator()->getAvailableDays();
            }

            while (!is_null($entitlement)) {
                if ($availableDays > 0) {
                    if ($getNextDate) {
                        $leaveDate = array_shift($leaveDates);
                        if (is_null($leaveDate)) {
                            $entitlementsOk = empty($leaveWithoutEntitlement);
                            $leaveLength = 0;
                            break;
                        } else {
                            $leaveLength = $leaveDate->getLengthDays();
                            $getNextDate = false;
                        }
                    }

                    if ($leaveLength <= 0) {
                        $getNextDate = true;
                    } else {
                        if (!$entitlement->getDecorator()->withinPeriod($leaveDate->getDate())) {
                            if ($leaveDate->getDate() < $entitlement->getFromDate()) {
                                $getNextDate = true;
                                $leaveWithoutEntitlement[] = $leaveDate;
                            } elseif ($leaveDate->getDate() > $entitlement->getToDate()) {
                                $availableDays = 0;
                            }
                        } else {
                            if ($leaveLength <= $availableDays) {
                                $entitlement->setDaysUsed($entitlement->getDaysUsed() + $leaveLength);
                                $availableDays -= $leaveLength;

                                $leaveId = $leaveDate->getId();

                                $leaveDateYmd = $leaveDate->getDate()->format('Y-m-d');
                                if (empty($leaveId)) {
                                    if (!isset($current[$leaveDateYmd])) {
                                        $current[$leaveDateYmd] = [];
                                    }
                                    $current[$leaveDateYmd][$entitlement->getId()] = $leaveLength;
                                } else {
                                    if (!isset($change[$leaveId])) {
                                        $change[$leaveId] = [];
                                    }
                                    $change[$leaveId][$entitlement->getId()] = $leaveLength;
                                }
                                $getNextDate = true;
                            } else {
                                $entitlement->setDaysUsed($entitlement->getNoOfDays());
                                $leaveLength -= $availableDays;

                                $leaveId = $leaveDate->getId();

                                $leaveDateYmd = $leaveDate->getDate()->format('Y-m-d');
                                if (empty($leaveId)) {
                                    if (!isset($current[$leaveDateYmd])) {
                                        $current[$leaveDateYmd] = [];
                                    }
                                    $current[$leaveDateYmd][$entitlement->getId()] = $availableDays;
                                } else {
                                    if (!isset($change[$leaveId])) {
                                        $change[$leaveId] = [];
                                    }
                                    $change[$leaveId][$entitlement->getId()] = $availableDays;
                                }

                                $availableDays = 0;
                                $getNextDate = false;
                            }
                        }
                    }
                } else {
                    $entitlement = array_shift($entitlements);
                    if (is_null($entitlement)) {
                        if (empty($leaveDates) && empty($leaveWithoutEntitlement) && $getNextDate) {
                            $entitlementsOk = true;
                        }
                    } else {
                        $availableDays = $entitlement->getDecorator()->getAvailableDays();
                    }
                }
            }
        }


        return new CurrentAndChangeEntitlement($current, $change);
    }

    /**
     * @inheritDoc
     */
    public function handleLeavePeriodChange(
        LeavePeriod $leavePeriodForToday,
        int $oldStartMonth,
        int $oldStartDay,
        int $newStartMonth,
        int $newStartDay
    ): void {
        $this->getDao()->handleLeavePeriodChange(
            $leavePeriodForToday,
            $oldStartMonth,
            $oldStartDay,
            $newStartMonth,
            $newStartDay
        );
    }

    /**
     * @inheritDoc
     */
    public function getLeaveWithoutEntitlementDateLimitsForLeaveBalance(
        DateTime $balanceStartDate,
        ?DateTime $balanceEndDate = null,
        ?int $empNumber = null,
        ?int $leaveTypeId = null
    ) {
        $limits = null;

        $startPeriod = $this->getLeavePeriod($balanceStartDate, $empNumber, $leaveTypeId);

        if (!is_null($startPeriod) && !is_null($startPeriod->getEndDate())) {
            $startDate = $startPeriod->getStartDate();
            $endDate = $startPeriod->getEndDate();

            if (!empty($balanceEndDate)) {
                $endPeriod = $this->getLeavePeriod($balanceEndDate, $empNumber, $leaveTypeId);
                if (!is_null($endPeriod) && !is_null($endPeriod->getEndDate())) {
                    $endDate = $endPeriod->getEndDate();
                }
            }
            $limits = [$startDate, $endDate];
        }

        return $limits;
    }

    /**
     * @inheritDoc
     */
    public function getLeavePeriod(DateTime $date, ?int $empNumber = null, ?int $leaveTypeId = null): ?LeavePeriod
    {
        return $this->getLeavePeriodService()->getCurrentLeavePeriodByDate($date);
    }
}
