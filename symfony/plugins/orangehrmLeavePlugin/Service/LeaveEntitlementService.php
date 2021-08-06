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

namespace OrangeHRM\Leave\Service;

use DateTime;
use OrangeHRM\Core\Exception\ServiceException;
use OrangeHRM\Core\Traits\ClassHelperTrait;
use OrangeHRM\Entity\LeaveEntitlement;
use OrangeHRM\Leave\Dao\LeaveEntitlementDao;
use OrangeHRM\Leave\Entitlement\EntitlementConsumptionStrategy;
use OrangeHRM\Leave\Entitlement\FIFOEntitlementConsumptionStrategy;
use OrangeHRM\Leave\Entitlement\LeaveBalance;
use OrangeHRM\Leave\Traits\Service\LeaveConfigServiceTrait;

class LeaveEntitlementService {
    use LeaveConfigServiceTrait;
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
    public function getLeaveEntitlementStrategy():EntitlementConsumptionStrategy {
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
    public function getLeaveEntitlementDao():LeaveEntitlementDao {
        if (!($this->leaveEntitlementDao instanceof LeaveEntitlementDao)) {
            $this->leaveEntitlementDao = new LeaveEntitlementDao();
        }
        return $this->leaveEntitlementDao;
    }
    
    public function searchLeaveEntitlements(LeaveEntitlementSearchParameterHolder $searchParameters) {
        // TODO
        return $this->getLeaveEntitlementDao()->searchLeaveEntitlements($searchParameters);
    }
    
    public function deleteLeaveEntitlements($ids) {
        // TODO
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
        // TODO
        return $this->getLeaveEntitlementDao()->getLeaveEntitlement($id);
    }    
    
    public function bulkAssignLeaveEntitlements($employeeNumbers, LeaveEntitlement $leaveEntitlement) {
        // TODO
        return $this->getLeaveEntitlementDao()->bulkAssignLeaveEntitlements($employeeNumbers, $leaveEntitlement);
    }
    
    public function getAvailableEntitlements(LeaveParameterObject $leaveParameterObject) {
        // TODO
        return $this->getLeaveEntitlementStrategy()->getAvailableEntitlements($leaveParameterObject);
    }
    
//    public function getValidLeaveEntitlements(int $empNumber, int $leaveTypeId, \DateTime $fromDate, \DateTime $toDate, string $orderField, string $order) {
//        // TODO
//        return $this->getLeaveEntitlementDao()->getValidLeaveEntitlements($empNumber, $leaveTypeId, $fromDate, $toDate, $orderField, $order);
//    }
    
    public function getLinkedLeaveRequests($entitlementIds, $statuses) {
        // TODO
        return $this->getLeaveEntitlementDao()->getLinkedLeaveRequests($entitlementIds, $statuses);
    }

    /**
     * @param int $empNumber
     * @param int $leaveTypeId
     * @param DateTime|null $asAtDate
     * @param DateTime|null $date
     * @return LeaveBalance
     * @throws ServiceException
     */
    public function getLeaveBalance(
        int $empNumber,
        int $leaveTypeId,
        ?DateTime $asAtDate = null,
        ?DateTime $date = null
    ): LeaveBalance {
        if (is_null($asAtDate)) {
            $asAtDate = new DateTime();
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
    
    public function getEntitlementUsageForLeave($leaveId) {
        // TODO
        return $this->getLeaveEntitlementDao()->getEntitlementUsageForLeave($leaveId);
    }
    
    public function getLeaveWithoutEntitlements($empNumber, $leaveTypeId, $fromDate, $toDate) {
        // TODO
        return $this->getLeaveEntitlementDao()->getLeaveWithoutEntitlements($empNumber, $leaveTypeId, $fromDate, $toDate);
    }
    
    public function getMatchingEntitlements($empNumber, $leaveTypeId, $fromDate, $toDate) {
        // TODO
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
        // TODO
        return $this->getLeaveEntitlementDao()->getLeaveEntitlementTypeList($orderField, $orderBy);
    }    
}
