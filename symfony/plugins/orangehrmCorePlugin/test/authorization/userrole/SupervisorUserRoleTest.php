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
 * Description of SupervisorUserRoleTest
 * @group Core
 */
class SupervisorUserRoleTest extends PHPUnit_Framework_TestCase {

    /** @property AdminUserRole $supervisorUserRole */
    private $supervisorUserRole;

    /**
     * Set up method
     */
    protected function setUp() {

        $this->supervisorUserRole = new SupervisorUserRole('Supervisor', new BasicUserRoleManager());
    }

    public function testGetSetEmployeeService() {
        $mockService = $this->getMockBuilder('EmployeeService')->getMock();

        $this->supervisorUserRole->setEmployeeService($mockService);
        $this->assertEquals($mockService, $this->supervisorUserRole->getEmployeeService());
    }

    public function testGetAccessibleEmployeeIds() {

        
        $mockService = $this->getMockBuilder('EmployeeService')->getMock();
        $mockService->expects($this->once())
                ->method('getSubordinateIdListBySupervisorId')
                ->will($this->returnValue(array(1, 3)));
        
        $this->supervisorUserRole->setEmployeeNumber(1);
        $this->supervisorUserRole->setEmployeeService($mockService);
        $empIds = $this->supervisorUserRole->getAccessibleEmployeeIds();
        $this->assertEquals($empIds, array(1, 3));
    }

    public function testGetAccessibleEmployeePropertyList() {
        $mockService = $this->getMockBuilder('EmployeeService')->getMock();
        $properties = array("empNumber", "firstName", "middleName", "lastName", "termination_id");
        $propertyList = new Doctrine_Collection('Employee');
        for ($i = 0; $i < 2; $i++) {
            $employee = new Employee();
            $employee->setEmployeeId($i + 1);
            $employee->setFirstName("test name" . ($i + 1));
            $propertyList->add($employee);
        }

        $mockService->expects($this->once())
                ->method('getSubordinatePropertyListBySupervisorId')
                ->with(1, $properties, 'lastName', 'ASC', true)
                ->will($this->returnValue($propertyList));

        $this->supervisorUserRole->setEmployeeNumber(1);
        $this->supervisorUserRole->setEmployeeService($mockService);
        $empProperties = $this->supervisorUserRole->getAccessibleEmployeePropertyList($properties, 'lastName', 'ASC');
        $this->assertEquals($empProperties, $propertyList);
    }

    public function testGetAccessibleEmployees() {
        $mockService = $this->getMockBuilder('EmployeeService')->getMock();
        $employeeList = new Doctrine_Collection('Employee');
        for ($i = 0; $i < 2; $i++) {
            $employee = new Employee();
            $employee->setEmployeeId($i + 1);
            $employee->setEmpNumber($i + 1);
            $employee->setFirstName("test name" . ($i + 1));
            $employeeList->add($employee);
        }

        $mockService->expects($this->once())
                ->method('getSubordinateList')
                ->with(1, true)
                ->will($this->returnValue($employeeList));

        $this->supervisorUserRole->setEmployeeNumber(1);
        $this->supervisorUserRole->setEmployeeService($mockService);
        $employees = $this->supervisorUserRole->getAccessibleEmployees();
        $this->assertEquals(2, count($employees));
        for ($i = 0; $i < 2; $i++) {
            $this->assertEquals($employees[$i + 1], $employeeList[$i]);
        }
    }
//
//    public function testGetAccessibleLocationIds() {
//        $mockService = $this->getMockBuilder('LocationService')->getMock();
//        $locationList = new Doctrine_Collection('Location');
//        for ($i = 0; $i < 3; $i++) {
//            $location = new Location();
//            $location->setId($i + 1);
//            $location->setName("test name" . $i + 1);
//            $locationList->add($location);
//        }
//        $mockService->expects($this->once())
//                ->method('getLocationList')
//                ->will($this->returnValue($locationList));
//
//        $this->supervisorUserRole->setLocationService($mockService);
//        $locationIds = $this->supervisorUserRole->getAccessibleLocationIds(null, null);
//        $this->assertEquals($locationIds, array(1, 2, 3));
//    }
//
//    public function testGetAccessibleOperationalCountryIds() {
//        $mockService = $this->getMockBuilder('OperationalCountryService')->getMock();
//        $opCountryList = new Doctrine_Collection('OperationalCountry');
//        for ($i = 0; $i < 3; $i++) {
//            $operationalCountry = new OperationalCountry();
//            $operationalCountry->setId($i + 1);
//            $opCountryList->add($operationalCountry);
//        }
//
//        $mockService->expects($this->once())
//                ->method('getOperationalCountryList')
//                ->will($this->returnValue($opCountryList));
//
//        $this->supervisorUserRole->setOperationalCountryService($mockService);
//        $opCountryIds = $this->supervisorUserRole->getAccessibleOperationalCountryIds(null, null);
//        $this->assertEquals($opCountryIds, array(1, 2, 3));
//    }
//
//    public function testGetAccessibleSystemUserIds() {
//        $mockService = $this->getMockBuilder('SystemUserService')->getMock();
//
//        $mockService->expects($this->once())
//                ->method('getSystemUserIdList')
//                ->will($this->returnValue(array(1, 2, 3)));
//
//        $this->supervisorUserRole->setSystemUserService($mockService);
//        $userIds = $this->supervisorUserRole->getAccessibleSystemUserIds(null, null);
//        $this->assertEquals($userIds, array(1, 2, 3));
//    }
//
//    public function testGetAccessibleUserRoleIds() {
//        $mockService = $this->getMockBuilder('SystemUserService')->getMock();
//        $roleList = new Doctrine_Collection('SystemUser');
//        for ($i = 0; $i < 3; $i++) {
//            $userRole = new UserRole();
//            $userRole->setId($i + 1);
//            $userRole->setName('test'.$i + 1);
//            $roleList->add($userRole);
//        }
//
//        $mockService->expects($this->once())
//                ->method('getAssignableUserRoles')
//                ->will($this->returnValue($roleList));
//
//        $this->supervisorUserRole->setSystemUserService($mockService);
//        $roleIds = $this->supervisorUserRole->getAccessibleUserRoleIds(null, null);
//        $this->assertEquals($roleIds, array(1, 2, 3));
//    }

}