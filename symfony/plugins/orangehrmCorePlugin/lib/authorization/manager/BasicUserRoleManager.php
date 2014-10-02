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

    const PERMISSION_TYPE_DATA_GROUP = 'data_group';
    const PERMISSION_TYPE_ACTION = 'action';
    const PERMISSION_TYPE_WORKFLOW_ACTION = 'workflow_action';
    
    const OPERATION_VIEW = 'view';
    const OPERATION_EDIT = 'edit';
    const OPERATION_DELETE = 'delete';

    protected $employeeService;
    protected $systemUserService;
    protected $screenPermissionService;
    protected $operationalCountryService;
    protected $locationService;
    protected $dataGroupService;
    protected $subordinates = null;
    protected $menuService;
    protected $projectService;
    protected $vacancyService;
    protected $homePageDao;
    
    protected $userRoleClasses;
    protected $decoratorClasses;

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
                        $this->userRoleClasses[$roleName] = new $roleObj['class']($roleName, $this);
                    }
                }
            }
        }
        
        
        foreach ($directoryIterator as $fileInfo) {
            if ($fileInfo->isDir()) {

                $pluginName = $fileInfo->getFilename();
                $configuraitonPath = $pluginsPath . '/' . $pluginName . '/config/user_role_decorator.yml';

                if (is_file($configuraitonPath)) {
                    $configuraiton = sfYaml::load($configuraitonPath);

                    if (!is_array($configuraiton)) {
                        continue;
                    }

                    foreach ($configuraiton as $roleName => $roleObj) {
                        if (!isset($this->decoratorClasses[$roleName])) {
                            $this->decoratorClasses[$roleName] = array($roleObj['class']);
                        } else {
                            $this->decoratorClasses[$roleName][] = $roleObj['class'];
                        }

                        $this->userRoleClasses[$roleName] = new $roleObj['class']($roleName, $this, $this->userRoleClasses[$roleName]);
                    }
                }
            }
        }
        
        // Get non-predefined user roles (or lazy load)
        
    }
    
    protected function getUserRoleClass($roleName) {
        
        if (isset($this->userRoleClasses[$roleName])) {
            return $this->userRoleClasses[$roleName];
        } else {        
            return null;
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
    
    public function getMenuService() {
        
        if (!$this->menuService instanceof MenuService) {
            $this->menuService = new MenuService();
        }
        
        return $this->menuService;
        
    }
    
    public function setMenuService(MenuService $menuService) {
        $this->menuService = $menuService;
    }
    
    public function getProjectService() {
        
        if (is_null($this->projectService)) {
            $this->projectService = new ProjectService();
        }
        
        return $this->projectService;
        
    }

    public function setProjectService($projectService) {
        $this->projectService = $projectService;
    }
    
    public function getVacancyService() {
        
        if (is_null($this->vacancyService)) {
            $this->vacancyService = new VacancyService();
        }
        
        return $this->vacancyService;
        
    }

    public function setVacancyService($vacancyService) {
        $this->vacancyService = $vacancyService;
    }    
    
    public function getHomePageDao() {
        if (!$this->homePageDao instanceof HomePageDao) {
            $this->homePageDao = new HomePageDao();
        }
        return $this->homePageDao;
    }

    public function setHomePageDao($homePageDao) {
        $this->homePageDao = $homePageDao;
    }    

    public function getAccessibleEntities($entityType, $operation = null, $returnType = null,
            $rolesToExclude = array(), $rolesToInclude = array(), $requiredPermissions = array()) {
        
        $allEmployees = array();

        $filteredRoles = $this->filterRoles($this->userRoles, $rolesToExclude, $rolesToInclude);

        foreach ($filteredRoles as $role) {
            $employees = array();

            $roleClass = $this->getUserRoleClass($role->getName());

            if ($roleClass) {
                $employees = $roleClass->getAccessibleEntities($entityType, $operation, $returnType, $requiredPermissions);
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
    public function getAccessibleEntityProperties($entityType, $properties = array(), 
            $orderField = null, $orderBy = null, $rolesToExclude = array(), 
            $rolesToInclude = array(), $requiredPermissions = array()) {

        $allPropertyList = array();
        $filteredRoles = $this->filterRoles($this->userRoles, $rolesToExclude, $rolesToInclude);

        foreach ($filteredRoles as $role) {
            $propertyList = array();

            $roleClass = $this->getUserRoleClass($role->getName());

            if ($roleClass) {
                $propertyList = $roleClass->getAccessibleEntityProperties($entityType, $properties, $orderField, $orderBy, $requiredPermissions);
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

    public function getAccessibleEntityIds($entityType, $operation = null, $returnType = null,
            $rolesToExclude = array(), $rolesToInclude = array(), $requiredPermissions = array()) {
    
        $allIds = array();
        $filteredRoles = $this->filterRoles($this->userRoles, $rolesToExclude, $rolesToInclude);

        foreach ($filteredRoles as $role) {
            $ids = array();
            
            $roleClass = $this->getUserRoleClass($role->getName());

            if ($roleClass) {
                $ids = $roleClass->getAccessibleEntityIds($entityType ,$operation, $returnType, $requiredPermissions);
            }

            if (count($ids) > 0) {
                $allIds = array_unique(array_merge($allIds, $ids));
            }
        }

        return $allIds;
    }
    
    /**
     * Check State Transition possible for User 
     * 
     * @param type $workFlowId
     * @param type $state
     * @param type $action
     * @return boolean 
     */
    public function isActionAllowed($workFlowId, $state, $action, $rolesToExclude = array(), $rolesToInclude = array(), $entities = array()){
        $accessFlowStateMachineService = new AccessFlowStateMachineService();
        $isAllowed = FALSE;
        
        $filteredRoles = $this->filterRoles($this->userRoles, $rolesToExclude, $rolesToInclude, $entities);
        
        foreach ($filteredRoles as $role) {
           $roleName = $this->fixUserRoleNameForWorkflowStateMachine($role->getName(), $workFlowId);
           
           $isAllowed = $accessFlowStateMachineService->isActionAllowed($workFlowId, $state, $roleName, $action);
           if($isAllowed){
               break;
           }
        }
        return $isAllowed;
    }
    
    /**
     * Get allowed Workflow action items for User
     * 
     * @param string $workflow Workflow Name
     * @param string $state Workflow state
     * @return array Array of workflow items with action name as array index 
     */
    public function getAllowedActions($workflow, $state, $rolesToExclude = array(), $rolesToInclude = array(), $entities = array()){
        $accessFlowStateMachineService = new AccessFlowStateMachineService();
        $allActions = array();
        
        $filteredRoles = $this->filterRoles($this->userRoles, $rolesToExclude, $rolesToInclude, $entities);
        
        foreach ($filteredRoles as $role) {
            $roleName = $this->fixUserRoleNameForWorkflowStateMachine($role->getName(), $workflow);
            $workFlowItems = $accessFlowStateMachineService->getAllowedWorkflowItems($workflow, $state, $roleName);     

            if (count($workFlowItems) > 0) {
                $allActions = $this->getUniqueActionsBasedOnPriority($allActions, $workFlowItems);
            }
        }
        return $allActions;
    }
    
    /**
     * Given an array of actions, returns the states for which those actions can be applied
     * by the current logged in user
     * 
     * @param string $workflow Workflow 
     * @param array $actions Array of Action names
     * @param array $rolesToExclude
     * @param array $rolesToInclude
     * @param array $entities
     * 
     * @return array Array of states
     */
    public function getActionableStates($workflow, $actions, $rolesToExclude = array(), $rolesToInclude = array(), $entities = array()) {

        $accessFlowStateMachineService = new AccessFlowStateMachineService();
        $actionableStates = array();
        
        $filteredRoles = $this->filterRoles($this->userRoles, $rolesToExclude, $rolesToInclude, $entities);
        
        foreach ($filteredRoles as $role) {   
            $roleName = $this->fixUserRoleNameForWorkflowStateMachine($role->getName(), $workflow);
            $states = $accessFlowStateMachineService->getActionableStates($workflow, $roleName, $actions); 

            if (!empty($states)) {
                $actionableStates = array_unique(array_merge($actionableStates, $states));
            }
        }
        return $actionableStates;
    }
    
    protected function getUniqueActionsBasedOnPriority($currentItems, $itemsToMerge) {
        
        foreach($itemsToMerge as $item) {
            $actionName = $item->getAction();
            if (!isset($currentItems[$actionName])) {
                $currentItems[$actionName] = $item;
            } else {                
                $existing = $currentItems[$actionName];
                
                if ($item->getPriority() > $existing->getPriority()) {
                    $currentItems[$actionName] = $item;
                }
            }
        }
        
        return $currentItems;        
    }
    

    public function isEntityAccessible($entityType, $entityId, $operation = null, 
            $rolesToExclude = array(), $rolesToInclude = array(), $requiredPermissions = array()) {

        $entityIds = $this->getAccessibleEntityIds($entityType, $operation, null, 
                $rolesToExclude, $rolesToInclude, $requiredPermissions);
        
        $accessible = in_array($entityId, $entityIds);

        return $accessible;
    }

    
    public function areEntitiesAccessible($entityType, $entityIds, $operation = null, 
            $rolesToExclude = array(), $rolesToInclude = array(), $requiredPermissions = array()) {
        
        $accessibleIds = $this->getAccessibleEntityIds($entityType, $operation, 
                null, $rolesToExclude, $rolesToInclude, $requiredPermissions);

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
    
    public function getEmployeesWithRole($roleName, $entities = array()) {
        
        $employees = array();
        $roleClass = $this->getUserRoleClass($roleName);
        if (!empty($roleClass)) {
            $employees = $roleClass->getEmployeesWithRole($entities);
        }
        
        return $employees;
    }

    public function getAccessibleModules() {
        
    }
    
    public function getAccessibleMenuItemDetails() {
        
        return $this->getMenuService()->getMenuItemDetails($this->userRoles);
        
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
            
            if ($user->getUserRole()->getName() != 'ESS') {
                $roles[] = $this->getSystemUserService()->getUserRole('ESS');
            }
            
            if ($this->isProjectAdmin($empNumber)) {
                $roles[] = $this->getSystemUserService()->getUserRole('ProjectAdmin');
            }
            
            if ($this->isHiringManager($empNumber)) {
                $roles[] = $this->getSystemUserService()->getUserRole('HiringManager');
            }
            
            if ($this->isInterviewer($empNumber)) {
                $roles[] = $this->getSystemUserService()->getUserRole('Interviewer');
            }            
            
            if ($this->getEmployeeService()->isSupervisor($empNumber)) {
                $supervisorRole = $this->getSystemUserService()->getUserRole('Supervisor');
                if (!empty($supervisorRole)) {
                    $roles[] = $supervisorRole;
                }
            }                        
            
        }
        
        
        return $roles;
    }    
    

    protected function areRequiredPermissionsAvailable($role, $requiredPermissions = array()) {
        $permitted = true;
        
        foreach ($requiredPermissions as $permissionType => $permissions) {
            if ($permissionType == self::PERMISSION_TYPE_DATA_GROUP) {
                foreach ($permissions as $dataGroupName => $requestedResourcePermission) {
                    $dataGroupPermissions = $this->getDataGroupPermissions($dataGroupName, array(), array($role->getName()));

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
            }
        } 
        
        return $permitted;
    }
    
    protected function mergeEmployees($empList1, $empList2) {

        foreach ($empList2 as $id => $emp) {
            if (!isset($empList1[$id])) {
                $empList1[$id] = $emp;
            }
        }
        return $empList1;
    }
    
    /**
     * Filter the given $userRoles array according to the given parameters
     * 
     * @param Array $userRoles Array of UserRole objects
     * @param Array $rolesToExclude Array of User role names to exclude. These user roles will be removed from $userRoles
     * @param Array $rolesToInclude Array of User role names to include. If not empty, only these user roles will be included.
     * @param Array $entities Array of details relevent to deciding if a particular user role applies to this 
     * @return Array $userRoles array filtered as described above.
     */
    protected function filterRoles($userRoles, $rolesToExclude, $rolesToInclude, $entities = array()) {

        
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

        $temp = array();
        
        if (!empty($entities)) {
            foreach ($userRoles as $role) {
                
                $include = true;
                
                if ($role->getName() == 'Supervisor') {
                                    
                    // If Employee entitiy is given, supervisor role will only 
                    // apply if current employee is the supervisor for the given employee
                    if (isset($entities['Employee'])) {
                        if (!$this->isSupervisorFor($entities['Employee'])) {
                            $include = false;
                        }
                    }
                } else if ($role->getName() == 'ESS') {
                    
                    // If Employee entity is given, the ESS role will only apply
                    // If current logged in employee is the same as the passed entity.
                    if (isset($entities['Employee'])) {
                        if ($this->user->getEmpNumber() != $entities['Employee']) {
                            $include = false;
                        }
                    }
                }
                
                if ($include) {
                    $temp[] = $role;
                }
            }
            
            $userRoles = $temp;
        }
        
        return $userRoles;
    }
    
    protected function isSupervisorFor($empNumber) {

        if (is_null($this->subordinates)) {
            $this->subordinates = $this->getEmployeeService()->getSubordinateIdListBySupervisorId($this->user->getEmpNumber());
        }
        
        if (is_array($this->subordinates) && in_array($empNumber, $this->subordinates)) {
            return true;
        }
               
        return false;
    }
    
    protected function isProjectAdmin($empNumber) {
        return $this->getProjectService()->isProjectAdmin($empNumber);
    }
    
    private function isHiringManager($empNumber) {
        return $this->getVacancyService()->isHiringManager($empNumber);
    }
    
    private function isInterviewer($empNumber) {
        return $this->getVacancyService()->isInterviewer($empNumber);
    }    

    public function getDataGroupService() {
         if (empty($this->dataGroupService)) {
            $this->dataGroupService = new DataGroupService();
            $this->dataGroupService->setDao(new DataGroupDao());
            return $this->dataGroupService;
        }        
        return $this->dataGroupService;
    }

    public function setDataGroupService($dataGroupService) {
        $this->dataGroupService = $dataGroupService;
    }

    
    /**
     * Get user roles
     * for each user role, 
     * get data group permissions - if permissions not defined, should return object with all rights set to false.
     * merge the permissions
     * return merged data group permission object.
     * 
     * For testing, move service object into member variable.
     * 
     * @param type $dataGroupName
     * @param type $userRoleId 
     * 
     * @return ResourcePermission
     */
    public function getDataGroupPermissions ($dataGroupName, $rolesToExclude = array(), $rolesToInclude = array(), $selfPermission = false, $entities = array()) {
        
        $filteredRoles = $this->filterRoles($this->userRoles, $rolesToExclude, $rolesToInclude, $entities); 
              
        $finalPermission = array('read'=> false, 'create'=> false,'update'=> false,'delete'=> false);
        
        foreach ($filteredRoles as $role){ 
            $userRoleId = $role->getId();           
            $permissions = $this->getDataGroupService()->getDataGroupPermission($dataGroupName, $userRoleId, $selfPermission);
            
            foreach ($permissions as $permission ){           

                if($permission->getCanRead()){
                    $finalPermission ['read'] = true;
                }

                if($permission->getCanCreate()){
                    $finalPermission ['create'] = true;
                }

                if($permission->getCanUpdate()){
                    $finalPermission ['update'] = true;
                }

                if($permission->getCanDelete()){
                    $finalPermission ['delete'] = true;
                }
            }
        }
        
        $resourcePermission = new ResourcePermission( $finalPermission ['read'], $finalPermission ['create'], $finalPermission ['update'], $finalPermission ['delete']);
        return $resourcePermission;
    }
    
    public function getModuleDefaultPage($module) {
        $action = NULL;
        
        $userRoleIds = array();
        foreach ($this->userRoles as $role) {
            $userRoleIds[] = $role->getId();
        }
        $defaultPages = $this->getHomePageDao()->getModuleDefaultPagesInPriorityOrder($module, $userRoleIds);

        foreach ($defaultPages as $defaultPage) {
            $enabled = true;
            $enableClass = $defaultPage->getEnableClass();

            if (!empty($enableClass) && class_exists($enableClass)) {
                $enableClassInstance = new $enableClass();
                if ($enableClassInstance instanceof HomePageEnablerInterface) {
                    $enabled = $enableClassInstance->isEnabled($this->getUser());
                }
            }
            
            if ($enabled) {
                $action = $defaultPage->getAction();
                break;
            }
        }
        
        return $action;        
    }
    
    public function getHomePage() {
        $action = NULL;
        
        $userRoleIds = array();
        foreach ($this->userRoles as $role) {
            $userRoleIds[] = $role->getId();
        }
        $defaultPages = $this->getHomePageDao()->getHomePagesInPriorityOrder($userRoleIds);
        
        foreach ($defaultPages as $defaultPage) {
            $enabled = true;
            $enableClass = $defaultPage->getEnableClass();
            if (!empty($enableClass) && class_exists($enableClass)) {
                $enableClassInstance = new $enableClass();
                if ($enableClassInstance instanceof HomePageEnablerInterface) {
                    $enabled = $enableClassInstance->isEnabled($this->getUser());
                }
            }
            if ($enabled) {
                $action = $defaultPage->getAction();
                break;
            }
        }
        
        return $action;        
    }    
    
    protected function fixUserRoleNameForWorkflowStateMachine($roleName, $workflow) {
        $fixedName = $roleName;
        if ($roleName == 'ESS' && $workflow != WorkflowStateMachine::FLOW_LEAVE) {
            $fixedName = 'ESS User';
        } else if ($roleName == 'HiringManager' && $workflow == WorkflowStateMachine::FLOW_RECRUITMENT) {
            $fixedName = 'HIRING MANAGER';
        }

        return $fixedName;
    }
}
