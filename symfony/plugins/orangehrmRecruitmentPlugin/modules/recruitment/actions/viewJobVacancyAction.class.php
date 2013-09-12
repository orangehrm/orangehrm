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
class viewJobVacancyAction extends baseRecruitmentAction {

    private $vacancyService;

    /**
     * Get CandidateService
     * @returns CandidateService
     */
    public function getVacancyService() {
        if (is_null($this->vacancyService)) {
            $this->vacancyService = new VacancyService();
            $this->vacancyService->setVacancyDao(new VacancyDao());
        }
        return $this->vacancyService;
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

    /**
     *
     * @param <type> $request
     */
    public function execute($request) {

        $usrObj = $this->getUser()->getAttribute('user');

        $this->vacancyPermissions = $this->getDataGroupPermissions('recruitment_vacancies');

        $allowedVacancyList = $usrObj->getAllowedVacancyList();

        $isPaging = $request->getParameter('pageNo');
        $vacancyId = $request->getParameter('vacancyId');

        $pageNumber = $isPaging;
        if (!is_null($this->getUser()->getAttribute('vacancyPageNumber')) && !($pageNumber >= 1)) {
            $pageNumber = $this->getUser()->getAttribute('vacancyPageNumber');
        }
        $this->getUser()->setAttribute('vacancyPageNumber', $pageNumber);

        $sortField = $request->getParameter('sortField');
        $sortOrder = $request->getParameter('sortOrder');
        $noOfRecords = sfConfig::get('app_items_per_page');
        $offset = ($pageNumber >= 1) ? (($pageNumber - 1) * $noOfRecords) : ($request->getParameter('pageNo', 1) - 1) * $noOfRecords;

        $param = array('allowedVacancyList' => $allowedVacancyList);
        $this->setForm(new ViewJobVacancyForm(array(), $param, true));


        $srchParams = array('jobTitle' => "", 'jobVacancy' => "", 'hiringManager' => "", 'status' => "");
        $srchParams['noOfRecords'] = $noOfRecords;
        $srchParams['offset'] = $offset;

        if (!empty($sortField) && !empty($sortOrder) || $vacancyId > 0 || $isPaging > 0) {
            if ($this->getUser()->hasAttribute('searchParameters')) {
                $srchParams = $this->getUser()->getAttribute('searchParameters');
                $this->form->setDefaultDataToWidgets($srchParams);
            }
            $srchParams['orderField'] = $sortField;
            $srchParams['orderBy'] = $sortOrder;
        } else {
            $this->getUser()->setAttribute('searchParameters', $srchParams);
        }

        list($this->messageType, $this->message) = $this->getUser()->getFlash('vacancyDeletionMessageItems');
        $srchParams['offset'] = $offset;
        $vacancyList = $this->getVacancyService()->searchVacancies($srchParams);

        if ($this->vacancyPermissions->canRead()) {
            $this->_setListComponent($vacancyList, $noOfRecords, $srchParams, $pageNumber, $this->vacancyPermissions);
        }
        $params = array();
        $this->parmetersForListCompoment = $params;
        if (empty($isPaging)) {
            if ($request->isMethod('post')) {

                $pageNumber = 1;
                $this->getUser()->setAttribute('vacancyPageNumber', $pageNumber);
                $this->form->bind($request->getParameter($this->form->getName()));

                if ($this->form->isValid()) {
                    $srchParams = $this->form->getSearchParamsBindwithFormData();
                    $srchParams['noOfRecords'] = $noOfRecords;
                    $srchParams['offset'] = 0;
                    $this->getUser()->setAttribute('searchParameters', $srchParams);
                    $vacancyList = $this->getVacancyService()->searchVacancies($srchParams);
                    $this->_setListComponent($vacancyList, $noOfRecords, $srchParams, $pageNumber, $this->vacancyPermissions);
                }
            }
        }
    }

    /**
     *
     * @param <type> $vacancyList
     * @param <type> $noOfRecords
     * @param <type> $srchParams
     */
    private function _setListComponent($vacancyList, $noOfRecords, $srchParams, $pageNumber, $permissions) {
        $runtimeDefinitions = array();
        $buttons = array();

        if ($permissions->canCreate()) {
            $buttons['Add'] = array('label' => 'Add',
                'function' => 'addJobVacancy');
        }

        if (!$permissions->canDelete()) {
            $runtimeDefinitions['hasSelectableRows'] = false;
        } else if ($permissions->canDelete()) {
            $buttons['Delete'] = array('label' => 'Delete',
                'type' => 'submit',
                'data-toggle' => 'modal',
                'data-target' => '#deleteConfirmation',
                'class' => 'delete');
        }

        $runtimeDefinitions['buttons'] = $buttons;
        
        $configurationFactory = new JobVacancyHeaderFactory();
        $configurationFactory->setRuntimeDefinitions($runtimeDefinitions);
        
        ohrmListComponent::setPageNumber($pageNumber);
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setListData($vacancyList);
        ohrmListComponent::setItemsPerPage($noOfRecords);
        ohrmListComponent::setNumberOfRecords($this->getVacancyService()->searchVacanciesCount($srchParams));
    }

}
