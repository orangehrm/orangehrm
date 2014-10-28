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
 * Service used to generate left menu for PIM
 */
class PIMLeftMenuService {
    
    const PIM_LEFTMENU_SESSION_KEY = 'pim.leftMenu.cache';
    const PIM_LEFTMENU_TAXMENU_ENABLED = 'pim.leftMenu.isTaxMenuEnabled';
    
    private $user;    
    private $employeeService;
    private $userRoleManager;
    
    private $availableActions = array(
        'viewPersonalDetails' => array(
            'module' => 'pim',
            'data_groups' => array('personal_information', 'personal_attachment', 'personal_custom_fields'),
            'label' => "Personal Details"),
        'contactDetails' => array(
            'module' => 'pim',
            'data_groups' => array('contact_details', 'contact_attachment', 'contact_custom_fields'),
            'label' => 'Contact Details'),
        'viewEmergencyContacts' => array(
            'module' => 'pim',
            'data_groups' => array('emergency_contacts', 'emergency_attachment', 'emergency_custom_fields'),
            'label' => 'Emergency Contacts'),
        'viewDependents' => array(
            'module' => 'pim',
            'data_groups' => array('dependents', 'dependents_attachment', 'dependents_custom_fields'),
            'label' => 'Dependents'),
        'viewImmigration' => array(
            'module' => 'pim',
            'data_groups' => array('immigration', 'immigration_attachment', 'immigration_custom_fields'),
            'label' => 'Immigration'),
        'viewJobDetails' => array(
            'module' => 'pim',
            'data_groups' => array('job_details', 'job_attachment', 'job_custom_fields'),
            'label' => 'Job'),
        'viewSalaryList' => array(
            'module' => 'pim',
            'data_groups' => array('salary_details', 'salary_attachment', 'salary_custom_fields'),
            'label' => 'Salary'),
        'viewUsTaxExemptions' => array(
            'module' => 'pim',
            'data_groups' => array('tax_exemptions', 'tax_attachment', 'tax_custom_fields'),
            'label' => 'Tax Exemptions'),
        'viewReportToDetails' => array(
            'module' => 'pim',
            'data_groups' => array('supervisor', 'subordinates', 'report-to_attachment', 'report-to_custom_fields'),
            'actions' => array(),
            'label' => 'Report-to'),
        'viewQualifications' => array(
            'module' => 'pim',
            'data_groups' => array('qualification_work', 'qualification_education', 'qualification_skills', 'qualification_languages', 'qualification_license', 'qualifications_attachment', 'qualifications_custom_fields'),
            'label' => 'Qualifications'),
        'viewMemberships' => array(
            'module' => 'pim',
            'data_groups' => array('membership', 'membership_attachment', 'membership_custom_fields'),
            'label' => 'Memberships')
    );    

    /**
     * Get EmployeeService
     * @returns EmployeeService
     */
    public function getEmployeeService() {
        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
    }

    /**
     * Set EmployeeService
     * @param EmployeeService $employeeService
     */
    public function setEmployeeService(EmployeeService $employeeService) {
        $this->employeeService = $employeeService;
    }    
    
    /**
     * Get UserRoleManager
     * @returns AbstractUserRoleManager
     */
    public function getUserRoleManager() {
        if (is_null($this->userRoleManager)) {
            $this->userRoleManager = UserRoleManagerFactory::getUserRoleManager();;
        }
        return $this->userRoleManager;
    }

    /**
     * Set UserRoleManager
     * @param AbstractUserRoleManager $userRoleManager
     */
    public function setUserRoleManager(AbstractUserRoleManager $userRoleManager) {
        $this->userRoleManager = $userRoleManager;
    }    
    
    /**
     * Get symfony's sfUser representing the logged in user
     * 
     * @return sfUser
     */
    public function getUser() {
        if (empty($this->user)) {
            $this->user = sfContext::getInstance()->getUser();
        }
        return $this->user;
    }

    /**
     * Set symfony sfUser
     * 
     * @param sfUser $user
     */
    public function setUser($user) {
        $this->user = $user;
    }
    
