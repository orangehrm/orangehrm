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
class LeaveSummaryDao extends BaseDao {

    private static $doneSyncingLeaveEntitlements = false;

    public function __construct() {
    }
    
    /**
     * @tutorial this is a performance imporoved version of fetchRawLeaveSummaryRecords method
     * @param array $clues
     * @return array 
     */
    public function fetchRawLeaveSummaryRecordsImproved($clues) {
       $q = "SELECT a.emp_lastname, a.emp_middle_name, a.emp_firstname, b.leave_type_name, q.no_of_days_allotted, q.leave_brought_forward,
            q.leave_carried_forward,
            (SELECT CONCAT (q.no_of_days_allotted + q.leave_brought_forward - (SUM(l.leave_length_days) + q.leave_carried_forward) ,'_',  sum(IF(l.leave_status = ".Leave::LEAVE_STATUS_LEAVE_APPROVED.", leave_length_days, 0)),'_',  sum(IF(l.leave_status = ".Leave::LEAVE_STATUS_LEAVE_TAKEN.", leave_length_days, 0)))
             FROM hs_hr_leave_requests lr 
             LEFT JOIN hs_hr_leave l ON lr.leave_request_id = l.leave_request_id 
             WHERE lr.leave_type_id = b.leave_type_id AND lr.employee_id = a.emp_number AND lr.leave_period_id = q.leave_period_id 
             AND leave_status IN (".Leave::LEAVE_STATUS_LEAVE_APPROVED.",".Leave::LEAVE_STATUS_LEAVE_TAKEN.")) AS leave_info 
       
             FROM
             (hs_hr_employee a, hs_hr_leavetype b) 
             LEFT JOIN hs_hr_employee_leave_quota q ON a.emp_number = q.employee_id AND b.leave_type_id = q.leave_type_id";
        
        $q .= " ";        
           
        
        if (!empty($clues['cmbLocation'])) {
            $q .= " LEFT JOIN hs_hr_emp_locations c ON a.emp_number = c.emp_number";
        }
        
        $where = array();
        $where [] = " b.available_flag = 1 ";
        
        
        if (!empty($clues['txtEmpName'])) {
            // Replace multiple spaces in string with wildcards
            $clues['txtEmpName'] = str_replace(' (' . __('Past Employee') . ')', '', $clues['txtEmpName']);
            $value = preg_replace('!\s+!', '%', $clues['txtEmpName']);
            $employeeName = '\'%' . $value . '%\'';
            $where[] = "CONCAT_WS('', a.emp_firstname, a.emp_middle_name, a.emp_lastname) LIKE $employeeName";
        }
        
        if ($clues['userType'] == 'Supervisor') {
            $where[] = "a.emp_number IN(".implode(",", $clues['subordinates']).")";
        }

        if ($clues['userType'] == 'ESS') {
            $where[] = "a.emp_number = '{$clues['cmbEmpId']}'";
        }

        if (!empty($clues['emp_numbers'])) {
            $where[] = "a.emp_number IN(".implode(",", $clues['emp_numbers']).")";
        }
       
        if (!empty($clues['cmbLeaveType'])) {
            $where[] = "b.leave_type_id = '{$clues['cmbLeaveType']}'";
        }

        if (!empty($clues['cmbSubDivision'])) {
            $where[] = "a.work_station IN ({$clues['cmbSubDivision']})";
        }

        if (!empty($clues['cmbJobTitle'])) {
            $where[] = "a.job_title_code = '{$clues['cmbJobTitle']}'";
        }

        if (!empty($clues['cmbLocation'])) {
            $where[] = "c.location_id = '{$clues['cmbLocation']}'";
        }

        if (!empty($clues['cmbLeavePeriod'])) {
            $where[] = "q.leave_period_id = '{$clues['cmbLeavePeriod']}'";
        }

        if(empty($clues['cmbWithTerminated'])) {
            $where[] = "(a.termination_id IS NULL)";           
        }
       
        if(count($where) > 0) {
            $q .= ' WHERE '.implode(' AND ',$where);
        }
        
        $q .= " ORDER By a.emp_number, b.leave_type_id"; 
        $pdo = Doctrine_Manager::connection()->getDbh();      
        return $pdo->query($q)->fetchAll();         

    }
    
