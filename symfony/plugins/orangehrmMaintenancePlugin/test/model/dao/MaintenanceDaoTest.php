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
 * Boston, MA 02110-1301, USA
 */

/**
 * Class MaintenanceDaoTest
 * @group maintenance
 */
class MaintenanceDaoTest extends PHPUnit_Framework_TestCase
{

    protected $fixture;
    private $maintenanceDao;

    /**
     * Set up method
     */
    protected function setUp()
    {
        $this->maintenanceDao = new MaintenanceDao();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmMaintenancePlugin/test/fixtures/EmployeeDaoWithDeletedEmployee.yml';
        TestDataService::populate($this->fixture);
    }

    /**
     * @return EmployeeService
     */
    public function getEmployeeService()
    {
        if (!isset($this->employeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }

    /**
     *
     */
    public function testGetEmployeePurgingList()
    {
        $data = $this->maintenanceDao->getEmployeePurgingList();
        $this->assertEquals(gettype($data), 'object');
        $this->assertEquals(sizeof($data), 1);

        $data = $this->maintenanceDao->getEmployeePurgingList()->toArray();
        $this->assertEquals(sizeof($data), 1);

        $employeeId = $data[0]['empNumber'];
        $employee = $this->getEmployeeService()->getEmployee($employeeId);

        $this->assertEquals($employee->getFirstName(), 'Kayla');
        $this->assertEquals($employee->getLastName(), 'Abbey');
        $this->assertEquals($employee->getMiddleName(), 'T');
        $this->assertEquals($employee->getNickName(), 'viki');
        $this->assertEquals($employee->getJobTitleName(), 'Software Engineer');
        $this->assertEquals($employee->getEmpMobile(), '111111');
        $this->assertEquals($employee->getEmpOthEmail(), 'kayla2@xample.com');
        $this->assertEquals($employee->getEmpWorkEmail(), 'kayla@xample.com');
    }

    /**
     * @throws DaoException
     */
    public function testExtractDataFromEmpNumber()
    {
        $table = 'Employee';
        $employeeId = 1;
        $matchByValues = ['empNumber' => '1'];

        $employee = $this->getEmployeeService()->getEmployee($employeeId);
        $this->assertEquals($employee->getFirstName(), 'Kayla');
        $this->assertEquals($employee->getLastName(), 'Abbey');
        $this->assertEquals($employee->getMiddleName(), 'T');
        $this->assertEquals($employee->getNickName(), 'viki');

        $data = $this->maintenanceDao->extractDataFromEmpNumber($matchByValues, $table);
        $this->assertTrue(sizeof($data) > 0);
        $this->assertEquals($data[0]->getFirstName(), 'Kayla');
        $this->assertEquals($data[0]->getLastName(), 'Abbey');
        $this->assertEquals($data[0]->getMiddleName(), 'T');
        $this->assertEquals($data[0]->getNickName(), 'viki');
    }

    /**
     * @throws DaoException
     */
    public function testSaveEntity()
    {
        $employeeId = 1;
        $employee = $this->getEmployeeService()->getEmployee($employeeId);
        $this->assertEquals($employee->getFirstName(), 'Kayla');
        $this->assertEquals($employee->getLastName(), 'Abbey');
        $this->assertEquals($employee->getMiddleName(), 'T');
        $this->assertEquals($employee->getNickName(), 'viki');

        $employee->firstName = 'ashan1';
        $employee->lastName = 'ashan2';
        $employee->middleName = 'ashan3';
        $employee->nickName = 'ashan4';

        $this->maintenanceDao->saveEntity($employee);
        $employee = $this->getEmployeeService()->getEmployee($employeeId);
        $this->assertEquals($employee->getFirstName(), 'ashan1');
        $this->assertEquals($employee->getLastName(), 'ashan2');
        $this->assertEquals($employee->getMiddleName(), 'ashan3');
        $this->assertEquals($employee->getNickName(), 'ashan4');
    }
}
