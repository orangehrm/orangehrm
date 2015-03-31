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
 * Description of getFilteredEmployeeCountAjaxAction
 */
class getEmployeeEntitlementAjaxAction  extends sfAction {
    
    
    protected $entitlementService ;
    
    
    
    public function getEntitlementService() { 
        if (empty($this->entitlementService)) {
            $this->entitlementService = new LeaveEntitlementService();
        }
        return $this->entitlementService;
    }

    public function setEntitlementService($entitlementService) {
        $this->entitlementService = $entitlementService;
    }
    
    protected function getEmployeeEntitlement($parameters) {

            $localizationService = new LocalizationService();
            $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
            
            $fromDate = $localizationService->convertPHPFormatDateToISOFormatDate($inputDatePattern, $parameters['fd']);
            $toDate = $localizationService->convertPHPFormatDateToISOFormatDate($inputDatePattern, $parameters['td']);
            
            $leaveEntitlementSearchParameterHolder = new LeaveEntitlementSearchParameterHolder();
            $leaveEntitlementSearchParameterHolder->setEmpNumber($parameters['empId']);
            $leaveEntitlementSearchParameterHolder->setFromDate($fromDate);
            $leaveEntitlementSearchParameterHolder->setLeaveTypeId($parameters['lt']);
            $leaveEntitlementSearchParameterHolder->setToDate($toDate);
            
            
            $entitlementList = $this->getEntitlementService()->searchLeaveEntitlements( $leaveEntitlementSearchParameterHolder );
            $oldValue = 0;
            $newValue = $parameters['ent'];
            
            foreach ($entitlementList as $existingEntitlement) {
                $oldValue += $existingEntitlement->getNoOfDays();
            }
            
            return array($oldValue, $newValue + $oldValue);
        
    }
    
    public function execute($request) {
        sfConfig::set('sf_web_debug', false);
        sfConfig::set('sf_debug', false);
        
        $employees = $this->getEmployeeEntitlement($request->getGetParameters());


        $response = $this->getResponse();
        $response->setHttpHeader('Expires', '0');
        $response->setHttpHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0, max-age=0");
        $response->setHttpHeader("Cache-Control", "private", false);

        
         return $this->renderText(json_encode($employees))  ;
          
    }
}
