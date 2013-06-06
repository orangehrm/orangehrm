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
require_once sfConfig::get('sf_test_dir') . '/util/TestDataService.php';

/**
 * @group Pim
 */
class EmployeeListDaoTest extends PHPUnit_Framework_TestCase {
    private $testCase;
    private $employeeDao;
    protected $fixture;

    /**
     * Set up method
     */
    protected function setUp() {
        $this->employeeDao = new EmployeeDao();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/EmployeeDao.yml';
        TestDataService::populate($this->fixture);
    }
    
   
    public function testSearchEmployeeList() {
        $result = $this->employeeDao->searchEmployees($this->_getParameterHolder());
        
         $this->assertTrue( $result instanceof Doctrine_Collection);
    }
    
    public function testGetEmployeeCount(){
         $result = $this->employeeDao->getSearchEmployeeCount();
         $this->assertEquals(5,$result);
    }
    
    public function testSearchEmployeeListByFirstName(){
      
         $result = $this->employeeDao->searchEmployees($this->_getParameterHolder());
         $this->assertEquals( $result[0]->getFirstName(),'Kayla');
         $this->assertEquals(1,count($result));
    }
    
    private function _getParameterHolder() {
        
         $filters = array ( 
                        'employee_name' => 'Kayla',
                        'id'=>'',
                        'employee_status' => 0,
                        'termination' => 1,
                        'supervisor_name' => '',
                        'job_title' => 0,
                        'sub_unit' => 0
                        );
         
         $parameterHolder = new EmployeeSearchParameterHolder();
         $parameterHolder->setFilters($filters);
         $parameterHolder->setOrderField('empNumber');
         
         return $parameterHolder;
        
    }
    

}
?>
