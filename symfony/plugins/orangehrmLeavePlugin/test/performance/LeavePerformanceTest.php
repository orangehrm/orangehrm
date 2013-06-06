<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2010 OrangeHRM Inc., http://www.orangehrm.com
 *
 * Please refer the file license/LICENSE.TXT for the license which includes terms and conditions on using this software.
 *
 * */

/**
 * Description of LeavePerformance
 *
 * @author samantha
 */
class LeavePerformanceTest extends PHPUnit_Framework_TestCase{
    
   
    
    public function testBulkAssignLeaveEntitlements() {
         $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmLeavePlugin/test/fixtures/LeaveEntitlement.yml';
        TestDataService::populate($this->fixture);
        
        $dao = new LeaveEntitlementDao();
        $limit = 5000 ;
        $empList = array();
        $employeeService = new EmployeeService();
        $employeeService->setEmployeeDao(new EmployeeDao());
        
        for( $i=0 ; $i < $limit ; $i++){
             $employee = new Employee();
             $employee->setFirstName($i);
             $employee = $employeeService->saveEmployee($employee);
             array_push($empList, $employee->getEmpNumber());
             
        }
        $start_time = microtime(true);
        $leaveEntitlement = new LeaveEntitlement();
        $leaveEntitlement->setLeaveTypeId(1);

        $leaveEntitlement->setCreditedDate(date('Y-m-d'));


        $leaveEntitlement->setEntitlementType(LeaveEntitlement::ENTITLEMENT_TYPE_ADD);
        $leaveEntitlement->setDeleted(0);

        $leaveEntitlement->setNoOfDays(2);
        $leaveEntitlement->setFromDate('2012-01-01');
        $leaveEntitlement->setToDate('2012-08-01');

        $result = $dao->bulkAssignLeaveEntitlements($empList, $leaveEntitlement);
        $deference = (microtime(true) - $start_time);
       
        $this->assertEquals(count($empList),$result,"Time Deference - ".$deference);
        
        echo "Add Entitlement 5000 Time ".$deference ;
    }
    
}

?>
