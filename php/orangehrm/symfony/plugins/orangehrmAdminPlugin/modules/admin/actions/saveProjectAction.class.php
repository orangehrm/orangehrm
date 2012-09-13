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
class saveProjectAction extends sfAction {

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
        return new UndeleteCustomerForm(array(), array('fromAction' => 'saveProject','projectId' => $projectId), true);
    }

    public function execute($request) {

        $usrObj = $this->getUser()->getAttribute('user');
        if (!($usrObj->isAdmin() || $usrObj->isProjectAdmin())) {
            $this->redirect('pim/viewPersonalDetails');
        }
        $this->isProjectAdmin = false;
        if ($usrObj->isProjectAdmin()) {
            $this->isProjectAdmin = true;
        }
        $this->projectId = $request->getParameter('projectId');
        $this->custId = $request->getParameter('custId');

        $values = array('projectId' => $this->projectId);
        $this->setForm(new ProjectForm(array(), $values));
        $this->customerForm = new CustomerForm();

        if ($this->custId > 0) {
            $customer = $this->getCustomerService()->getCustomerById($this->custId);
            $customerName = $customer->getName();
            $this->form->setDefault('customerName', $customerName);
            print_r($this->customerName);
            $this->getUser()->setFlash('templateMessage', array('success', __(TopLevelMessages::SAVE_SUCCESS)));
        }

        if (!empty($this->projectId)) {
            $this->activityForm = new AddProjectActivityForm();
            $this->copyActForm = new CopyActivityForm();
            //For list activities
            $this->activityList = $this->getProjectService()->getActivityListByProjectId($this->projectId);
            $this->_setListComponent($this->activityList);
            $params = array();
            $this->parmetersForListCompoment = $params;
        }

        if ($this->getUser()->hasFlash('templateMessage')) {
            list($this->messageType, $this->message) = $this->getUser()->getFlash('templateMessage');
        }

        if ($this->getUser()->hasFlash('templateMessageAct')) {
            list($this->messageTypeAct, $this->messageAct) = $this->getUser()->getFlash('templateMessageAct');
        }

        if ($request->isMethod('post')) {

            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {

                $projectId = $this->form->save();
                if ($this->form->edited) {
                    $this->getUser()->setFlash('templateMessage', array('success', __(TopLevelMessages::UPDATE_SUCCESS)));
                } else {
                    $this->getUser()->setFlash('templateMessage', array('success', __(TopLevelMessages::SAVE_SUCCESS)));
                }
                $this->redirect('admin/saveProject?projectId=' . $projectId);
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
    private function _setListComponent($customerList, $noOfRecords, $pageNumber) {

        $configurationFactory = new ProjectActivityHeaderFactory();
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setListData($customerList);
    }

}

