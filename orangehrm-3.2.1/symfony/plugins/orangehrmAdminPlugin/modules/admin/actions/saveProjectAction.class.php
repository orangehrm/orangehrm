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
class saveProjectAction extends baseAdminAction {

    private $projectService;
    private $customerService;

    public function getCustomerService() {
        if (is_null($this->customerService)) {
            $this->customerService = new CustomerService();
            $this->customerService->setCustomerDao(new CustomerDao());
        }
        return $this->customerService;
    }

    public function getProjectService() {
        if (is_null($this->projectService)) {
            $this->projectService = new ProjectService();
            $this->projectService->setProjectDao(new ProjectDao());
        }
        return $this->projectService;
    }

    /**
     * @param sfForm $form
     * @return
     */
    public function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }

    protected function getUndeleteForm($projectId = '') {
        return new UndeleteCustomerForm(array(), array('fromAction' => 'saveProject', 'projectId' => $projectId), true);
    }

    public function execute($request) {

        /* For highlighting corresponding menu item */
        $request->setParameter('initialActionName', 'viewProjects');

        $usrObj = $this->getUser()->getAttribute('user');

        $this->projectPermissions = $this->getDataGroupPermissions('time_projects');
        $this->customerPermissions = $this->getDataGroupPermissions('time_customers');
        
        $this->isProjectAdmin = false;
        if ($usrObj->isProjectAdmin()) {
            $this->isProjectAdmin = true;
        }
        $this->projectId = $request->getParameter('projectId');
        $this->custId = $request->getParameter('custId');
        
        if ($this->projectId) {
            $userRoleManager = $this->getContext()->getUserRoleManager();
            $isAccessible = $userRoleManager->isEntityAccessible('Project', $this->projectId);
            if (!$isAccessible) {
                $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
            }
        }

        $values = array('projectId' => $this->projectId, 'projectPermissions' => $this->projectPermissions);
        $this->setForm(new ProjectForm(array(), $values));

        $valuesForCustomer = array('customerPermissions' => $this->customerPermissions);
        $this->customerForm = new CustomerForm(array(), $valuesForCustomer);
        $this->formToImplementCsrfToken = new TimesheetFormToImplementCsrfTokens();

        if ($this->custId > 0) {
            $customer = $this->getCustomerService()->getCustomerById($this->custId);
            $customerName = $customer->getName();
            $this->form->setDefault('customerName', $customerName);
            print_r($this->customerName);
            $this->getUser()->setFlash('success', __(TopLevelMessages::SAVE_SUCCESS));
        }

        if (!empty($this->projectId)) {
            $this->activityForm = new AddProjectActivityForm();
            $this->copyActForm = new CopyActivityForm();
            //For list activities
            $this->activityList = $this->getProjectService()->getActivityListByProjectId($this->projectId);
            $this->_setListComponent($this->activityList, $this->projectPermissions);
            $params = array();
            $this->parmetersForListCompoment = $params;
        }

        if ($request->isMethod('post')) {
            if ($this->projectPermissions->canCreate() || $this->projectPermissions->canUpdate()) {
                $this->form->bind($request->getParameter($this->form->getName()));
                if ($this->form->isValid()) {
                    $projectId = $this->form->save();
                    if ($this->form->edited) {
                        $this->getUser()->setFlash('project.success', __(TopLevelMessages::UPDATE_SUCCESS));
                    } else {
                        $this->getUser()->setFlash('project.success', __(TopLevelMessages::SAVE_SUCCESS));
                    }
                    $this->redirect('admin/saveProject?projectId=' . $projectId);
                } 
            }
        } else {
            $this->undeleteForm = $this->getUndeleteForm($this->projectId);
        }
    }

    /**
     *
     * @param <type> $customerList
     * @param <type> $noOfRecords
     * @param <type> $pageNumber
     */
    private function _setListComponent($customerList, $permissions) {
        $configurationFactory = new ProjectActivityHeaderFactory();
        $runtimeDefinitions = array();
        $buttons = array();

        if ($permissions->canCreate() || $permissions->canUpdate()) {
            $configurationFactory->setAllowEdit(true);
            $buttons['Add'] = array('label' => 'Add');
            $runtimeDefinitions['hasSelectableRows'] = true;
            $buttons['Delete'] = array('label' => 'Delete',
                'type' => 'submit',
                'data-toggle' => 'modal',
                'data-target' => '#deleteConfModal',
                'class' => 'delete');
            $buttons['Copy'] = array('label' => 'Copy From',
                'data-toggle' => 'modal',
                'data-target' => '#copyActivityModal',
                'class' => 'reset');
        }else{
            $runtimeDefinitions['hasSelectableRows'] = false;
            $configurationFactory->setAllowEdit(false);
        }
        
        $runtimeDefinitions['buttons'] = $buttons;

        $configurationFactory->setRuntimeDefinitions($runtimeDefinitions);
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setListData($customerList);
    }

}

