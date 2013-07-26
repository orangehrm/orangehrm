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
class viewJobTitleListAction extends baseAdminAction {

    private $jobTitleService;

    public function getJobTitleService() {
        if (is_null($this->jobTitleService)) {
            $this->jobTitleService = new JobTitleService();
            $this->jobTitleService->setJobTitleDao(new JobTitleDao());
        }
        return $this->jobTitleService;
    }

    public function execute($request) {

        $usrObj = $this->getUser()->getAttribute('user');

        $this->jobTitlePermissions = $this->getDataGroupPermissions('job_titles');

//        if (!($usrObj->isAdmin()) && !($this->jobTitlePermission)) {
//            $this->redirect('pim/viewPersonalDetails');
//        }
        if ($this->jobTitlePermissions->canRead()) {
            $jobTitleId = $request->getParameter('jobTitleId');
            $isPaging = $request->getParameter('pageNo');

            $pageNumber = $isPaging;
            if (!empty($jobTitleId) && $this->getUser()->hasAttribute('pageNumber')) {
                $pageNumber = $this->getUser()->getAttribute('pageNumber');
            }

            $sortField = $request->getParameter('sortField');
            $sortOrder = $request->getParameter('sortOrder');

            $noOfRecords = JobTitle::NO_OF_RECORDS_PER_PAGE;
            $offset = ($pageNumber >= 1) ? (($pageNumber - 1) * $noOfRecords) : ($request->getParameter('pageNo', 1) - 1) * $noOfRecords;

            $JobTitleList = $this->getJobTitleService()->getJobTitleList($sortField, $sortOrder, true, $noOfRecords, $offset);

            $this->_setListComponent($JobTitleList, $noOfRecords, $pageNumber, $this->jobTitlePermissions);

            $this->getUser()->setAttribute('pageNumber', $pageNumber);
            $params = array();
            $this->parmetersForListCompoment = $params;
        }
    }

    private function _setListComponent($JobTitleList, $noOfRecords, $pageNumber, $permissions) {

        $configurationFactory = $this->_getConfigurationFactory($permissions);
        $runtimeDefinitions = array();
        $buttons = array();

        if ($permissions->canCreate()) {
            $buttons['Add'] = array('label' => 'Add');
        }

        if (!$permissions->canDelete()) {
            $runtimeDefinitions['hasSelectableRows'] = false;
        } else if ($permissions->canDelete()) {
            $buttons['Delete'] = array('label' => 'Delete',
                'type' => 'submit',
                'data-toggle' => 'modal',
                'data-target' => '#deleteConfModal',
                'class' => 'delete');
        }

        $runtimeDefinitions['buttons'] = $buttons;
        $configurationFactory->setRuntimeDefinitions($runtimeDefinitions);
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setListData($JobTitleList);
        ohrmListComponent::setPageNumber($pageNumber);
        ohrmListComponent::setItemsPerPage($noOfRecords);
        ohrmListComponent::setNumberOfRecords(count($this->getJobTitleService()->getJobTitleList()));
    }

    private function _getConfigurationFactory($permissions) {
        $jobTitleHeaderFactory = new JobTitleHeaderFactory();
//        if (!$permissions->canUpdate()) {
//            $jobTitleHeaderFactory->setAllowEdit(false);
//        }
        return $jobTitleHeaderFactory;
    }

}