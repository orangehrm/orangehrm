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
 * Description of AdminUserRole
 *
 * @author Chameera Senarathna
 */
class AdminUserRole implements UserRoleInterface {

    protected $employeeService;
    protected $systemUserService;
    protected $operationalCountryService;
    protected $locationService;

    public function getSystemUserService() {
        if (empty($this->systemUserService)) {
            $this->systemUserService = new SystemUserService();
        }
        return $this->systemUserService;
    }

    public function setSystemUserService($systemUserService) {
        $this->systemUserService = $systemUserService;
    }

    public function getEmployeeService() {

        if (empty($this->employeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }

    public function setEmployeeService($employeeService) {
        $this->employeeService = $employeeService;
    }

    public function getLocationService() {
        if (empty($this->locationService)) {
            $this->locationService = new LocationService();
        }
        return $this->locationService;
    }

    public function setLocationService($locationService) {
        $this->locationService = $locationService;
    }

    public function getOperationalCountryService() {
        if (empty($this->operationalCountryService)) {
            $this->operationalCountryService = new OperationalCountryService();
        }
        return $this->operationalCountryService;
    }

    public function setOperationalCountryService($operationalCountryService) {
        $this->operationalCountryService = $operationalCountryService;
    }

    public function getAccessibleEmployeeIds($operation = null, $returnType = null) {

        return $this->getEmployeeService()->getEmployeeIdList(false);
    }

    public function getAccessibleEmployeePropertyList($properties, $orderField, $orderBy) {

        return $this->getEmployeeService()->getEmployeePropertyList($properties, $orderField, $orderBy, false);
    }

    public function getAccessibleEmployees($operation = null, $returnType = null) {

        $employees = $this->getEmployeeService()->getEmployeeList('empNumber', 'ASC', true);

        $employeesWithIds = array();

        foreach ($employees as $employee) {
            $employeesWithIds[$employee->getEmpNumber()] = $employee;
        }

        return $employeesWithIds;
    }

    public function getAccessibleLocationIds($operation, $returnType) {

        $locations = $this->getLocationService()->getLocationList();

        $ids = array();

        foreach ($locations as $location) {
            $ids[] = $location->getId();
        }

        return $ids;
    }

    public function getAccessibleOperationalCountryIds($operation, $returnType) {

        $operationalCountries = $this->getOperationalCountryService()->getOperationalCountryList();

        $ids = array();

        foreach ($operationalCountries as $country) {
            $ids[] = $country->getId();
        }

        return $ids;
    }

    public function getAccessibleSystemUserIds($operation, $returnType) {

        return $this->getSystemUserService()->getSystemUserIdList();
    }

    public function getAccessibleUserRoleIds($operation, $returnType) {

        $userRoles = $this->getSystemUserService()->getAssignableUserRoles();

        $ids = array();

        foreach ($userRoles as $role) {
            $ids[] = $role->getId();
        }

        return $ids;
    }

}