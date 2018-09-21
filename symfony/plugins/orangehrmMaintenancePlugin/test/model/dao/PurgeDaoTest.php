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
 * Class PurgeDaoTest
 * @group maintenance
 */
class PurgeDaoTest extends PHPUnit_Framework_TestCase
{

    protected $fixture;
    private $purgeDao;

    /**
     * Set up method
     */
    protected function setUp()
    {
        $this->purgeDao = new PurgeDao();
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
     * @throws DaoException
     */
    public function testReplaceEntityValuesWithValuesAndSingleMatcher()
    {
        $employeeNumber = 1;
        $entityClassName = "Employee";
        $fieldValueArray = array(
            "nickName" => "Shali",
            "emp_mobile" => "0714582524"
        );
        $matchByValuesArray = array("empNumber" => $employeeNumber);
        $employee = $this->getEmployeeService()->getEmployee($employeeNumber);
        $this->assertEquals("viki", $employee->getNickName());
        $this->assertEquals("111111", $employee->getEmpMobile());
        $recordsCount = $this->purgeDao->replaceEntityValues($entityClassName, $fieldValueArray, $matchByValuesArray);
        $this->assertEquals(1, $recordsCount);
        $employee = $this->getEmployeeService()->getEmployee($employeeNumber);
        $this->assertEquals("Shali", $employee->getNickName());
        $this->assertEquals("0714582524", $employee->getEmpMobile());
    }

    /**
     * @throws DaoException
     */
    public function testReplaceEntityValuesWithValuesAndNullValuesSingleMatcher()
    {
        $employeeNumber = 1;
        $entityClassName = "Employee";
        $fieldValueArray = array(
            "nickName" => "Shali",
            "emp_mobile" => null
        );
        $matchByValuesArray = array("empNumber" => $employeeNumber);
        $employee = $this->getEmployeeService()->getEmployee($employeeNumber);
        $this->assertEquals("viki", $employee->getNickName());
        $this->assertEquals("111111", $employee->getEmpMobile());
        $recordsCount = $this->purgeDao->replaceEntityValues($entityClassName, $fieldValueArray, $matchByValuesArray);
        $this->assertEquals(1, $recordsCount);
        $employee = $this->getEmployeeService()->getEmployee($employeeNumber);
        $this->assertEquals("Shali", $employee->getNickName());
        $this->assertNull($employee->getEmpMobile());
    }

    /**
     * @throws DaoException
     */
    public function testReplaceEntityValuesWithValuesAndNullValuesMultipleMatcher()
    {
        $employeeNumber = 1;
        $entityClassName = "Employee";
        $fieldValueArray = array(
            "nickName" => "Shali",
            "emp_mobile" => null
        );
        $matchByValuesArray = array(
            "firstName" => "Shalitha",
            "lastName" => "Vikum"
        );
        $employee = $this->getEmployeeService()->getEmployee($employeeNumber);
        $this->assertEquals("viki", $employee->getNickName());
        $this->assertEquals("111111", $employee->getEmpMobile());
        $recordsCount = $this->purgeDao->replaceEntityValues($entityClassName, $fieldValueArray, $matchByValuesArray);
        $this->assertEquals(1, $recordsCount);
        $employee = $this->getEmployeeService()->getEmployee($employeeNumber);
        $this->assertEquals("viki", $employee->getNickName());
    }

    /**
     * @throws DaoException
     */
    public function testReplaceEntityValuesWithValuesMatcherMatchingMultipleEntities()
    {
        $entityClassName = "Employee";
        $fieldValueArray = array(
            "firstName" => "John",
        );
        $matchByValuesArray = array(
            "middleName" => "ST",
        );

        $employee1 = $this->getEmployeeService()->getEmployee(2);
        $employee2 = $this->getEmployeeService()->getEmployee(4);
        $this->assertEquals("Ashley", $employee1->getFirstName());
        $this->assertEquals("Chaturanga", $employee2->getFirstName());
        $recordsCount = $this->purgeDao->replaceEntityValues($entityClassName, $fieldValueArray, $matchByValuesArray);
        $this->assertEquals(2, $recordsCount);
        $employee1 = $this->getEmployeeService()->getEmployee(2);
        $employee2 = $this->getEmployeeService()->getEmployee(4);
        $this->assertEquals("John", $employee1->getFirstName());
        $this->assertEquals("John", $employee2->getFirstName());
    }

    /**
     * @throws DaoException
     */
    public function testReplaceEntityValuesWithValuesAndMatcherMatchingMultipleEntitiesUsingArray()
    {
        $entityClassName = "Employee";
        $fieldValueArray = array(
            "firstName" => "John",
        );
        $matchByValuesArray = array(
            "empNumber" => array(2, 4),
        );
        $employee1 = $this->getEmployeeService()->getEmployee(2);
        $employee2 = $this->getEmployeeService()->getEmployee(4);
        $this->assertEquals("Ashley", $employee1->getFirstName());
        $this->assertEquals("Chaturanga", $employee2->getFirstName());
        $recordsCount = $this->purgeDao->replaceEntityValues($entityClassName, $fieldValueArray, $matchByValuesArray);
        $this->assertEquals(2, $recordsCount);
        $employee1 = $this->getEmployeeService()->getEmployee(2);
        $employee2 = $this->getEmployeeService()->getEmployee(4);
        $this->assertEquals("John", $employee1->getFirstName());
        $this->assertEquals("John", $employee2->getFirstName());
    }

    /**
     * @throws DaoException
     */
    public function testRemoveEntitiesSingleMatcher()
    {

        $employeeNumber = 1;
        $entityClassName = "Employee";
        $matchByValuesArray = array("empNumber" => $employeeNumber);
        $employee = $this->getEmployeeService()->getEmployee($employeeNumber);
        $this->assertEquals("viki", $employee->getNickName());
        $this->assertEquals("111111", $employee->getEmpMobile());
        $recordsCount = $this->purgeDao->removeEntities($entityClassName, $matchByValuesArray);
        $this->assertEquals(1, $recordsCount);
        $employee = $this->getEmployeeService()->getEmployee($employeeNumber);
        $this->assertFalse($employee);
    }

    /**
     * @throws DaoException
     */
    public function testRemoveEntitiesMultipleMatcher()
    {
        $employeeNumber = 1;
        $entityClassName = "Employee";
        $matchByValuesArray = array(
            "empNumber" => "1"
        );
        $employee = $this->getEmployeeService()->getEmployee($employeeNumber);
        $this->assertEquals("viki", $employee->getNickName());
        $this->assertEquals("111111", $employee->getEmpMobile());
        $recordsCount = $this->purgeDao->removeEntities($entityClassName, $matchByValuesArray);
        $this->assertEquals(1, $recordsCount);
        $employee = $this->getEmployeeService()->getEmployee($employeeNumber);
        $this->assertFalse($employee instanceof Employee);
    }

    /**
     * @throws DaoException
     */
    public function testRemoveEntitiesSingleMatcherUsingArray()
    {
        $entityClassName = "Employee";
        $matchByValuesArray = array(
            "empNumber" => array(2, 4),
        );
        $employee1 = $this->getEmployeeService()->getEmployee(2);
        $employee2 = $this->getEmployeeService()->getEmployee(4);
        $this->assertEquals("Ashley", $employee1->getFirstName());
        $this->assertEquals("Chaturanga", $employee2->getFirstName());
        $recordsCount = $this->purgeDao->removeEntities($entityClassName, $matchByValuesArray);
        $this->assertEquals(2, $recordsCount);
        $employee1 = $this->getEmployeeService()->getEmployee(2);
        $employee2 = $this->getEmployeeService()->getEmployee(4);
        $this->assertFalse($employee1);
        $this->assertFalse($employee2);
    }

    /**
     *
     */
    public function testGetEmployeePurgingList()
    {
        $data = $this->purgeDao->getEmployeePurgingList();
        $this->assertEquals(gettype($data), 'object');
    }
}
