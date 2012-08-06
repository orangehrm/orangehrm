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
class LeaveEntitlementDao extends BaseDao{
	
	/**
	 * Get Employee Leave Entitlement
	 * @param $empId
	 * @param $leaveTypeId
	 * @return EmployeeLeaveEntitlement
	 */
	public function getEmployeeLeaveEntitlement( $empId, $leaveTypeId,$leavePeriodId){
		try {
				$q = Doctrine_Query::create()
				    ->from('EmployeeLeaveEntitlement ele')
                    ->where('ele.employee_id = ?',$empId)
                    ->andWhere('ele.leave_type_id = ?',$leaveTypeId)
                    ->andWhere('ele.leave_period_id = ?',$leavePeriodId);

			   $employeeLeaveEntitlement = $q->fetchOne();
			   
			   return $employeeLeaveEntitlement;
			   
		} catch( Exception $e) {
            throw new DaoException( $e->getMessage());
        }
	}
	
	/**
	 * 
	 * @param int $employeeId
	 * @param string $leaveTypeId
	 * @param int $leavePeriodId
	 * @param int $adjustment Number of days to adjust as integer (eg: 1, 5, -1, -7, etc) 
	 * @return boolean Returns true if the operation is successfult
	 */
	public function adjustEmployeeLeaveEntitlement($employeeId, $leaveTypeId, $leavePeriodId, $adjustment) {
		$q = Doctrine_Query::create()
			->update('EmployeeLeaveEntitlement le')
			->set('le.leave_taken', "(le.leave_taken + ({$adjustment}))")
			->where('le.leave_type_id = ?', $leaveTypeId)
			->andWhere('le.leave_period_id = ?', $leavePeriodId)
			->andWhere('le.employee_id = ?', $employeeId);
		return $q->execute();
	}
	
	/**
	 * 
	 * @param int $employeeId
	 * @param string $leaveTypeId
	 * @param int $leavePeriodId
	 * @param int $adjustment Number of days to adjust as integer (eg: 1, 5, -1, -7, etc) 
	 * @return boolean Returns true if the operation is successfult
	 */
	public function overwriteEmployeeLeaveEntitlement($employeeId, $leaveTypeId, $leavePeriodId, $adjustment) {
		try {

            if ($this->_isValidAdjustment($adjustment)) {

                $q = Doctrine_Query::create()
                    ->update('EmployeeLeaveEntitlement le')
                    ->set('le.no_of_days_allotted', "({$adjustment})")
                    ->where('le.leave_type_id = ?', $leaveTypeId)
                    ->andWhere('le.leave_period_id = ?', $leavePeriodId)
                    ->andWhere('le.employee_id = ?', $employeeId);
                $q->execute();

            }
			
			return true ;
		} catch( Exception $e) {
            throw new DaoException( $e->getMessage());
        }
	}

    private function _isValidAdjustment($adjustment) {

        if (!is_numeric($adjustment)) {
            return false;
        } elseif ($adjustment > 365) {
            return false;
        }

        return true;

    }
	
	/**
	 * 
	 * @param int $employeeId
	 * @param string $leaveTypeId
	 * @param int $leavePeriodId
	 * @param int $adjustment Number of days to adjust as integer (eg: 1, 5, -1, -7, etc) 
	 * @return boolean Returns true if the operation is successfult
	 */
	public function updateEmployeeLeaveEntitlement($employeeId, $leaveTypeId, $leavePeriodId, $adjustment) {
		try {
			$q = Doctrine_Query::create()
				->update('EmployeeLeaveEntitlement le')
				->set('le.no_of_days_allotted',"(le.no_of_days_allotted + ({$adjustment}))")
				->where('le.leave_type_id = ?', $leaveTypeId)
				->andWhere('le.leave_period_id = ?', $leavePeriodId)
				->andWhere('le.employee_id = ?', $employeeId);
			$q->execute();
			
			return true ;
		} catch( Exception $e) {
            throw new DaoException( $e->getMessage());
        }
	}
	
