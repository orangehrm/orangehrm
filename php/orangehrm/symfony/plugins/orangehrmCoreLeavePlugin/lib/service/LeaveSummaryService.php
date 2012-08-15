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

class LeaveSummaryService extends BaseService {

    private $leaveSummaryDao;
    private $userRoleManager;

    /**
     *
     * @return LeaveSummaryDao
     */
    public function getLeaveSummaryDao() {
        return $this->leaveSummaryDao;
    }

    /**
     *
     * @param LeaveSummaryDao $leaveSummaryDao
     * @return void
     */
    public function setLeaveSummaryDao(LeaveSummaryDao $leaveSummaryDao) {
        $this->leaveSummaryDao = $leaveSummaryDao;
    }
    
    public function getUserRoleManager() {
        
        if (is_null($this->userRoleManager)) {
            $this->userRoleManager = UserRoleManagerFactory::getUserRoleManager();
        }
        
        return $this->userRoleManager;
        
    }
    
    public function setUserRoleManager($userRoleManger) {
        
        $this->userRoleManager = $userRoleManger;
        
    }

    /**
     * @tutorial this is a performance imporoved version of fetchRawLeaveSummaryRecords method
     * @param array $clues
     * @return array 
     */
    public function fetchRawLeaveSummaryRecordsImproved($clues) {
        return $this->getLeaveSummaryDao()->fetchRawLeaveSummaryRecordsImproved($clues);        
    }
    
    /**
     * Returns an Array of leave summary records
     * 
     * @version 2.7.1
     * @param Array $clues Array of Search Clues
     * @param Int $offset Offset for Limit
     * @param Int $limit
     * @return Array List of Leave Summary Records
     */
    public function searchLeaveSummary($clues, $offset, $limit) {
        $listData = $this->getLeaveSummaryDao()->searchLeaveSummary($clues, $offset, $limit);
        
        $userRoleManager = $this->getUserRoleManager();
        $loggedInEmpNumber = $userRoleManager->getUser()->getEmpNumber();
     
        $accessibleEmployeeIds = $userRoleManager->getAccessibleEntityIds('Employee');

        foreach ($listData as $key => $row) {
            $empNumber = $row['emp_number'];
            
            if ($empNumber == $loggedInEmpNumber) {
                $selfPermissions = $userRoleManager->getDataGroupPermissions(array('leave_summary'), array(), array(), true);
                $isAccessible = $selfPermissions->canUpdate();
            } else {
                $isAccessible = in_array($empNumber, $accessibleEmployeeIds);
            }
            
            $listData[$key]['is_accessible'] = $isAccessible;
            $leave_info = explode("_", $row['leave_info']);
            $listData[$key]['having_taken'] = ($leave_info[2] != 0.00) ? true : false;
            $listData[$key]['leave_taken'] = $leave_info[2];
            $listData[$key]['having_scheduled'] = ($leave_info[1] != 0.00) ? true : false;
            $listData[$key]['leave_scheduled'] = $leave_info[1];
            $listData[$key]['leave_period_id'] ? $listData[$key]['leave_period_id'] : $clues['cmbLeavePeriod'];
            $listData[$key]['logged_user_id'] = $clues['loggedUserId'];
            $listData[$key]['leave_type_status'] = $listData[$key]['available_flag'] ? true : false;
        }
        
        return $listData;
    }
    
    /**
     * Return Count of Total Leave Summary Records
     * 
     * @version 2.7.1
     * @param Array $clues Array of Search Clues
     * @return Int Count of Total Leave Summary Records
     */
    public function searchLeaveSummaryCount($clues) {
        return $this->getLeaveSummaryDao()->searchLeaveSummaryCount($clues);
    }
    
}