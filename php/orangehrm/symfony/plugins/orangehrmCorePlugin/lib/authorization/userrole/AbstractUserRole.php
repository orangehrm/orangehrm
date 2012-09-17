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
 * Description of UserRoleInterface
 *
 * @author Chameera Senarathna
 */
abstract class AbstractUserRole {
    
    protected $employeeService;
    protected $systemUserService;
    protected $operationalCountryService;
    protected $locationService;
    
    protected $userRoleManager;
    
    protected $roleName;
 
    public function __construct($roleName, $userRoleManager) {
        $this->userRoleManager = $userRoleManager;
        $this->roleName = $roleName;        
    }

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
       
    public function getAccessibleEntities($entityType, $operation = null, $returnType = null, $requiredPermissions = array()) {

        switch ($entityType) {
            case 'Employee':
                $entities = $this->getAccessibleEmployees($operation, $returnType, $requiredPermissions);
                break;
        }
        return $entities;
    }

    public function getAccessibleEntityProperties($entityType, $properties = array(), $orderField = null, $orderBy = null, $requiredPermissions = array()) {

        switch ($entityType) {
            case 'Employee':
                $propertyList = $this->getAccessibleEmployeePropertyList($properties, $orderField, $orderBy, $requiredPermissions);
                break;
        }
        return $propertyList;
    }

    public function getAccessibleEntityIds($entityType, $operation = null, $returnType = null, $requiredPermissions = array()) {   
        
        switch ($entityType) {
            case 'Employee':
                $ids = $this->getAccessibleEmployeeIds($operation, $returnType, $requiredPermissions);                
                break;
            case 'SystemUser':
                $ids = $this->getAccessibleSystemUserIds($operation, $returnType);
                break;
            case 'OperationalCountry':
                $ids = $this->getAccessibleOperationalCountryIds($operation, $returnType);
                break;
            case 'UserRole':
                $ids = $this->getAccessibleUserRoleIds($operation, $returnType);
                break;
            case 'Location':
                $ids = $this->getAccessibleLocationIds($operation, $returnType);
                break;
        }
        return $ids;
    }

    public abstract function getAccessibleEmployees($operation = null, $returnType = null, $requiredPermissions = array());
    
    public abstract function getAccessibleEmployeePropertyList($properties, $orderField, $orderBy, $requiredPermissions = array());
    
    public abstract function getAccessibleEmployeeIds($operation, $returnType, $requiredPermissions = array());

    public abstract function getAccessibleSystemUserIds($operation, $returnType);

    public abstract function getAccessibleOperationalCountryIds($operation, $returnType);

    public abstract function getAccessibleUserRoleIds($operation, $returnType);

    public abstract function getAccessibleLocationIds($operation, $returnType);    
}