	/**
	 * 
	 * @param int $employeeId
	 * @param String $leaveTypeId
	 * @param int $leavePeriodId
	 * @param int $entitlment
	 * @return boolean Returns true if the operation is successfuly
	 */
	public function saveEmployeeLeaveEntitlement( $employeeId, $leaveTypeId, $leavePeriodId, $entitlment){
		try {
			$employeeLeaveEntitlement	=	new EmployeeLeaveEntitlement();
			$employeeLeaveEntitlement->setLeaveTypeId($leaveTypeId);
			$employeeLeaveEntitlement->setEmployeeId($employeeId);
			$employeeLeaveEntitlement->setLeavePeriodId($leavePeriodId);
			$employeeLeaveEntitlement->setNoOfDaysAllotted($entitlment);
			
			$employeeLeaveEntitlement->save();
			  
			return true ;
		} catch( Exception $e) {
            throw new DaoException( $e->getMessage());
        }
	}
	
	/**
	 * Read Leave Entitlment
	 * @param int $employeeId
	 * @param String $leaveTypeId
	 * @param int $leavePeriodId
	 * @return boolean Returns true if the operation is successfuly
	 */
	public function readEmployeeLeaveEntitlement( $employeeId, $leaveTypeId, $leavePeriodId){
		try {
			
			$q = Doctrine_Query::create()
			->from('EmployeeLeaveEntitlement ele')
			->where("ele.leave_type_id = '$leaveTypeId'")
			->andWhere("ele.employee_id = '$employeeId'")
			->andWhere("ele.leave_period_id = '$leavePeriodId'");
			
			$record = $q->fetchOne();
			return $record;
			   
		} catch( Exception $e) {
            throw new DaoException( $e->getMessage());
        }
	}

