<?php
require_once 'PHPUnit/Framework.php';
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

require_once  sfConfig::get('sf_test_dir') . '/util/TestDataService.php';

class EmployeeDaoTest extends PHPUnit_Framework_TestCase {
    private $testCase;
    private $employeeDao;

    /**
     * Set up method
     */
    protected function setUp() {
        $this->testCase = sfYaml::load(sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/employee.yml');
        $this->employeeDao = new EmployeeDao();
        TestDataService::populate(sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/EmployeeDao.yml');
    }

    /**
     * Testing addEmployee
     */
    public function testAddEmployee() {
        foreach($this->testCase['Employee'] as $k => $v) {
            $employee	=	new Employee();
            $employee->setLastName($v['lastName']);
            $employee->setFirstName($v['firstName']);
            $result		=	$this->employeeDao->addEmployee($employee);
            $this->assertTrue($result);
            $this->testCase['Employee'][$k]['id'] = $employee->getEmpNumber();
        }
        file_put_contents(sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/employee.yml', sfYaml::dump($this->testCase));
    }

    /**
     * Testing Adding Employee Picture
     */
    public function testAddEmployeePicture() {
        foreach($this->testCase['Employee'] as $k => $v) {
            $pic = new EmpPicture();
            $pic->setEmpNumber($v['id']);
            $pic->setFilename("pic_" . rand(0, 1000));
            $result = $this->employeeDao->saveEmployeePicture($pic);
            $this->assertTrue($result);
        }
    }

    /**
     * Testing Manipulation of Employee Picture
     */
    public function testManipulateEmployeePicture() {
        foreach($this->testCase['Employee'] as $k => $v) {
            $pic = $this->employeeDao->readEmployeePicture($v['id']);
            $this->assertTrue($pic instanceof EmpPicture);

            $pic = $this->employeeDao->getPicture($v['id']);
            $this->assertTrue($pic instanceof EmpPicture);

            $result = $this->employeeDao->deletePhoto($v['id']);
            $this->assertTrue($result);
        }
    }

    /**
     * Testing getEmployeeListAsJson
     */
    public function testGetEmployeeListAsJson() {
        $result = $this->employeeDao->getEmployeeListAsJson();
        $this->assertTrue(!empty($result));
    }

    /**
     * Testing deleteEmployee and all associated domain classes
     */
    public function testDeleteEmployee() {
        foreach($this->testCase['Employee'] as $k => $v) {
            $employee = $this->employeeDao->getEmployee($v['id']);
            $this->assertTrue($employee instanceof Employee);

            $result = $this->employeeDao->deleteEmployee(array($v['id']));
            $this->assertEquals($result, 1);
            unset($this->testCase['Employee'][$k]['id']);
        }
        file_put_contents(sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/employee.yml', sfYaml::dump($this->testCase));
    }

    /**
     * Test saving EmployeePassport without sequence number
     */
    public function testSaveEmployeePassport1() {

        $empPassport = new EmpPassPort();
        $empPassport->setEmpNumber(1);
        $empPassport->country = 'LK';
        $result = $this->employeeDao->saveEmployeePassport($empPassport);
        $this->assertTrue($result);
        $this->assertEquals(1, $empPassport->seqno);

    }

    /**
     * Test saving EmployeePassport
     */
    public function testSaveEmployeePassport2() {

        $empPassport = TestDataService::fetchLastInsertedRecords('EmpPassport', 2);
        $empNumbers = array(1 => 1, 2 => 2);

        foreach($empPassport as $passport) {
            
            $this->assertTrue($passport instanceof EmpPassPort);
            $this->assertEquals($empNumbers[$passport->getEmpNumber()], $passport->getEmpNumber());
            $comment = "I add more comments";
            $passport->comments = $comment;
            $result = $this->employeeDao->saveEmployeePassport($passport);
            $this->assertTrue($result);

            $savedPassport = $this->employeeDao->getEmployeePassport($passport->getEmpNumber(), $passport->getSeqno());
            $this->assertEquals($comment, $savedPassport->comments);
            $this->assertEquals($savedPassport, $passport);

        }
    }

    /**
     * Test saving getEmployeePassport returns object
     */
    public function testGetEmployeePassport1() {

        $empPassports = TestDataService::fetchLastInsertedRecords('EmpPassport', 2);

        foreach($empPassports as $passport) {

            $empPassport = $this->employeeDao->getEmployeePassport($passport->getEmpNumber(), $passport->getSeqno());
            $this->assertEquals($passport, $empPassport);

        }

    }
    
    /**
     * Test saving getEmployeePassport returns Collection
     */
    public function testGetEmployeePassport2() {

        $empPassports = TestDataService::fetchLastInsertedRecords('EmpPassport', 2);

        foreach($empPassports as $passport) {

            $collection = $this->employeeDao->getEmployeePassport($passport->getEmpNumber());
            $this->assertTrue($collection instanceof Doctrine_Collection);
            
        }
        
    }
}
?>