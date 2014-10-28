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
class AdminUserRole extends AbstractUserRole {
    
    public function getAccessibleEmployeeIds($operation = null, $returnType = null, $requiredPermissions = array()) {

        return $this->getEmployeeService()->getEmployeeIdList(false);
    }

    public function getAccessibleEmployeePropertyList($properties, $orderField, $orderBy, $requiredPermissions = array()) {

        return $this->getEmployeeService()->getEmployeePropertyList($properties, $orderField, $orderBy, false);
    }

    public function getAccessibleEmployees($operation = null, $returnType = null, $requiredPermissions = array()) {

        $employees = $this->getEmployeeService()->getEmployeeList('empNumber', 'ASC', true);

        $employeesWithIds = array();

        foreach ($employees as $employee) {
            $employeesWithIds[$employee->getEmpNumber()] = $employee;
        }

        return $employeesWithIds;
    }

    public function getAccessibleLocationIds($operation = null, $returnType = null, $requiredPermissions = array()) {

        $locations = $this->getLocationService()->getLocationList();

        $ids = array();

        foreach ($locations as $location) {
            $ids[] = $location->getId();
        }

        return $ids;
    }

    public function getAccessibleOperationalCountryIds($operation = null, $returnType = null, $requiredPermissions = array()) {

        $operationalCountries = $this->getOperationalCountryService()->getOperationalCountryList();

        $ids = array();

        foreach ($operationalCountries as $country) {
            $ids[] = $country->getId();
        }

        return $ids;
    }

    public function getAccessibleSystemUserIds($operation = null, $returnType = null, $requiredPermissions = array()) {

        return $this->getSystemUserService()->getSystemUserIdList();
    }

    public function getAccessibleUserRoleIds($operation = null, $returnType = null, $requiredPermissions = array()) {

        $userRoles = $this->getSystemUserService()->getAssignableUserRoles();

        $ids = array();

        foreach ($userRoles as $role) {
            $ids[] = $role->getId();
        }

        return $ids;
    }
    
    public function getEmployeesWithRole($entities = array()) {
        return $this->getSystemUserService()->getEmployeesByUserRole($this->roleName);
    }
    
    /**
     * Returns all projects (active and inactive)
     */
    public function getAccessibleProjects($operation = null, $returnType = null, $requiredPermissions = array()) {
        $activeProjectList = $this->getProjectService()->getAllProjects();
        return $activeProjectList;
    }

    /**
     * Returns all project ids (active and inactive)
     */
    public function getAccessibleProjectIds($operation = null, $returnType = null, $requiredPermissions = array()) {
        return $this->getProjectService()->getProjectListForUserRole(null, null);
    }

    public function getAccessibleVacancyIds($operation = null, $returnType = null, $requiredPermissions = array()) {
        return $this->getVacancyService()->getVacancyIdList();
    }

    
}