    /**
     * searchEmployeeLeaveEntitlement
     * Search Leave Entitlment by employeeId, leaveTypeId and leave period id
     * @param Array $employeeIdArray
     * @param Array $leaveTypeIdArray
     * @param int $leavePeriodId
     * @param int $recordCount
     * @return Doctrine_Collection EmployeeLeaveEntitlement
     */
    public function searchEmployeeLeaveEntitlement($employeeIdArray, $leaveTypeIdArray, $leavePeriodId, $recordCount) {
        try {
            $q = Doctrine_Query::create()
            ->from('EmployeeLeaveEntitlement ele')
            ->where("ele.leave_period_id = ?");
    
            $whereClause = "";
            $escapeArray = array();
            $escapeArray[] = $leavePeriodId;
            for ($i = 0; $i < $recordCount; $i++) {
                $whereClause .= "( (ele.leave_type_id = ?) AND (ele.employee_id = ? ) )";
                $escapeArray[] = $leaveTypeIdArray[$i];
                $escapeArray[] = $employeeIdArray[$i];
                if (($whereClause != "") && ($i != ($recordCount - 1))) {
                    $whereClause .= " OR ";
                }
            }
            
            $q->andWhere($whereClause);
            return $q->execute($escapeArray);
    
        } catch( Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }
    
    /**
     * SaveEmployeeLeaveEntitlementCollection
     * Save EmployeeLeaveEntitlement Collection
     * @param Array $employeeLeaveEntitlements
     * @return Doctrine_Collection EmployeeLeaveEntitlement
     */
    public function saveEmployeeLeaveEntitlementCollection($employeeLeaveEntitlements){
        try {
            $employeeLeaveEntitlementCollection = new Doctrine_Collection('EmployeeLeaveEntitlement');
            foreach ($employeeLeaveEntitlements as $employeeLeaveEntitlement) {
                $employeeLeaveEntitlementCollection->add($employeeLeaveEntitlement);
            }
            $employeeLeaveEntitlementCollection->save();
            return $employeeLeaveEntitlementCollection;
        } catch( Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

           /**
	 * Save employee leave carried forward for given period
	 * @param int $employeeId
         * @param int $leaveTypeId
         * @param int $leavePeriodId
         * @param float $carriedForwardLeaveLength
	 * @return boolean
	 */
	public function saveEmployeeLeaveCarriedForward( $employeeId, $leaveTypeId, $leavePeriodId, $carriedForwardLeaveLength){
            try {

                $leaveEntitlement = $this->readEmployeeLeaveEntitlement($employeeId, $leaveTypeId, $leavePeriodId);
                if(!$leaveEntitlement instanceof EmployeeLeaveEntitlement){
                    $leaveEntitlement = new EmployeeLeaveEntitlement();
                    $leaveEntitlement->setLeaveTypeId($leaveTypeId);
                    $leaveEntitlement->setLeavePeriodId($leavePeriodId);
                    $leaveEntitlement->setEmployeeId($employeeId);

                }
                $leaveEntitlement->setLeaveCarriedForward($carriedForwardLeaveLength);
                $leaveEntitlement->save();
                return true ;

            } catch( Exception $e) {
                throw new DaoException( $e->getMessage());
            }
	}

        /**
	 * Save employee leave brought forward for given period
	 * @param int $employeeId
         * @param int $leaveTypeId
         * @param int $leavePeriodId
         * @param float $broughtForwardLeaveLength
	 * @return boolean
	 */
	public function saveEmployeeLeaveBroughtForward( $employeeId, $leaveTypeId, $leavePeriodId, $broughtForwardLeaveLength){
            try {

                $leaveEntitlement = $this->readEmployeeLeaveEntitlement($employeeId, $leaveTypeId, $leavePeriodId);
                
                if(!$leaveEntitlement instanceof EmployeeLeaveEntitlement){
                   $leaveEntitlement = new EmployeeLeaveEntitlement();
                   $leaveEntitlement->setLeaveTypeId($leaveTypeId);
                   $leaveEntitlement->setLeavePeriodId($leavePeriodId);
                   $leaveEntitlement->setEmployeeId($employeeId);
                   $leaveEntitlement->setNoOfDaysAllotted(0);
                }
                
                $leaveEntitlement->setLeaveBroughtForward($broughtForwardLeaveLength);
                $leaveEntitlement->save();
                
                return true ;

            } catch( Exception $e) {
                throw new DaoException( $e->getMessage());
            }
	}
    
    /**
     * Get Leave Balance 
     * 
     * @version 2.7.1
     * @param type $employeeId
     * @param type $leaveTypeId
     * @param type $leavePeriodId
     * @return Leave Balance
     */
    public function getLeaveBalance($empNumber, $leaveTypeId, $leavePeriodId) {

        $query = 'SELECT IFNULL(q.no_of_days_allotted,0) + IFNULL(q.leave_brought_forward,0) 
                  - (IFNULL(sum(IF(l.leave_status = ' .Leave::LEAVE_STATUS_LEAVE_APPROVED. ', leave_length_days, 0)),0) + 
                     IFNULL(sum(IF(l.leave_status = '.Leave::LEAVE_STATUS_LEAVE_TAKEN.', leave_length_days, 0)),0) +
                     IFNULL(q.leave_carried_forward,0)) as balance
            
                FROM hs_hr_employee a 
             	LEFT JOIN hs_hr_employee_leave_quota q 
             		ON a.emp_number = q.employee_id              		
            		AND q.leave_period_id = :leave_period_id
                    AND q.leave_type_id = :leave_type_id
                LEFT JOIN hs_hr_leave_requests lr 
                    ON a.emp_number = lr.employee_id
                    AND lr.leave_period_id = :leave_period_id
                    AND lr.leave_type_id = :leave_type_id                    
                LEFT JOIN hs_hr_leave l 
             		ON lr.leave_request_id = l.leave_request_id AND leave_status IN ('.Leave::LEAVE_STATUS_LEAVE_APPROVED.','.Leave::LEAVE_STATUS_LEAVE_TAKEN.') 
                WHERE a.emp_number = :emp_number         
                GROUP BY a.emp_number';                
                        
        $pdo = Doctrine_Manager::connection()->getDbh(); 
        $statement = $pdo->prepare($query);
        
        $statement->execute(array(':emp_number' => $empNumber, 
           ':leave_type_id' => $leaveTypeId, ':leave_period_id' => $leavePeriodId));

        $balance = $statement->fetchColumn();
        
        if ($balance) {
            return $balance;
        }
        
        return 0;
      
    }    

}

