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
class EmployeeServiceTest extends PHPUnit_Framework_TestCase {
    private $testCase;
    private $employeeDao;
    private $employeeService;
    private $fixture;

    /**
     * Set up method
     */
    protected function setUp() {
        $this->testCase = sfYaml::load(sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/employee.yml');
        $this->employeeService = new EmployeeService();
    }

    /**
     * Testing addEmployee
     */
    public function testAddEmployee() {
        foreach($this->testCase['Employee'] as $k => $v) {
            $employee	=	new Employee();
            $employee->setLastName($v['lastName']);
            $employee->setFirstName($v['firstName']);

            $this->employeeDao  =	$this->getMock('EmployeeDao');
            $this->employeeDao->expects($this->once())
                    ->method('addEmployee')
                    ->will($this->returnValue(true));
            $this->employeeService->setEmployeeDao($this->employeeDao);
            $result = $this->employeeService->addEmployee($employee);
            $this->assertTrue($result);
        }
    }

    /**
     * Testing Adding Employee Picture
     */
    public function testAddEmployeePicture() {
        foreach($this->testCase['Employee'] as $k => $v) {
            $this->employeeDao  =	$this->getMock('EmployeeDao');
            $this->employeeDao->expects($this->once())
                    ->method('saveEmployeePicture')
                    ->will($this->returnValue(true));
            $this->employeeService->setEmployeeDao($this->employeeDao);

            $pic = new EmpPicture();
            $pic->setEmpNumber($v['id']);
            $pic->setFilename("pic_" . rand(0, 1000));
            $result = $this->employeeService->saveEmployeePicture($pic);
            $this->assertTrue($result);
        }
    }

    /**
     * Testing readEmployeePicture
     */
    public function testManipulateEmployeePicture() {
        foreach($this->testCase['Employee'] as $k => $v) {
            $this->employeeDao  =	$this->getMock('EmployeeDao');
            $this->employeeDao->expects($this->once())
                    ->method('readEmployeePicture')
                    ->will($this->returnValue(new EmpPicture()));
            $this->employeeService->setEmployeeDao($this->employeeDao);
            $pic = $this->employeeService->readEmployeePicture($v['id']);
            $this->assertTrue($pic instanceof EmpPicture);
        }
    }

    /**
     * Testing deletePhoto
     */
    public function testDeletePhoto() {
        foreach($this->testCase['Employee'] as $k => $v) {
            $this->employeeDao  =	$this->getMock('EmployeeDao');
            $this->employeeDao->expects($this->once())
                    ->method('deletePhoto')
                    ->will($this->returnValue(true));
            $this->employeeService->setEmployeeDao($this->employeeDao);
            $result = $this->employeeService->deletePhoto($v['id']);
            $this->assertTrue($result);
        }
    }

    /**
     * Testing getEmployeeListAsJson
     */
    public function testGetEmployeeListAsJson() {
        $empDao  = new EmployeeDao();
        $str     = $empDao->getEmployeeListAsJson();

        $this->employeeDao  =	$this->getMock('EmployeeDao');
        $this->employeeDao->expects($this->once())
                ->method('getEmployeeListAsJson')
                ->will($this->returnValue($str));
        $this->employeeService->setEmployeeDao($this->employeeDao);
        $result = $this->employeeService->getEmployeeListAsJson();
        $this->assertEquals($str, $result);
    }

    /**
     * Testing deleteEmployee
     */
    public function testDeleteEmployee() {
        foreach($this->testCase['Employee'] as $k => $v) {
            $this->employeeDao  =	$this->getMock('EmployeeDao');
            $this->employeeDao->expects($this->once())
                    ->method('deleteEmployee')
                    ->will($this->returnValue(1));
            $this->employeeService->setEmployeeDao($this->employeeDao);
            $result = $this->employeeService->deleteEmployee(array($v['id']));
            $this->assertEquals($result, 1);
        }
    }

    /**
     * Test GetEmergencyContacts
     */
    public function testGetEmergencyContacts() {

        // TODO: Load from fixture
        $contacts = array();
        $contacts[0] = new EmpEmergencyContact();
        $contacts[1] = new EmpEmergencyContact();

        $empNumber = 2;

        $emergencyContactList = array();

        $employeeDao = $this->getMock('EmployeeDao');

        $employeeDao->expects($this->once())
                ->method('getEmergencyContacts')
                ->with($empNumber)
                ->will($this->returnValue($contacts));

        $this->employeeService->setEmployeeDao($employeeDao);

        $emgContacts = $this->employeeService->getEmergencyContacts($empNumber);
        $this->assertEquals(count($contacts), count($emgContacts));
        $this->assertEquals($emgContacts, $contacts);


        // Test exception
        $employeeDao = $this->getMock('EmployeeDao');

        $employeeDao->expects($this->once())
                ->method('getEmergencyContacts')
                ->with($empNumber)
                ->will($this->throwException(new DaoException('test')));

        $this->employeeService->setEmployeeDao($employeeDao);

        try {
            $emgContacts = $this->employeeService->getEmergencyContacts($empNumber);
            $this->fail('DaoException expected');
        } catch (PIMServiceException $e) {
            // expected
        }
    }

    /**
     * Test SaveEmployeePassport
     */
    public function testSaveEmployeePassport() {

        $employeeDao = $this->getMock('EmployeeDao');

        $employeeDao->expects($this->once())
                ->method('saveEmployeePassport')
                ->will($this->returnValue(true));

        $this->employeeService->setEmployeeDao($employeeDao);
        $empPassport = new EmpPassPort();
        $empPassport->setEmpNumber(1);
        $result = $this->employeeService->saveEmployeePassport($empPassport);
        $this->assertTrue($result);

    }

    /**
     * Test saving getEmployeePassport returns object
     */
    public function testGetEmployeePassport() {

        $employeeDao = $this->getMock('EmployeeDao');

        $employeeDao->expects($this->once())
                ->method('getEmployeePassport')
                ->will($this->returnValue(new EmpPassport()));

        $this->employeeService->setEmployeeDao($employeeDao);

        $readEmpPassport = $this->employeeService->getEmployeePassport(1);
        $this->assertTrue($readEmpPassport instanceof EmpPassport);

    }

    /**
     * Test getEmployeeTax returns object
     */
    public function testGetEmployeeTaxExemptions() {

        $employeeDao = $this->getMock('EmployeeDao');

        $employeeDao->expects($this->once())
                ->method('getEmployeeTaxExemptions')
                ->will($this->returnValue(new EmpUsTaxExemption()));

        $this->employeeService->setEmployeeDao($employeeDao);

        $readEmpTaxExemption = $this->employeeService->getEmployeeTaxExemptions(1);
        $this->assertTrue($readEmpTaxExemption instanceof EmpUsTaxExemption);

    }
}
?>