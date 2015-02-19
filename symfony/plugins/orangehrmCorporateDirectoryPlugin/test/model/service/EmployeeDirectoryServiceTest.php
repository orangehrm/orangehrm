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

/**
 * @group CorporateDirectory
 */
class EmployeeDirectoryServiceTest extends PHPUnit_Framework_TestCase {



    private $employeeDirectoryService;


    /**
     * Set up method
     */
    protected function setUp() {
        $this->employeeDirectoryService = new EmployeeDirectoryService();
    }
    
    public function testGetSetEmployeeDirectoryDao() {
        $mockDao = $this->getMock('EmployeeDirectoryDao');
                
        $this->employeeDirectoryService->setEmployeeDirectoryDao($mockDao);
        $this->assertEquals($mockDao, $this->employeeDirectoryService->getEmployeeDirectoryDao());        
    }

    
    /**
     * Testing searchEmployee
     */
//    public function testSearchEmployee() {
//        $field = 'empNumber';
//        $value = '2';
//        
//        $employees = array();
//        foreach ($this->testCase['Employee'] as $values ) {
//            $employee = new Employee();
//            $employee->fromArray($values);
//            $employees[] = $employee;
//        }             
//        
//        $mockDao = $this->getMock('EmployeeDao');
//        $mockDao->expects($this->once())
//                 ->method('searchEmployee')
//                 ->with($field, $value)
//                 ->will($this->returnValue($employees));
//        
//        $this->employeeDirectoryService->setEmployeeDao($mockDao);
//        
//        $result = $this->employeeDirectoryService->searchEmployee($field, $value);
//        $this->assertEquals($employees, $result);              
//        
//    }    
//    
     public function testSearchEmployeeList(){
        
        $parameterHolder = new EmployeeSearchParameterHolder();
        
        $employee1 = new Employee();
        $employee1->setLastName('Last Name');
        $employee1->setFirstName('First Name');
        
        $employee2 = new Employee();
        $employee2->setLastName('Last Name');
        $employee2->setFirstName('First Name');
                
        $list   =   array( $employee1,$employee2);
        
        $mockDao = $this->getMock('EmployeeDirectoryDao');
        $mockDao->expects($this->once())
             ->method('searchEmployees')
             ->with($parameterHolder)   
             ->will($this->returnValue($list));
        
        $this->employeeDirectoryService->setEmployeeDirectoryDao($mockDao);
        $result = $this->employeeDirectoryService->searchEmployees($parameterHolder);
        $this->assertEquals($list, $result);
        
    }
    
    /**
     * Testing getEmployeeCount
     */
    public function testGetSearchEmployeeCount() {

        $count = 20;         
        
        $mockDao = $this->getMock('EmployeeDirectoryDao');
        $mockDao->expects($this->once())
                 ->method('getSearchEmployeeCount')
                 ->will($this->returnValue($count));
        
        $this->employeeDirectoryService->setEmployeeDirectoryDao($mockDao);
        
        $result = $this->employeeDirectoryService->getSearchEmployeeCount();
        $this->assertEquals($count, $result);              
        
    }    
    
    
    
}
