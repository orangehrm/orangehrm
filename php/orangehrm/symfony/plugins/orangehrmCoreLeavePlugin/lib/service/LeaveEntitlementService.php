<?php
/*
 *
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
 *
*/

/**
 * Leave Period Service
 */
class LeaveEntitlementService extends BaseService {

    private $leaveEntitlementDao;
    private $leaveRequestService;

    public function getLeaveEntitlementDao() {
        if (!($this->leaveEntitlementDao instanceof LeaveEntitlementDao)) {
            $this->leaveEntitlementDao = new LeaveEntitlementDao();
        }
        return $this->leaveEntitlementDao ;
    }

    public function setLeaveEntitlementDao(LeaveEntitlementDao $leaveEntitlementDao) {
        $this->leaveEntitlementDao	=	$leaveEntitlementDao;
    }

    public function getLeaveRequestService() {

        if (is_null($this->leaveRequestService)) {
            $this->leaveRequestService = new LeaveRequestService();
            $this->leaveRequestService->setLeaveRequestDao(new LeaveRequestDao());
        }

        return $this->leaveRequestService;

    }

    public function setLeaveRequestService(LeaveRequestService $leaveRequestService) {
        $this->leaveRequestService = $leaveRequestService;
    }

    /**
     *
     * @param int $empId
     * @param int $leaveTypeId
     * @return int
     */
    public function getEmployeeLeaveEntitlementDays( $empId, $leaveTypeId,$leavePeriodId) {

        $entitlementdays	=	0 ;

        $employeeLeaveEntitlement	=	$this->getLeaveEntitlementDao()->getEmployeeLeaveEntitlement( $empId, $leaveTypeId ,$leavePeriodId);

        if ($employeeLeaveEntitlement != null) {
            $entitlementdays = $employeeLeaveEntitlement->getNoOfDaysAllotted();
        }

        return $entitlementdays;

    }

    /**
     *
     * @param LeaveRequest $leaveRquest
     * @param int $adjustment
     * @return boolean True if the operation is successful
     */
    public function adjustEmployeeLeaveEntitlement($leave, $adjustment) {

        $leaveRquest = $leave->getLeaveRequest();

        return $this->getLeaveEntitlementDao()->adjustEmployeeLeaveEntitlement($leaveRquest->getEmployeeId(), $leaveRquest->getLeaveTypeId(), $leaveRquest->getLeavePeriodId(), $adjustment);

    }

    /**
     *
     * @param int $employeeId
     * @param String $leaveTypeId
     * @param int $leavePeriodId
     * @param int $entitlment
     * @return boolean Returns true if the operation is successfuly
     */
    public function saveEmployeeLeaveEntitlement( $employeeId, $leaveTypeId, $leavePeriodId , $entitlment,$overWrite = false) {

        $employeeLeaveEntitlement	=	$this->getLeaveEntitlementDao()->readEmployeeLeaveEntitlement($employeeId ,$leaveTypeId, $leavePeriodId);

        if($employeeLeaveEntitlement != null) {

            if($overWrite) {
                $this->getLeaveEntitlementDao()->overwriteEmployeeLeaveEntitlement($employeeId ,$leaveTypeId, $leavePeriodId, $entitlment);
            } else {
                $this->getLeaveEntitlementDao()->updateEmployeeLeaveEntitlement($employeeId ,$leaveTypeId, $leavePeriodId, $entitlment);
            }

        } else {
            $this->getLeaveEntitlementDao()->saveEmployeeLeaveEntitlement($employeeId ,$leaveTypeId, $leavePeriodId, $entitlment);
        }

        return true ;

    }

    public function readEmployeeLeaveEntitlement($employeeId, $leaveTypeId, $leavePeriodId) {

        return $this->getLeaveEntitlementDao()->readEmployeeLeaveEntitlement($employeeId, $leaveTypeId, $leavePeriodId);

    }


    /**
     * Save employee leave carried forward for given period
     * @param int $employeeId
     * @param int $leaveTypeId
     * @param int $leavePeriodId
     * @param float $carriedForwardLeaveLength
     * @return boolean
     */
    public function saveEmployeeLeaveCarriedForward( $employeeId, $leaveTypeId, $leavePeriodId, $carriedForwardLeaveLength) {

        return $this->getLeaveEntitlementDao()->saveEmployeeLeaveCarriedForward($employeeId, $leaveTypeId, $leavePeriodId, $carriedForwardLeaveLength);

    }

    /**
     * Save employee leave brought forward for given period
     * @param int $employeeId
     * @param int $leaveTypeId
     * @param int $leavePeriodId
     * @param float $broughtForwardLeaveLength
     * @return boolean
     */
    public function saveEmployeeLeaveBroughtForward( $employeeId, $leaveTypeId, $leavePeriodId, $broughtForwardLeaveLength) {

        return $this->getLeaveEntitlementDao()->saveEmployeeLeaveBroughtForward($employeeId, $leaveTypeId, $leavePeriodId, $broughtForwardLeaveLength);

    }

    public function getLeaveBalance($employeeId, $leaveTypeId, $leavePeriodId) {

        $leaveEntitlementObj = $this->
            readEmployeeLeaveEntitlement(
            $employeeId, $leaveTypeId, $leavePeriodId);

        if ($leaveEntitlementObj instanceof EmployeeLeaveEntitlement) {
            $leaveEntitled = $leaveEntitlementObj->getNoOfDaysAllotted();
            $leaveBroughtForward = $leaveEntitlementObj->getLeaveBroughtForward();
            $leaveTaken = $leaveEntitlementObj->getLeaveTaken();
            $leaveCarryForward = $leaveEntitlementObj->getLeaveCarriedForward();
        } else {
            $leaveEntitled = '0.00';
            $leaveBroughtForward = '0.00';
            $leaveTaken = '0.00';
            $leaveCarryForward = '0.00';
        }

        $leaveScheduled = $this->getLeaveRequestService()->getScheduledLeavesSum($employeeId, $leaveTypeId, $leavePeriodId);

        $leaveBalance = ($leaveEntitled + $leaveBroughtForward) - ($leaveTaken + $leaveScheduled + $leaveCarryForward);
        $leaveBalance = number_format($leaveBalance, 2);

        return $leaveBalance;

    }





}