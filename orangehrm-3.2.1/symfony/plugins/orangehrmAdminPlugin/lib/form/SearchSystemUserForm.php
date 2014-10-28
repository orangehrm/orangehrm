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
 *
 */
class SearchSystemUserForm extends BaseForm {

    private $systemUserService;

    public function getSystemUserService() {
        $this->systemUserService = new SystemUserService();
        return $this->systemUserService;
    }

    public function configure() {

        $userRoleList = $this->getAssignableUserRoleList();
        $statusList = $this->getStatusList();

        $widgets = array();

        $widgets['userName'] = new sfWidgetFormInputText();
        $widgets['userType'] = new sfWidgetFormSelect(array('choices' => $userRoleList));
        $widgets['employeeName'] = new ohrmWidgetEmployeeNameAutoFill();
        $widgets['status'] = new sfWidgetFormSelect(array('choices' => $statusList));        
        $this->setWidgets($widgets);
                
        $validators = array();
        $validators['userName'] = new sfValidatorString(array('required' => false));
        $validators['userType'] = new sfValidatorChoice(array('required' => false, 
                'choices' => array_keys($userRoleList)));                
        $validators['employeeName'] = new ohrmValidatorEmployeeNameAutoFill();
        $validators['status'] = new sfValidatorChoice(array('required' => false, 
                'choices' => array_keys($statusList)));
        
        $this->setValidators($validators);

        //merge location filter
        $formExtension = PluginFormMergeManager::instance();
        $formExtension->mergeForms($this, 'viewSystemUsers', 'SearchSystemUserForm');

        $this->getWidgetSchema()->setNameFormat('searchSystemUser[%s]');
        $this->getWidgetSchema()->setLabels($this->getFormLabels());

    }

    /**
     * Get Pre Defined User Role List
     * 
     * @return array
     */
    private function getAssignableUserRoleList() {
        $list = array();
        $list[] = __("All");
        $userRoles = $this->getSystemUserService()->getAssignableUserRoles();
        
        $accessibleRoleIds = UserRoleManagerFactory::getUserRoleManager()->getAccessibleEntityIds('UserRole');
        
        foreach ($userRoles as $userRole) {
            if (in_array($userRole->getId(), $accessibleRoleIds)) {
                $list[$userRole->getId()] = $userRole->getDisplayName();
            }
        }
        return $list;
    }

    private function getStatusList() {
        $list = array();
        $list[''] = __("All");
        $list['1'] = __("Enabled");
        $list['0'] = __("Disabled");

        return $list;
    }

    public function setDefaultDataToWidgets($searchClues) {
        $this->setDefault('userName', $searchClues['userName']);
        $this->setDefault('userType', $searchClues['userType']);
        if (isset($searchClues['employeeName'])) {
            $this->setDefault('employeeName', $searchClues['employeeName']);
        }
        $this->setDefault('status', $searchClues['status']);
    }

    /**
     *
     * @return array
     */
    protected function getFormLabels() {
        $labels = array(
            'userName' => __('Username'),
            'userType' => __('User Role'),
            'employeeName' => __('Employee Name'),
            'status' => __('Status')
        );

        return $labels;
    }

}