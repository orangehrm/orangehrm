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
    protected $projectService;
    protected $vacancyService;
    
    protected $userRoleManager;
    
    protected $employeeNumber;
    
    protected $roleName;
 
    public function __construct($roleName, $userRoleManager) {
        $this->userRoleManager = $userRoleManager;
        $this->roleName = $roleName;        
    }
    
    public function getEmployeeNumber() {
        if(empty($this->employeeNumber)) {
            $this->employeeNumber = sfContext::getInstance()->getUser()->getEmployeeNumber();
        }
        return $this->employeeNumber;
    }

    public function setEmployeeNumber($employeeNumber) {
        $this->employeeNumber = $employeeNumber;
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
    
    /**
     * Get the Project Data Access Object
     * @return ProjectService
     */
    public function getProjectService() {
        if (is_null($this->projectService)) {
            $this->projectService = new ProjectService();
        }
        return $this->projectService;
    }

    /**
     * Set Project Service Access Object
     * @param ProjectService $projectService
     * @return void
     */
    public function setProjectService(ProjectService $projectService) {
        $this->projectService = $projectService;
    }    
    
    /**
     * Get VacancyService
     * @return VacancyService
     */
    public function getVacancyService() {
        if (is_null($this->vacancyService)) {
            $this->vacancyService = new VacancyService();
        }        
        return $this->vacancyService;
    }

    /**
     * Set Vacancy Service
     * @param VacancyService $vacancyService
     */
    public function setVacancyService(VacancyService $vacancyService) {
        $this->vacancyService = $vacancyService;
    }

        
    public function getAccessibleEntities($entityType, $operation = null, $returnType = null, $requiredPermissions = array()) {

        $permitted = $this->areRequiredPermissionsAvailable($requiredPermissions);
        
        if ($permitted) {
            switch ($entityType) {
                case 'Employee':
                    $entities = $this->getAccessibleEmployees($operation, $returnType, $requiredPermissions);
                    break;
                case 'Project':
                    $entities = $this->getAccessibleProjects($operation, $returnType, $requiredPermissions);
                    break;
                case 'Vacancy':
                    $entities = $this->getAccessibleVacancies($operation, $returnType, $requiredPermissions);
                    break;                

            }
        } else {
            $entities = array();
        }
        return $entities;
    }

    public function getAccessibleEntityProperties($entityType, $properties = array(), $orderField = null, $orderBy = null, $requiredPermissions = array()) {

        $permitted = $this->areRequiredPermissionsAvailable($requiredPermissions);
        if ($permitted) {
            switch ($entityType) {
                case 'Employee':
                    $propertyList = $this->getAccessibleEmployeePropertyList($properties, $orderField, $orderBy, $requiredPermissions);
                    break;
            }
        } else {
            $propertyList = array();
        }
        return $propertyList;
    }

    public function getAccessibleEntityIds($entityType, $operation = null, $returnType = null, $requiredPermissions = array()) {   
        
        $permitted = $this->areRequiredPermissionsAvailable($requiredPermissions);
        if ($permitted) {        
            switch ($entityType) {
                case 'Employee':
                    $ids = $this->getAccessibleEmployeeIds($operation, $returnType, $requiredPermissions);                
                    break;
                case 'SystemUser':
                    $ids = $this->getAccessibleSystemUserIds($operation, $returnType, $requiredPermissions);
                    break;
                case 'OperationalCountry':
                    $ids = $this->getAccessibleOperationalCountryIds($operation, $returnType, $requiredPermissions);
                    break;
                case 'UserRole':
                    $ids = $this->getAccessibleUserRoleIds($operation, $returnType, $requiredPermissions);
                    break;
                case 'Location':
                    $ids = $this->getAccessibleLocationIds($operation, $returnType, $requiredPermissions);
                    break;
                case 'Project':
                    $ids = $this->getAccessibleProjectIds($operation, $returnType, $requiredPermissions);
                    break;            
                case 'Vacancy':
                    $ids = $this->getAccessibleVacancyIds($operation, $returnType, $requiredPermissions);
                    break;                    
            }
        } else {
            $ids = array();
        }
        return $ids;
    }
    
    public function getEmployeesWithRole($entities = array()) {
        return array();
    }    

    public function getAccessibleProjects($operation = null, $returnType = null, $requiredPermissions = array()) {
        return array();
    }
    
    public function getAccessibleProjectIds($operation = null, $returnType = null, $requiredPermissions = array()) {
        return array();
    }
    
    public function getAccessibleVacancies($operation = null, $returnType = null, $requiredPermissions = array()) {
        return array();
    }    
    
    public function getAccessibleVacancyIds($operation = null, $returnType = null, $requiredPermissions = array()) {
        return array();
    }        
    
    public abstract function getAccessibleEmployees($operation = null, $returnType = null, $requiredPermissions = array());
    
    public abstract function getAccessibleEmployeePropertyList($properties, $orderField, $orderBy, $requiredPermissions = array());
    
    public abstract function getAccessibleEmployeeIds($operation = null, $returnType = null, $requiredPermissions = array());

    public abstract function getAccessibleSystemUserIds($operation = null, $returnType = null, $requiredPermissions = array());

    public abstract function getAccessibleOperationalCountryIds($operation = null, $returnType = null, $requiredPermissions = array());

    public abstract function getAccessibleUserRoleIds($operation = null, $returnType = null, $requiredPermissions = array());

    public abstract function getAccessibleLocationIds($operation = null, $returnType = null, $requiredPermissions = array()); 
    
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
                $permitted = true;
            }
        } 
        
        return $permitted;
    }
        
}