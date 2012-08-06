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
 * Description of UserDefinedUserRole
 *
 */
class UserDefinedUserRole extends AbstractUserRole {
    
    
    public function getAccessibleEmployeeIds($operation, $returnType, $requiredPermissions = array()) {
        $employeeIdArray = array();
        
        // A user defined user role
        $permitted = $this->areRequiredPermissionsAvailable($requiredPermissions);

        if ($permitted) {
            $employeeIdArray = $this->getEmployeeService()->getEmployeeIdList();
        }
        
        return $employeeIdArray;
    }

    public function getAccessibleEmployeePropertyList($properties, $orderField, $orderBy, $requiredPermissions = array()) {
        
        $employeeProperties = array();
        
        $permitted = $this->areRequiredPermissionsAvailable($requiredPermissions);
        
        if ($permitted) {        
            $employeeProperties = $this->getEmployeeService()->getEmployeePropertyList($properties, $orderField, $orderBy, false);
        }
        
        return $employeeProperties;
    }

    public function getAccessibleEmployees($operation = null, $returnType = null, $requiredPermissions = array()) {
        $employees = array();
        
        $permitted = $this->areRequiredPermissionsAvailable($requiredPermissions);

        if ($permitted) {
            $employees = $this->getEmployeeService()->getEmployeeList('empNumber', 'ASC', true);
        }
        
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
        $ids = array();

        return $ids;         
    }

    public function getAccessibleUserRoleIds($operation, $returnType) {
        $ids = array();

        return $ids;         
    }
    
    protected function areRequiredPermissionsAvailable($requiredPermissions = array()) {
        $permitted = true;
        
        foreach ($requiredPermissions as $permissionType => $permissions) {
            if ($permissionType == BasicUserRoleManager::PERMISSION_TYPE_DATA_GROUP) {
                foreach ($permissions as $dataGroupName => $requestedResourcePermission) {
                    $dataGroupPermissions = $this->userRoleManager->getDataGroupPermissions($dataGroupName, array(), array($this->roleName));

                    if ($permitted && $requestedResourcePermission->canRead()) {
                        $permitted = $permitted && $dataGroupPermissions->canRead();
                    }

                    if ($permitted && $requestedResourcePermission->canCreate()) {
                        $permitted = $dataGroupPermissions->canCreate();
                    }

                    if ($permitted && $requestedResourcePermission->canUpdate()) {
                        $permitted = $dataGroupPermissions->canUpdate();
                    }

                    if ($permitted && $requestedResourcePermission->canDelete()) {
                        $permitted = $dataGroupPermissions->canDelete();
                    }                        
                }
            } else if ($permissionType == BasicUserRoleManager::PERMISSION_TYPE_ACTION) {
                $permitted = false;
            }
        } 
        
        return $permitted;
    }
    
}

