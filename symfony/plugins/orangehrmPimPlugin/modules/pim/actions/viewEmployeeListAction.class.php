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
 * View employee list action
 */
class viewEmployeeListAction extends basePimAction {

    protected $leftMenuService;
    
    /**
     * Index action. Displays employee list
     *      `
     * @param sfWebRequest $request
     */
    public function execute($request) {
      
        if ($this->getUser()->hasFlash('templateMessage')) {
            list($this->messageType, $this->message) = $this->getUser()->getFlash('templateMessage');
        }
        
        $empNumber = $request->getParameter('empNumber');
        $isPaging = $request->getParameter('hdnAction') == 'search' ? 1 : $request->getParameter('pageNo', 1);

        $pageNumber = $isPaging;
        if (!empty($empNumber) && $this->getUser()->hasAttribute('pageNumber')) {
            $pageNumber = $this->getUser()->getAttribute('pageNumber');
        }
                
        $noOfRecords = sfConfig::get('app_items_per_page');

        $offset = ($pageNumber >= 1) ? (($pageNumber - 1) * $noOfRecords) : ($request->getParameter('pageNo', 1) - 1) * $noOfRecords;

         // Reset filters if requested to
        if ($request->hasParameter('reset')) {
            $this->setFilters(array());
            $this->setSortParameter(array("field"=> NULL, "order"=> NULL));
            $this->setPage(1);
        }

        $this->form = new EmployeeSearchForm($this->getFilters());
        if ($request->isMethod('post')) {

            $this->form->bind($request->getParameter($this->form->getName()));

            if ($this->form->isValid()) {
                
                if($this->form->getValue('isSubmitted')=='yes'){
                    $this->setSortParameter(array("field"=> NULL, "order"=> NULL));
                }         
                
                $this->setFilters($this->form->getValues());
                
            } else {
                $this->setFilters(array());
            }

            $this->setPage(1);
        }
        
        if ($request->isMethod('get')) {
            $sortParam = array("field"=>$request->getParameter('sortField'), 
                               "order"=>$request->getParameter('sortOrder'));
            $this->setSortParameter($sortParam);
            $this->setPage(1);
        }
        
        $sort = $this->getSortParameter();
        $sortField = $sort["field"];
        $sortOrder = $sort["order"];
        $filters = $this->getFilters();
        
        if( isset(  $filters['employee_name'])){
            $filters['employee_name'] = str_replace(' (' . __('Past Employee') . ')', '', $filters['employee_name']['empName']);
        }
        
        if (isset($filters['supervisor_name'])) {
            $filters['supervisor_name'] = str_replace(' (' . __('Past Employee') . ')', '', $filters['supervisor_name']);
        }
        
        $this->filterApply = !empty($filters);

        $accessibleEmployees = UserRoleManagerFactory::getUserRoleManager()->getAccessibleEntityIds('Employee');

        if (count($accessibleEmployees) > 0) {
            $filters['employee_id_list'] = $accessibleEmployees;
            $count = $this->getEmployeeService()->getSearchEmployeeCount( $filters );
            
            $parameterHolder = new EmployeeSearchParameterHolder();
            $parameterHolder->setOrderField($sortField);
            $parameterHolder->setOrderBy($sortOrder);
            $parameterHolder->setLimit($noOfRecords);
            $parameterHolder->setOffset($offset);
            $parameterHolder->setFilters($filters);

            $list = $this->getEmployeeService()->searchEmployees($parameterHolder);
            
        } else {
            $count = 0;
            $list = array();
        }

        $this->setListComponent($list, $count, $noOfRecords, $pageNumber);

        // Show message if list is empty, and we don't already have a message.
        if (empty($this->message) && (count($list) == 0)) {

            // Check to see if we have any employees in system
            $employeeCount = $this->getEmployeeService()->getEmployeeCount();
            $this->messageType = "warning";

            if (empty($employeeCount)) {
                $this->message = __("No Employees Available");
            } else {
                $this->message = __(TopLevelMessages::NO_RECORDS_FOUND);
            }

        }
    }
    
