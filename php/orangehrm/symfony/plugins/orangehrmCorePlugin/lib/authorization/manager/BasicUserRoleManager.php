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
 * Description of BasicUserRoleManager
 *
 */
class BasicUserRoleManager extends AbstractUserRoleManager {

    protected $employeeService;
    protected $systemUserService;
    protected $screenPermissionService;
    protected $operationalCountryService;
    protected $locationService;
    protected $userRoleClasses;

    public function __construct() {
        $this->_init();
    }

    private function _init() {

        $pluginsPath = sfConfig::get('sf_plugins_dir');
        $directoryIterator = new DirectoryIterator($pluginsPath);
        foreach ($directoryIterator as $fileInfo) {
            if ($fileInfo->isDir()) {

                $pluginName = $fileInfo->getFilename();
                $configuraitonPath = $pluginsPath . '/' . $pluginName . '/config/user_role.yml';

                if (is_file($configuraitonPath)) {
                    $configuraiton = sfYaml::load($configuraitonPath);

                    if (!is_array($configuraiton)) {
                        continue;
                    }

                    foreach ($configuraiton as $roleName => $roleObj) {
                        $this->userRoleClasses[$roleName] = new $roleObj['class'];
                    }
                }
            }
        }
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

    public function getScreenPermissionService() {
        if (empty($this->screenPermissionService)) {
            $this->screenPermissionService = new ScreenPermissionService();
        }
        return $this->screenPermissionService;
    }

    public function setScreenPermissionService($screenPermissionService) {
        $this->screenPermissionService = $screenPermissionService;
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

    public function getAccessibleEntities($entityType, $operation = null, $returnType = null, $rolesToExclude = array(), $rolesToInclude = array()) {

        $allEmployees = array();

        $filteredRoles = $this->filterRoles($this->userRoles, $rolesToExclude, $rolesToInclude);

        foreach ($filteredRoles as $role) {
            $employees = array();

            $roleClass = $this->userRoleClasses["$role"];

            if ($roleClass) {
                switch ($entityType) {
                    case 'Employee':
                        $employees = $roleClass->getAccessibleEmployees($operation, $returnType);
                        break;
                }
            }

            if (count($employees) > 0) {
                $allEmployees = $this->mergeEmployees($allEmployees, $employees);
            }
        }

        return $allEmployees;
    }

    /**
     * Get Properties of Accessible Entities
     * @param $entityType Entity Type
     * @parm $properties Properties of the entity which should return
     */
    public function getAccessibleEntityProperties($entityType, $properties = array(), $orderField = null, $orderBy = null, $rolesToExclude = array(), $rolesToInclude = array()) {
        $allPropertyList = array();
        $filteredRoles = $this->filterRoles($this->userRoles, $rolesToExclude, $rolesToInclude);

        foreach ($filteredRoles as $role) {
            $propertyList = array();

            $roleClass = $this->userRoleClasses["$role"];

            if ($roleClass) {
                switch ($entityType) {
                    case 'Employee':
                        $propertyList = $roleClass->getAccessibleEmployeePropertyList($properties, $orderField, $orderBy);
                        break;
                }
            }

            if (count($propertyList) > 0) {
                foreach ($propertyList as $property) {
                    $allPropertyList[$property['empNumber']] = $property;
                }
            }
        }

        return $allPropertyList;
    }

    /**
     * TODO: 'locations', 'system users', 'operational countries', 
     *       'user role' (only ess for regional admin),
     * 
     * @param type $entityType
     * @param type $operation
     * @param type $returnType
     * @return type 
     */
    public function getAccessibleEntityIds($entityType, $operation = null, $returnType = null, $rolesToExclude = array(), $rolesToInclude = array()) {

        $allIds = array();
        $filteredRoles = $this->filterRoles($this->userRoles, $rolesToExclude, $rolesToInclude);

        foreach ($filteredRoles as $role) {
            $ids = array();
            
            $roleClass = $this->userRoleClasses["$role"];

            if ($roleClass) {

            switch ($entityType) {
                case 'Employee':
                    $ids = $roleClass->getAccessibleEmployeeIds($operation, $returnType);
                    break;
                case 'SystemUser':
                    $ids = $roleClass->getAccessibleSystemUserIds($operation, $returnType);
                    break;
                case 'OperationalCountry':
                    $ids = $roleClass->getAccessibleOperationalCountryIds($operation, $returnType);
                    break;
                case 'UserRole':
                    $ids = $roleClass->getAccessibleUserRoleIds($operation, $returnType);
                    break;
                case 'Location':
                    $ids = $roleClass->getAccessibleLocationIds($operation, $returnType);
                    break;
            }
            }

            if (count($ids) > 0) {
                $allIds = array_unique(array_merge($allIds, $ids));
            }
        }

        return $allIds;
    }

    public function isEntityAccessible($entityType, $entityId, $operation = null, $rolesToExclude = array(), $rolesToInclude = array()) {
        $entityIds = $this->getAccessibleEntityIds($entityType, $operation, null, $rolesToExclude, $rolesToInclude);

        $accessible = in_array($entityId, $entityIds);

        return $accessible;
    }

    public function areEntitiesAccessible($entityType, $entityIds, $operation = null, $rolesToExclude = array(), $rolesToInclude = array()) {
        $accessibleIds = $this->getAccessibleEntityIds($entityType, $operation, null, $rolesToExclude, $rolesToInclude);
        $intersection = array_intersect($accessibleIds, $entityIds);

        $accessible = false;

        if (count($entityIds) == count($intersection)) {
            $diff = array_diff($entityIds, $intersection);
            if (count($diff) == 0) {
                $accessible = true;
            }
        }

        return $accessible;
    }

    public function getAccessibleModules() {
        
    }

    public function isModuleAccessible($module) {
        
    }

    public function isScreenAccessible($module, $screen, $field) {
        
    }

    public function isFieldAccessible($module, $screen, $field) {
        
    }

    public function getScreenPermissions($module, $action) {
        $permissions = $this->getScreenPermissionService()->getScreenPermissions($module, $action, $this->userRoles);

        return $permissions;
    }

    protected function getUserRoles(SystemUser $user) {

        $user = $this->getSystemUserService()->getSystemUser($user->id);

        $roles = array($user->getUserRole());

        // Check for supervisor:
        $empNumber = $user->getEmpNumber();
        if (!empty($empNumber)) {
            if ($this->getEmployeeService()->isSupervisor($empNumber)) {
                $supervisorRole = $this->getSystemUserService()->getUserRole('Supervisor');
                if (!empty($supervisorRole)) {
                    $roles[] = $supervisorRole;
                }
            }
        }


        return $roles;
    }

    protected function mergeEmployees($empList1, $empList2) {

        foreach ($empList2 as $id => $emp) {
            if (!isset($empList1[$id])) {
                $empList1[$id] = $emp;
            }
        }
        return $empList1;
    }

    protected function filterRoles($userRoles, $rolesToExclude, $rolesToInclude) {

        if (!empty($rolesToExclude)) {

            $temp = array();

            foreach ($userRoles as $role) {
                if (!in_array($role->getName(), $rolesToExclude)) {
                    $temp[] = $role;
                }
            }

            $userRoles = $temp;
        }

        if (!empty($rolesToInclude)) {
            $temp = array();

            foreach ($userRoles as $role) {
                if (in_array($role->getName(), $rolesToInclude)) {
                    $temp[] = $role;
                }
            }

            $userRoles = $temp;
        }

        return $userRoles;
    }

}