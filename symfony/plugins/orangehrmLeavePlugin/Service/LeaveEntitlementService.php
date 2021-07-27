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
 *
 */

/**
 * LeaveEntitlement service
 */
class LeaveEntitlementService extends BaseService {

    protected $leaveConfigService;
    protected $leaveEntitlementDao;
    protected $leaveEntitlementStrategy;    
    protected $leavePeriodService;

    /**
     * Returns Leave Period
     * @return LeavePeriodService
     */
    public function getLeavePeriodService() {

        if (is_null($this->leavePeriodService)) {
            $leavePeriodService = new LeavePeriodService();
            $leavePeriodService->setLeavePeriodDao(new LeavePeriodDao());
            $this->leavePeriodService = $leavePeriodService;
        }

        return $this->leavePeriodService;
    }
    
    /**
     * Set Leave Period
     */
    public function setLeavePeriodService($leavePeriodService) {
        $this->leavePeriodService = $leavePeriodService;
    }    
    
    public function getLeaveEntitlementStrategy() {
        if (!isset($this->leaveEntitlementStrategy)) {
            
            $strategyClass = $this->getLeaveConfigService()->getLeaveEntitlementConsumptionStrategy();            
            $this->leaveEntitlementStrategy = new $strategyClass;
        }
        
        return $this->leaveEntitlementStrategy;
    }
    
    public function setLeaveEntitlementStrategy($leaveEntitlementStrategy) {
        $this->leaveEntitlementStrategy = $leaveEntitlementStrategy;
    }
    
    public function getLeaveConfigService() {
        if (!($this->leaveConfigService instanceof LeaveConfigurationService)) {
            $this->leaveConfigService = new LeaveConfigurationService();
        }        
        return $this->leaveConfigService;
    }

    public function setLeaveConfigService($leaveConfigService) {
        $this->leaveConfigService = $leaveConfigService;
    }

    
    public function getLeaveEntitlementDao() {
        if (!($this->leaveEntitlementDao instanceof LeaveEntitlementDao)) {
            $this->leaveEntitlementDao = new LeaveEntitlementDao();
        }
        return $this->leaveEntitlementDao;
    }

    public function setLeaveEntitlementDao(LeaveEntitlementDao $leaveEntitlementDao) {
        $this->leaveEntitlementDao = $leaveEntitlementDao;
    }
    
    public function searchLeaveEntitlements(LeaveEntitlementSearchParameterHolder $searchParameters) {
        return $this->getLeaveEntitlementDao()->searchLeaveEntitlements($searchParameters);
    }
    
    public function saveLeaveEntitlement(LeaveEntitlement $leaveEntitlement) {

        return $this->getLeaveEntitlementDao()->saveLeaveEntitlement($leaveEntitlement);
    }
    
    /**
     * Save Leave Adjustment and linked to relevent leave entitlement 
     * 
     * @param LeaveAdjustment $leaveAdjustment
     * @return type
     * @throws DaoException
     */
    public function saveLeaveAdjustment( LeaveAdjustment $leaveAdjustment){
        return $this->getLeaveEntitlementDao()->saveLeaveAdjustment($leaveAdjustment);
    }
    
    public function deleteLeaveEntitlements($ids) {
        
        $deleted = 0;
        
        $allDeleted = true;
        $avaliableToDeleteIds = array();
        $leaveEntitlementSearchParameterHolder = new LeaveEntitlementSearchParameterHolder();
        $leaveEntitlementSearchParameterHolder->setIdList($ids);
        
        $entitlementList = $this->searchLeaveEntitlements( $leaveEntitlementSearchParameterHolder );
        foreach( $entitlementList as $entitlement){
            if( $entitlement->getDaysUsed() > 0){
                $allDeleted = false;
            }else{
                $avaliableToDeleteIds[] = $entitlement->getId();
            }
        }
        if(count($avaliableToDeleteIds) > 0){
            $deleted = $this->getLeaveEntitlementDao()->deleteLeaveEntitlements($avaliableToDeleteIds);
        }
        
        if(!$allDeleted){
            throw new Exception("Entitlement/s will not be deleted since it's already in use");
        }
        
        return $deleted;
            
    }    
    
    public function getLeaveEntitlement($id) {
        return $this->getLeaveEntitlementDao()->getLeaveEntitlement($id);
    }    
    
    public function bulkAssignLeaveEntitlements($employeeNumbers, LeaveEntitlement $leaveEntitlement) {
        return $this->getLeaveEntitlementDao()->bulkAssignLeaveEntitlements($employeeNumbers, $leaveEntitlement);
    }
    
    public function getAvailableEntitlements(LeaveParameterObject $leaveParameterObject) {
        return $this->getLeaveEntitlementStrategy()->getAvailableEntitlements($leaveParameterObject);
    }
    
    public function getValidLeaveEntitlements($empNumber, $leaveTypeId, $fromDate, $toDate, $orderField, $order) {
        return $this->getLeaveEntitlementDao()->getValidLeaveEntitlements($empNumber, $leaveTypeId, $fromDate, $toDate, $orderField, $order);
    }
    
    public function getLinkedLeaveRequests($entitlementIds, $statuses) {
        return $this->getLeaveEntitlementDao()->getLinkedLeaveRequests($entitlementIds, $statuses);
    }    
    
    public function getLeaveBalance($empNumber, $leaveTypeId, $asAtDate = NULL, $date = NULL) {
        if (empty($asAtDate)) {
            $asAtDate = date('Y-m-d', time());
        }
        
        // If end date is not defined, and leave period is forced, use end date of current leave period
        // as the end date for leave balance calculation
        if (empty($date)) {
            $leavePeriodStatus = LeavePeriodService::getLeavePeriodStatus();
            if ($leavePeriodStatus == LeavePeriodService::LEAVE_PERIOD_STATUS_FORCED) {
                $leavePeriod = $this->getLeaveEntitlementStrategy()->getLeavePeriod($asAtDate, $empNumber, $leaveTypeId);
                
                if (is_array($leavePeriod) && isset($leavePeriod[1])) {
                    $date = $leavePeriod[1];
                }
            }
        }

        return $this->getLeaveEntitlementDao()->getLeaveBalance($empNumber, $leaveTypeId, $asAtDate, $date);
    }
    
    public function getEntitlementUsageForLeave($leaveId) {
        return $this->getLeaveEntitlementDao()->getEntitlementUsageForLeave($leaveId);
    }
    
    public function getLeaveWithoutEntitlements($empNumber, $leaveTypeId, $fromDate, $toDate) {
        return $this->getLeaveEntitlementDao()->getLeaveWithoutEntitlements($empNumber, $leaveTypeId, $fromDate, $toDate);
    }
    
    public function getMatchingEntitlements($empNumber, $leaveTypeId, $fromDate, $toDate) {
        return $this->getLeaveEntitlementDao()->getMatchingEntitlements($empNumber, $leaveTypeId, $fromDate, $toDate);
    }

    /**
     * Get List of LeaveEntitlementTypes
     * 
     * @param string $orderField field to order by
     * @param string $orderBy order (ASC/DESC)
     * @return Collection of LeaveEntitlementType
     * @throws DaoException on an error
     */    
    public function getLeaveEntitlementTypeList($orderField = 'name', $orderBy = 'ASC') {
        return $this->getLeaveEntitlementDao()->getLeaveEntitlementTypeList($orderField, $orderBy);
    }    
}