    /**
     * Returns PIM left menu items in when looking at the given employee.
     * 
     * @param int $empNumber Employee Number
     * @param int $self If true, indicates menu when user is looking at his own info
     * @return array Array of menu items.
     */
    public function getMenuItems($empNumber, $self) {

        $menu = $this->getMenuFromCache($empNumber, $self);
        
        if (empty($menu)) {            
            $menu = $this->generateMenuItems($empNumber, $self);               
            $this->saveMenuInCache($empNumber, $menu);
        } 
        return $menu;
    }
    
        
    /**
     * Clears cached PIM menu for given employee
     * 
     * If employee is null, all cached menu items are cleared.
     * 
     * @param int $empNumber Employee Number (or null)
     */
    public function clearCachedMenu($empNumber = null) {
        $user = $this->getUser();
        $cache = $user->getAttribute(self::PIM_LEFTMENU_SESSION_KEY, array());
        if (empty($empNumber)) {
            $cache = array();
        } else {
            unset($cache[$empNumber]);
        }
        
        $user->setAttribute(self::PIM_LEFTMENU_SESSION_KEY, $cache);       
    }      
    
    public function isPimAccessible($empNumber, $self) {
        $menu = $this->getMenuItems($empNumber, $self);
        
        return count($menu) > 0;        
    }

    protected function generateMenuItems($empNumber, $self) {

        $menu = array();
        $entities = array();
        
        if (!empty($empNumber)) {
            $entities = array('Employee' => $empNumber);
        }        

        $availableActions = $this->getAvailableActions();
        
        $userRoleManager = $this->getUserRoleManager();
        
        foreach ($availableActions as $action => $properties) {
            $dataGroupPermission = $userRoleManager->getDataGroupPermissions($properties['data_groups'], array(), array(), $self, $entities);
            if ($dataGroupPermission->canRead()) {
                $menu[$action] = $properties;
            } else if ($action == 'viewJobDetails' && $this->isEmployeeWorkflowActionsAllowed($empNumber)) {
                $menu[$action] = $properties;
            }
        }
        
        return $menu;
    }
    
    protected function isEmployeeWorkflowActionsAllowed($empNumber) {
        
        $userRoleManager = $this->getUserRoleManager();

        $employeeState = Null;
        
        if (!empty($empNumber)) {
            $employee = $this->getEmployeeService()->getEmployee($empNumber);
            if ($employee instanceof Employee) {
                $employeeState = $employee->getState();
            }
        }
        
        $actionableStates = $userRoleManager->getActionableStates(WorkflowStateMachine::FLOW_EMPLOYEE, 
                array(WorkflowStateMachine::EMPLOYEE_ACTION_TERMINATE, 
                    WorkflowStateMachine::EMPLOYEE_ACTION_REACTIVE));
                
        // If employee state not allowed, allow if can act on at least one state
        if (is_null($employeeState)) {
            $allowed = count($actionableStates) > 0;
        } else {
            $allowed = in_array($employeeState, $actionableStates);
        }  
        
        return $allowed;
    }
    
    /**
     * Get PIM left menu for given employee from session cache (if available)
     * 
     * @param int $empNumber Employee Number
     * @return array Menu array (or an empty array if not available in cache)
     */
    protected function getMenuFromCache($empNumber) {        
        $user = $this->getUser();
        $cache = $user->getAttribute(self::PIM_LEFTMENU_SESSION_KEY, array());
        $key = empty($empNumber) ? 'default' : $empNumber;
        $menu = isset($cache[$key]) ? $cache[$key] : array();
        
        return $menu;
    }
    
    /**
     * Store menu for the given employee in the session cache.
     * 
     * @param int $empNumber Employee Number
     * @param array $menu Menu array
     */
    protected function saveMenuInCache($empNumber, $menu) {
        $user = $this->getUser();
        $cache = $user->getAttribute(self::PIM_LEFTMENU_SESSION_KEY, array());
        $key = empty($empNumber) ? 'default' : $empNumber;
        $cache[$key] = $menu;
        $user->setAttribute(self::PIM_LEFTMENU_SESSION_KEY, $cache);
    }
    
    protected function getAvailableActions() {
        $availableActions = $this->availableActions;
        if (!$this->isTaxMenuEnabled()) {
            unset($availableActions['viewUsTaxExemptions']);
        }

        return $availableActions;
    }  
    
    /**
     * Check if tax menu is enabled
     * 
     * @return boolean true if enabled, false if not
     */
    protected function isTaxMenuEnabled() {

        $sfUser = $this->getUser();

        if (!$sfUser->hasAttribute(self::PIM_LEFTMENU_TAXMENU_ENABLED)) {
            $isTaxMenuEnabled = OrangeConfig::getInstance()->getAppConfValue(ConfigService::KEY_PIM_SHOW_TAX_EXEMPTIONS);
            $sfUser->setAttribute(self::PIM_LEFTMENU_TAXMENU_ENABLED, $isTaxMenuEnabled);
        }

        return $sfUser->getAttribute(self::PIM_LEFTMENU_TAXMENU_ENABLED);
    }
}

