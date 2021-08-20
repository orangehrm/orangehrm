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

namespace OrangeHRM\Leave\Entitlement;

use DateTime;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Entity\LeaveEntitlement;
use OrangeHRM\Leave\Dao\FIFOEntitlementConsumptionStrategyDao;
use OrangeHRM\Leave\Dto\CurrentAndChangeEntitlement;
use OrangeHRM\Leave\Dto\LeavePeriod;
use OrangeHRM\Leave\Traits\Service\LeaveEntitlementServiceTrait;
use OrangeHRM\Leave\Traits\Service\LeavePeriodServiceTrait;

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
     * Get available entitlements for given leave parameters
     *
     * Returns an array with the following structure:
     * eg:
     * array('current' => array(
     *          '2012-01-01' => array(1 => 1)
     *          '2012-01-02' => array(4 => 1)),
     *       'change' => array(
     *          12 => array(3 => 1), / leave_id => array(entitlement_id => length) /
     *          13 => array(4 => 0.5))
     * )
     *
     * If unable to assign the requested leave, will return false
     *
     * Here 'current' contains assignments for the currently requested leave dates
     * 'change' contains assignments for existing leave requests that need to change.
     *
     * @param $empNumber int Employee Number
     * @param $leaveType int LeaveType
     * @param $leaveDates Array Array of LeaveDate => Length (days)
     * @return Array or false As described above
     */
    public function getAvailableEntitlements($empNumber, $leaveType, $leaveDates, $allowNoEntitlements = false)
    {
        // TODO
        $result = false;
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
                $newentitlements = $this->getLeaveEntitlementService()
                    ->getLeaveEntitlementDao()
                    ->getValidLeaveEntitlements(
                        $empNumber,
                        $leaveType,
                        $fromDate,
                        $toDate,
                        'to_date',
                        'ASC'
                    );

                // TODO Get currently assigned leave dates and add to $leaveDates
                $entitlements = [];
                $entitlementIds = [];
                foreach ($newentitlements as $entitlement) {
                    $entitlementIds[] = $entitlement->getId();
                    $entitlements[] = $entitlement;
                }

                $statuses = [
                    Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL,
                    Leave::LEAVE_STATUS_LEAVE_APPROVED
                ];

                $otherLeaveDates = $this->getLeaveEntitlementService()
                    ->getLeaveEntitlementDao()
                    ->getLinkedLeaveRequests($entitlementIds, $statuses);

                $leaveDates = $this->mergeLeaveDates($leaveDates, $otherLeaveDates);
                $numDates = count($leaveDates);

                reset($leaveDates);
                $leaveNdx = 0;
                $getNextDate = true;
                $entitlementsOk = false;

                $entitlement = array_shift($entitlements);
                $tmpArray = [];
                $skipTemp = false;

                while (!is_null($entitlement)) {
                    $availableDays = $entitlement->getAvailableDays();

                    if ($availableDays > 0) {
                        if ($getNextDate) {
                            $getNextDate = false;
                            if ($leaveNdx < $numDates) {
                                $leaveDate = $leaveDates[$leaveNdx++];
                                $leaveLength = $leaveDate->getLengthDays();
                            } else {
                                $entitlementsOk = true;
                                break;
                            }
                        }

                        if ($leaveLength <= 0) {
                            $getNextDate = true;
                            $skipTemp = false;
                        } elseif (!$entitlement->withinPeriod($leaveDate->getDate())) {
                            array_push($tmpArray, $entitlement);
                            $skipTemp = true;
                        } elseif ($leaveLength <= $availableDays) {
                            $entitlement->days_used += $leaveLength;
                            $availableDays -= $leaveLength;

                            $leaveId = $leaveDate->getId();

                            if (empty($leaveId)) {
                                if (!isset($current[$leaveDate->getDate()])) {
                                    $current[$leaveDate->getDate()] = [];
                                }
                                $current[$leaveDate->getDate()][$entitlement->id] = $leaveLength;
                            } else {
                                if (!isset($change[$leaveDate->getId()])) {
                                    $change[$leaveDate->getId()] = [];
                                }
                                $change[$leaveDate->getId()][$entitlement->id] = $leaveLength;
                            }
                            $getNextDate = true;

                            $skipTemp = false;
                            if ($leaveNdx >= $numDates) {
                                $entitlementsOk = true;
                            }

                            array_unshift($entitlements, $entitlement);
                        } else {
                            $entitlement->days_used = $entitlement->no_of_days;
                            $leaveLength -= $availableDays;

                            $leaveId = $leaveDate->getId();

                            if (empty($leaveId)) {
                                if (!isset($current[$leaveDate->getDate()])) {
                                    $current[$leaveDate->getDate()] = [];
                                }
                                $current[$leaveDate->getDate()][$entitlement->id] = $availableDays;
                            } else {
                                if (!isset($change[$leaveDate->getId()])) {
                                    $change[$leaveDate->getId()] = [];
                                }
                                $change[$leaveDate->getId()][$entitlement->id] = $availableDays;
                            }


                            $availableDays = 0;

                            $getNextDate = false;
                        }
                    }

                    if ($entitlementsOk) {
                        break;
                    }

                    if (!$skipTemp && (count($tmpArray) > 0)) {
                        $entitlement = array_shift($tmpArray);
                    } else {
                        $entitlement = array_shift($entitlements);
                    }
                }
            }
        }

        if ($allowNoEntitlements || ($entitlementsOk && (count($change) > 0 || count($current) > 0))) {
            $result = ['current' => $current, 'change' => $change];
        }

        return $result;
    }

    protected function mergeLeaveDates($leaveDates, $otherLeaveDates)
    {
        // TODO
        $result = [];

        foreach ($leaveDates as $date) {
            $key = strtotime($date->getDate());

            if (isset($result[$key])) {
                $result[$key][] = $date;
            } else {
                $result[$key] = [$date];
            }
        }

        if (is_array($otherLeaveDates)) {
            foreach ($otherLeaveDates as $date) {
                $key = strtotime($date->getDate());

                if (isset($result[$key])) {
                    $result[$key][] = $date;
                } else {
                    $result[$key] = [$date];
                }
            }
        }

        ksort($result);

        $sortedDates = [];

        foreach ($result as $oneItem) {
            foreach ($oneItem as $item) {
                $sortedDates[] = $item;
            }
        }

        return $sortedDates;
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
                    ->getValidLeaveEntitlements($empNumber, $leaveTypeId, $fromDate, $toDate, 'le.fromDate', 'ASC');

                // TODO Get currently assigned leave dates and add to $leaveDates
                $entitlements = [];
                foreach ($newEntitlements as $entitlement) {
                    $entitlements[] = $entitlement;
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

                            if (is_null($leaveId)) {
                                if (!isset($current[$leaveDate->getDate()->format('Y-m-d')])) {
                                    $current[$leaveDate->getDate()->format('Y-m-d')] = [];
                                }
                                $current[$leaveDate->getDate()->format('Y-m-d')][$entitlement->getId(
                                )] = $availableDays;
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

    public function handleLeaveCancel($leave)
    {
        // TODO
        $result = false;
        $current = [];
        $change = [];

        $entitlementArray = $this->getLeaveEntitlementService()
            ->getLeaveEntitlementDao()
            ->getEntitlementUsageForLeave($leave->id);

        if (count($entitlementArray) > 0) {
            $minDate = null;
            $maxDate = null;

            // reduce entitlement usage for this leave

            foreach ($entitlementArray as $entitlementItem) {
                $entitlementItem['days_used'] -= $entitlementItem['length_days'];
                if ($entitlementItem['days_used'] < 0) {
                    $entitlementItem['days_used'] = 0;
                }
                if (is_null($minDate)) {
                    $minDate = $entitlementItem['from_date'];
                    $maxDate = $entitlementItem['to_date'];
                } else {
                    if (strtotime($minDate) > strtotime($entitlementItem['from_date'])) {
                        $minDate = strtotime($entitlementItem['from_date']);
                    }
                    if (strtotime($maxDate) < strtotime($entitlementItem['to_date'])) {
                        $maxDate = strtotime($entitlementItem['to_date']);
                    }
                }
            }
            // Get leave without entitlements between from_date and to_date
            $leaveList = $this->getLeaveEntitlementService()
                ->getLeaveEntitlementDao()
                ->getLeaveWithoutEntitlements($leave->getEmpNumber(), $leave->getLeaveTypeId(), $minDate, $maxDate);

            // remove current leave from list
            $leaveDates = [];
            foreach ($leaveList as $leaveDateTemp) {
                if ($leaveDateTemp['id'] != $leave->getId()) {
                    $leaveDates[] = $leaveDateTemp;
                }
            }

            $entitlements = [];
            foreach ($entitlementArray as $entitlementItem) {
                $entitlement = new LeaveEntitlement();
                $entitlement->setId($entitlementItem['id']);
                $entitlement->setNoOfDays($entitlementItem['no_of_days']);
                $newDaysUsed = $entitlementItem['days_used'] - $entitlementItem['length_days'];
                if ($newDaysUsed < 0) {
                    $newDaysUsed = 0;
                }

                $entitlement->setDaysUsed($newDaysUsed);
                $entitlement->setFromDate($entitlementItem['from_date']);
                $entitlement->setToDate($entitlementItem['to_date']);

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
                $availableDays = $entitlement->getAvailableDays();
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
                            $leaveLength = $leaveDate['length_days'];
                            $getNextDate = false;
                        }
                    }

                    if ($leaveLength <= 0) {
                        $getNextDate = true;
                    } else {
                        if (!$entitlement->withinPeriod($leaveDate['date'])) {
                            if (strtotime($leaveDate['date']) < strtotime($entitlement->getFromDate())) {
                                $getNextDate = true;
                                $leaveWithoutEntitlement[] = $leaveDate;
                            } else {
                                if (strtotime($leaveDate['date']) > strtotime($entitlement->getToDate())) {
                                    $availableDays = 0;
                                }
                            }
                        } else {
                            if ($leaveLength <= $availableDays) {
                                $entitlement->days_used += $leaveLength;
                                $availableDays -= $leaveLength;

                                $leaveId = $leaveDate['id'];

                                if (empty($leaveId)) {
                                    if (!isset($current[$leaveDate['date']])) {
                                        $current[$leaveDate['date']] = [];
                                    }
                                    $current[$leaveDate['date']][$entitlement->id] = $leaveLength;
                                } else {
                                    if (!isset($change[$leaveId])) {
                                        $change[$leaveId] = [];
                                    }
                                    $change[$leaveId][$entitlement->id] = $leaveLength;
                                }
                                $getNextDate = true;
                            } else {
                                $entitlement->days_used = $entitlement->no_of_days;
                                $leaveLength -= $availableDays;

                                $leaveId = $leaveDate['id'];

                                if (empty($leaveId)) {
                                    if (!isset($current[$leaveDate['date']])) {
                                        $current[$leaveDate['date']] = [];
                                    }
                                    $current[$leaveDate['date']][$entitlement->id] = $availableDays;
                                } else {
                                    if (!isset($change[$leaveId])) {
                                        $change[$leaveId] = [];
                                    }
                                    $change[$leaveId][$entitlement->id] = $availableDays;
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
                        $availableDays = $entitlement->getAvailableDays();
                    }
                }
            }
        }


        $result = ['current' => $current, 'change' => $change];

        return $result;
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
        // TODO
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