    /**
     * SearchLeaveSummary
     * Search leave summary records
     * The common query is taken from getLeaveSummarySearchQuery function. The most outer query is added to access
     * the leave_info records returned by the inner query. 
     * @param Array $clues
     * @param Int $offset
     * @param Int $limit
     */
    public function searchLeaveSummary($clues, $offset=0, $limit=20) {
        
        try {
            
            $q = "SELECT * 
            	FROM ( ";
            
            $q .= $this->getLeaveSummarySearchQuery($clues);
                        
            $q .= " LIMIT $offset,$limit";
    
            $pdo = Doctrine_Manager::connection()->getDbh();   
            return $pdo->query($q)->fetchAll();
            
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }

    }
    
    /**
     * SearchLeaveSummaryCount
     * Get total Search leave summary record count without considering a limit
     * The common query is taken from getLeaveSummarySearchQuery function. The most outer query is added to access
     * the leave_info records returned by the inner query. 
     * @param Array $clues
     */
	public function searchLeaveSummaryCount($clues) {
	    
	    try {
	        
            $q = "SELECT COUNT(emp_number) 
            	FROM ( ";
    	    
            $q .= $this->getLeaveSummarySearchQuery($clues);
    
            $pdo = Doctrine_Manager::connection()->getDbh();
            $res = $pdo->query($q);
            
            $row = $res->fetch();
    
            return $row[0];
            
	    } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        
    }
    
    /**
     * getLeaveSummarySearchQuery
     * Build the select clause and the where clause of the LeaveSummarySearch function. Outer query is getting the cartesian product 
     * of the employees with leave types. For each record there is an inner query runnning. The inner query returns
     * the concatenated values of the leave balance, leave taken and leave scheduled. Deleted employees are handle by checking whether there 
     * are values for no_of_days_allotted or in the last 10 digits of the leave_info. If there are values for 
     * last 10 digits, that means there are leave taken or leave scheduled for that deleted employee.
     * @param Array $clues
     * @return String $query
     */
    protected function getLeaveSummarySearchQuery($clues) {
        $q = "
 				SELECT 
 					CONCAT_WS(' ',TRIM(CONCAT_WS(' ',a.emp_firstname, a.emp_middle_name)), a.emp_lastname)  as emp_fullname, 
 					b.leave_type_id , 
 					b.available_flag, 
 					q.leave_period_id, 
 					a.employee_id, 
 					a.emp_number, 
 					a.termination_id,
 					b.leave_type_name, 
 					IFNULL(q.no_of_days_allotted,0) as no_of_days_allotted , 
 					q.leave_brought_forward,
            		q.leave_carried_forward,
            		(
            			SELECT 
            				CONCAT (
            					IFNULL(
            						IFNULL(q.no_of_days_allotted,0) + 
            						IFNULL(q.leave_brought_forward,0) - 
            						(
            							IFNULL(SUM(l.leave_length_days),0) + 
            							IFNULL(q.leave_carried_forward,0)
            						),0) ,'_',  
            					IFNULL(sum(IF(l.leave_status = ".Leave::LEAVE_STATUS_LEAVE_APPROVED.", leave_length_days, 0)),0),'_',  
            					IFNULL(sum(IF(l.leave_status = ".Leave::LEAVE_STATUS_LEAVE_TAKEN.", leave_length_days, 0)),0))
             			FROM hs_hr_leave_requests lr 
             			LEFT JOIN hs_hr_leave l 
             				ON lr.leave_request_id = l.leave_request_id 
            			WHERE lr.leave_type_id = b.leave_type_id 
            				AND	lr.employee_id = a.emp_number AND lr.leave_period_id = '{$clues['cmbLeavePeriod']}'
             				AND leave_status IN (".Leave::LEAVE_STATUS_LEAVE_APPROVED.",".Leave::LEAVE_STATUS_LEAVE_TAKEN.")
             		) AS leave_info 
             	FROM (hs_hr_employee a, hs_hr_leavetype b) 
             	LEFT JOIN hs_hr_employee_leave_quota q 
             		ON a.emp_number = q.employee_id 
             		AND b.leave_type_id = q.leave_type_id 
             		AND q.leave_period_id = '{$clues['cmbLeavePeriod']}'";
        
        $q .= " ";
           
        
        if (!empty($clues['cmbLocation'])) {
            $q .= " LEFT JOIN hs_hr_emp_locations c ON a.emp_number = c.emp_number";
        }
        
        $where = array();
        
        
        if (!empty($clues['txtEmpName'])) {
            // Replace multiple spaces in string with wildcards
            $clues['txtEmpName'] = str_replace(' (' . __('Past Employee') . ')', '', $clues['txtEmpName']);
            $value = preg_replace('!\s+!', '%', $clues['txtEmpName']);
            $employeeName = '\'%' . $value . '%\'';
            $where[] = "CONCAT_WS('', a.emp_firstname, a.emp_middle_name, a.emp_lastname) LIKE $employeeName";
        }
        
        if ($clues['userType'] == 'Supervisor') {
            $where[] = "a.emp_number IN(".implode(",", $clues['subordinates']).")";
        }

        if ($clues['userType'] == 'ESS') {
            $where[] = "a.emp_number = '{$clues['cmbEmpId']}'";
        }

        if (!empty($clues['emp_numbers'])) {
            $where[] = "a.emp_number IN(".implode(",", $clues['emp_numbers']).")";
        }
       
        if (!empty($clues['cmbLeaveType'])) {
            $where[] = "b.leave_type_id = '{$clues['cmbLeaveType']}'";
        }

        if (!empty($clues['cmbSubDivision'])) {
            $where[] = "a.work_station IN ({$clues['cmbSubDivision']})";
        }

        if (!empty($clues['cmbJobTitle'])) {
            $where[] = "a.job_title_code = '{$clues['cmbJobTitle']}'";
        }

        if (!empty($clues['cmbLocation'])) {
            $where[] = "c.location_id IN ({$clues['cmbLocation']})";
        }

        if(empty($clues['cmbWithTerminated'])) {
            $where[] = "(a.termination_id IS NULL)";           
        }
       
        if(count($where) > 0) {
            $q .= ' WHERE '.implode(' AND ',$where);
        }
        $q .= " ORDER By a.emp_lastname, b.leave_type_id";
        
        $q .= ") as leave_details
        			WHERE   ((available_flag = 1) OR ((RIGHT(leave_info , 10) != '_0.00_0.00') OR no_of_days_allotted != '0.00')) ";
        
        return $q;
    }


}
