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

require_once 'PHPUnit/Framework.php';

/**
 * Unit test for Employee class
 */
class EmployeeTest extends PHPUnit_Framework_TestCase {

    /** 
     * Test data
     * Loaded from employee_fixture.yml.
     */
    protected $testCases;
    
    /**
     * Employee
     *
     * @var Employee
     */
    protected $employee;
        
    /**
     * PhpUnit Setup function. 
     */
    public function setup() {
        $configuration = ProjectConfiguration::getApplicationConfiguration('orangehrm', 'test', true);       
        $this->testCases = sfYaml::load(sfConfig::get('sf_test_dir') . '/fixtures/employee_test.yml');

        $this->employee = new Employee();
    }
    
    /**
     * Test the getFullName() function
     */
    public function testGetFullName() {
       
       foreach ($this->testCases as $testCase) {
           $this->employee->fromArray($testCase);
           $this->assertEquals($testCase['fullName'], $this->employee->getFullName());    
       }       
       
    }    

    /**
     * Test the GetSupervisorNames function
     */
    public function testGetSupervisorNames() {
        
        // Employee with no supervisors
        $this->assertEquals('', $this->employee->getSupervisorNames());

        // Employee with one supervisor     
        $names = array($this->testCases[0]);
        $repTo = $this->_setSupervisors($names);        
        
        $expected = $this->_getFirstAndLastName($this->testCases[0]);
        $this->assertEquals($expected, $this->employee->getSupervisorNames());
        
        // Employee with 3 supervisors
        
        // add two more supervisors
        $names = array_slice($this->testCases, 1, 2);
        $this->_setSupervisors($names);        
        
        $expected = $this->_getFirstAndLastName($this->testCases[0]) . ', '
                    . $this->_getFirstAndLastName($this->testCases[1]) . ', '
                    . $this->_getFirstAndLastName($this->testCases[2]);
         
        $this->assertEquals($expected, $this->employee->getSupervisorNames());                
    }

    /**
     * Returns a Doctrine_Collection of supervisor names suitable for assigning
     * to an employee.
     * @param array $supervisorNames 
     * @return Doctrine_Collection of Employee objects 
     */
    private function _setSupervisors(array $supervisors) {

        foreach ($supervisors as $supervisor){
            $employee = new Employee();
            $employee->fromArray($supervisor);

            $this->employee->supervisors[] = $employee;
        }
    }
    
    private function _getFirstAndLastName($row) {
        return trim($row['firstName'] . ' ' . $row['lastName']);
    }


}