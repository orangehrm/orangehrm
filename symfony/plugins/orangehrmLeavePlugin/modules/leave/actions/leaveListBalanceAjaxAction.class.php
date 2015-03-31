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
 * Description of leaveListBalanceAjaxAction
 */
class leaveListBalanceAjaxAction extends sfAction {
    
    public function execute($request) {
        sfConfig::set('sf_web_debug', false);
        sfConfig::set('sf_debug', false);
        
        $postData = $request->getParameter('data');        

        $balances = $this->getLeaveBalances($postData);

        $response = $this->getResponse();
        $response->setHttpHeader('Expires', '0');
        $response->setHttpHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0, max-age=0");
        $response->setHttpHeader("Cache-Control", "private", false);

        
        return $this->renderText(json_encode($balances)); 
               
    }
    
    function getLeaveBalances($postData) {
        $count = count($postData);
        
        $data = array();

            
        $leaveEntitlementService = new LeaveEntitlementService();
        $leaveStrategy = $leaveEntitlementService->getLeaveEntitlementStrategy();

        for ($i = 0; $i < $count; $i++) {
            $empNumber = $postData[$i][0];
            $leaveTypeId = $postData[$i][1];
            $startDate = $postData[$i][2];
            $endDate = $postData[$i][3];

            if ($startDate == $endDate) {
                $leaveBalance = $leaveEntitlementService->getLeaveBalance($empNumber, $leaveTypeId, $startDate);
            } else {

                $leavePeriodForStartDate = $leaveStrategy->getLeavePeriod($startDate, $empNumber, $leaveTypeId);
                $leavePeriodForEndDate = $leaveStrategy->getLeavePeriod($endDate, $empNumber, $leaveTypeId);

                if (($leavePeriodForStartDate[0] == $leavePeriodForEndDate[0]) && 
                        ($leavePeriodForStartDate[1] == $leavePeriodForEndDate[1])) {
                    $leaveBalance = $leaveEntitlementService->getLeaveBalance($empNumber, $leaveTypeId, $startDate);
                } else {
                    $startPeriodBalance = $leaveEntitlementService->getLeaveBalance($empNumber, $leaveTypeId, $startDate);
                    $endPeriodBalance = $leaveEntitlementService->getLeaveBalance($empNumber, $leaveTypeId, $endDate);

                    $leaveBalance = array(
                        array('start' => set_datepicker_date_format($leavePeriodForStartDate[0]), 
                              'end' => set_datepicker_date_format($leavePeriodForStartDate[1]), 
                              'balance' => $startPeriodBalance->getBalance()),
                        array('start' => set_datepicker_date_format($leavePeriodForEndDate[0]), 
                              'end' => set_datepicker_date_format($leavePeriodForEndDate[1]), 
                              'balance' => $endPeriodBalance->getBalance())
                    );
                }                     
            }
            
            if ($leaveBalance instanceof LeaveBalance) {
                $data[] = number_format($leaveBalance->getBalance(), 2);
            } else {
                $data[] = $leaveBalance;                
            }
        }
        
        return $data;
    }
}