    protected function setListComponent($employeeList, $count, $noOfRecords, $page) {
        
        $configurationFactory = $this->getListConfigurationFactory();

        $runtimeDefinitions = array();
        $buttons = array();
        
        $allowedToAddEmployee = $this->getContext()->getUserRoleManager()->isActionAllowed(PluginWorkflowStateMachine::FLOW_EMPLOYEE, 
            Employee::STATE_NOT_EXIST, PluginWorkflowStateMachine::EMPLOYEE_ACTION_ADD);

        if ($allowedToAddEmployee) {
            $buttons['Add'] = array('label' => 'Add');
        }            
                            
        $deleteActiveEmployee = $this->getContext()->getUserRoleManager()->isActionAllowed(PluginWorkflowStateMachine::FLOW_EMPLOYEE, 
            Employee::STATE_ACTIVE, PluginWorkflowStateMachine::EMPLOYEE_ACTION_DELETE_ACTIVE);

        $deleteTerminatedEmployee = $this->getContext()->getUserRoleManager()->isActionAllowed(PluginWorkflowStateMachine::FLOW_EMPLOYEE, 
            Employee::STATE_TERMINATED, PluginWorkflowStateMachine::EMPLOYEE_ACTION_DELETE_TERMINATED);

        if ($deleteActiveEmployee || $deleteTerminatedEmployee) {
            $buttons['Delete'] = array('label' => 'Delete', 
                                        'type' => 'submit', 
                                        'data-toggle' => 'modal', 
                                        'data-target' => '#deleteConfModal',
                                        'class' => 'delete');
        } else {
            $runtimeDefinitions['hasSelectableRows'] = false;            
        }

        $unDeletableIds = $this->getUndeletableEmpNumbers();
        if (count($unDeletableIds) > 0) {
            $runtimeDefinitions['unselectableRowIds'] = $unDeletableIds;
        }
        
        $runtimeDefinitions['buttons'] = $buttons;
        $configurationFactory->setRuntimeDefinitions($runtimeDefinitions);
        
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        
        ohrmListComponent::setActivePlugin('orangehrmPimPlugin');
        ohrmListComponent::setListData($employeeList);
        ohrmListComponent::setItemsPerPage($noOfRecords);
        ohrmListComponent::setNumberOfRecords($count);      
        ohrmListComponent::setPageNumber($page);
    }
    
    protected function getListConfigurationFactory() {
        $isPimAccessible = $this->getLeftMenuService()->isPimAccessible(null, false);
        $linkSetting = $isPimAccessible ? EmployeeListConfigurationFactory::LINK_ALL_EMPLOYEES : EmployeeListConfigurationFactory::LINK_NONE;
        EmployeeListConfigurationFactory::setLinkSetting($linkSetting);
        
        $configurationFactory = new EmployeeListConfigurationFactory();        

        return $configurationFactory;
    }

    /**
     * Set's the current page number in the user session.
     * @param $page int Page Number
     * @return None
     */
    protected function setPage($page) {
        $this->getUser()->setAttribute('emplist.page', $page, 'pim_module');
    }

    /**
     * Get the current page number from the user session.
     * @return int Page number
     */
    protected function getPage() {
        return $this->getUser()->getAttribute('emplist.page', 1, 'pim_module');
    }
    
    /**
     * Sets the current sort field and order in the user session.
     * @param type Array $sort 
     */
    protected function setSortParameter($sort) {
        $this->getUser()->setAttribute('emplist.sort', $sort, 'pim_module');
    }

    /**
     * Get the current sort feild&order from the user session.
     * @return array ('field' , 'order')
     */
    protected function getSortParameter() {
        return $this->getUser()->getAttribute('emplist.sort', null, 'pim_module');
    }
    
    /**
     *
     * @param array $filters
     * @return unknown_type
     */
    protected function setFilters(array $filters) {
        return $this->getUser()->setAttribute('emplist.filters', $filters, 'pim_module');
    }

    /**
     *
     * @return unknown_type
     */
    protected function getFilters() {
        return $this->getUser()->getAttribute('emplist.filters', null, 'pim_module');
    }

    protected function _getFilterValue($filters, $parameter, $default = null) {
        $value = $default;
        if (isset($filters[$parameter])) {
            $value = $filters[$parameter];
        }

        return $value;
    }

    /**
     * Get PIMLeftMenuService
     * @returns PIMLeftMenuService
     */
    public function getLeftMenuService() {
        if (is_null($this->leftMenuService)) {
            $this->leftMenuService = new PIMLeftMenuService();
        }
        return $this->leftMenuService;
    }

    /**
     * Set PIMLeftMenuService
     * @param PIMLeftMenuService $leftMenuService
     */
    public function setLeftMenuService(PIMLeftMenuService $leftMenuService) {
        $this->leftMenuService = $leftMenuService;
    }    
    
    public function getUndeletableEmpNumbers() {
        return array();
    }
}
