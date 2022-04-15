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
require_once sfConfig::get('sf_test_dir') . '/util/TestDataService.php';
class EmployeeDirectoryDaoTest extends PHPUnit_Framework_TestCase {

    private $employeeDirectoryDao;

    /**
     * Set up method
     */
  protected function setUp() {
        $this->employeeDirectoryDao = new EmployeeDirectoryDao();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmCorporateDirectoryPlugin/test/fixtures/EmployeeDirectoryDao.yml';
        TestDataService::populate($this->fixture);
    }
    
    public function testGetSearchEmployeeCount() {
        $result = $this->employeeDirectoryDao->getSearchEmployeeCount();
        $this->assertTrue(!empty($result));
        $this->assertEquals('6', $result);
    }

    public function testSearchEmployeeList() {
        $result = $this->employeeDirectoryDao->searchEmployees($this->_getParameterHolder());

        $this->assertTrue($result instanceof Doctrine_Collection);
    }

    public function testSearchEmployeeListByName() {

        $result = $this->employeeDirectoryDao->searchEmployees($this->_getParameterHolder());
        $this->assertEquals($result[0]->getFirstName(), 'Saman');
        $this->assertEquals(2, count($result));
        
    }
    
    public function testSearchByJobTitle() {
        $parameterHolder = new EmployeeSearchParameterHolder();
        $parameterHolder->setOrderField('empNumber');
        $filters = array('job_title' => 2);
        $parameterHolder->setFilters($filters);

        $result = $this->employeeDirectoryDao->searchEmployees($parameterHolder);
        $this->assertEquals(2, count($result));
        $this->assertEquals(2, $result[0]->getEmpNumber());
        $this->assertEquals(3, $result[1]->getEmpNumber());
    }
    
    
     public function testSearchEmployeeListByNameReturnsAllEmployeContactDetails() {
         $parameterHolder = $this->_getParameterHolder();
         $filters = $parameterHolder->getFilters();
         $filters['employee_name'] = 'kamal';
         $parameterHolder->setFilters($filters);
         
        $employeeCollection  = $this->employeeDirectoryDao->searchEmployees($parameterHolder);
        $this->assertEquals($employeeCollection[0]->getFirstName(), 'kamal');
        $this->assertEquals(1, count($employeeCollection));
        $this->assertEquals('4444', $employeeCollection[0]->getEmpMobile());
        $this->assertEquals('3333', $employeeCollection[0]->getEmpHmTelephone());
        $this->assertEquals('aaa@aaa.com', $employeeCollection[0]->getEmpOthEmail());
    }
    
    private function _getParameterHolder() {
        
         $filters = array ( 
                        'employee_name' => 'Saman',
                        'id'=>'',
                        'job_title' => 0
                        );
         
         $parameterHolder = new EmployeeSearchParameterHolder();
         $parameterHolder->setFilters($filters);
         $parameterHolder->setOrderField('empNumber');
         
         return $parameterHolder;
        
    }

}
