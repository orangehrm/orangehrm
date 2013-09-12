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
class viewCandidatesAction extends baseAction {

    private $candidateService;

    /**
     * Get CandidateService
     * @returns CandidateService
     */
    public function getCandidateService() {
        if (is_null($this->candidateService)) {
            $this->candidateService = new CandidateService();
            $this->candidateService->setCandidateDao(new CandidateDao());
        }
        return $this->candidateService;
    }

    /**
     * Set CandidateService
     * @param CandidateService $candidateService
     */
    public function setCandidateService(CandidateService $candidateService) {
        $this->candidateService = $candidateService;
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

        $userRoleManager = $this->getContext()->getUserRoleManager();
        $requiredPermissions = array(
            BasicUserRoleManager::PERMISSION_TYPE_DATA_GROUP => array(
                'recruitment_candidates' => new ResourcePermission(true, false, false, false)
            )
        );
            
        $allowedVacancyList = $userRoleManager->getAccessibleEntityIds('Vacancy', 
                null, null, array(), array(), $requiredPermissions);

        $usrObj = $this->getUser()->getAttribute('user');
        $allowedCandidateList = $usrObj->getAllowedCandidateList();
        
        
        $allowedCandidateListToDelete = $usrObj->getAllowedCandidateListToDelete();

        $this->candidaatePermissions = $this->getDataGroupPermissions('recruitment_candidates');

        $isAdmin = $usrObj->isAdmin();
        if (!($usrObj->isAdmin() || $usrObj->isHiringManager() || $usrObj->isInterviewer() || $this->candidaatePermissions->canRead())) {
            $this->redirect('pim/viewPersonalDetails');
        }

        $param = array('allowedCandidateList' => $allowedCandidateList, 'allowedVacancyList' => $allowedVacancyList, 'allowedCandidateListToDelete' => $allowedCandidateListToDelete);
        list($this->messageType, $this->message) = $this->getUser()->getFlash('candidateListMessageItems');
        $candidateId = $request->getParameter('candidateId');
        $sortField = $request->getParameter('sortField');
        $sortOrder = $request->getParameter('sortOrder');
        $isPaging = $request->getParameter('pageNo');

        $pageNumber = $isPaging;
        if (!is_null($this->getUser()->getAttribute('pageNumber')) && !($pageNumber >= 1)) {
            $pageNumber = $this->getUser()->getAttribute('pageNumber');
        }
        $this->getUser()->setAttribute('pageNumber', $pageNumber);

        $searchParam = new CandidateSearchParameters();

        $searchParam->setIsAdmin($isAdmin);
        $searchParam->setEmpNumber($usrObj->getEmployeeNumber());
        $searchParam->setLimit(sfConfig::get('app_items_per_page'));
        $noOfRecords = $searchParam->getLimit();
        $offset = ($pageNumber >= 1) ? (($pageNumber - 1) * $noOfRecords) : ($request->getParameter('pageNo', 1) - 1) * $noOfRecords;
        $searchParam->setAdditionalParams($request->getParameter('additionalParams', array()));
        $this->setForm(new viewCandidatesForm(array(), $param, true));
        if (!empty($sortField) && !empty($sortOrder) || $isPaging > 0 || $candidateId > 0) {
            if ($this->getUser()->hasAttribute('searchParameters')) {
                $searchParam = $this->getUser()->getAttribute('searchParameters');
                $this->form->setDefaultDataToWidgets($searchParam);
            }
            $searchParam->setSortField($sortField);
            $searchParam->setSortOrder($sortOrder);
        } else {
            $this->getUser()->setAttribute('searchParameters', $searchParam);
            $offset = 0;
            $pageNumber = 1;
        }
        $searchParam->setAllowedCandidateList($allowedCandidateList);
        $searchParam->setAllowedVacancyList($allowedVacancyList);
        $searchParam->setOffset($offset);
        $candidates = $this->getCandidateService()->searchCandidates($searchParam);
        if ($this->candidaatePermissions->canRead()) {
            $this->_setListComponent($usrObj, $candidates, $noOfRecords, $searchParam, $pageNumber, $this->candidaatePermissions);
        }
        $params = array();
        $this->parmetersForListCompoment = $params;
        if (empty($isPaging)) {
            if ($request->isMethod('post')) {

                $pageNumber = 1;
                $searchParam->setOffset(0);
                $this->getUser()->setAttribute('pageNumber', $pageNumber);

                $this->form->bind($request->getParameter($this->form->getName()));
                if ($this->form->isValid()) {
                    $srchParams = $this->form->getSearchParamsBindwithFormData($searchParam);
                    $this->getUser()->setAttribute('searchParameters', $srchParams);
                    $candidates = $this->getCandidateService()->searchCandidates($srchParams);
                    $this->_setListComponent($usrObj, $candidates, $noOfRecords, $searchParam, $pageNumber, $this->candidaatePermissions);
                }
            }
        }
    }

    /**
     *
     * @param <type> $candidates
     * @param <type> $noOfRecords
     * @param CandidateSearchParameters $searchParam
     */
    private function _setListComponent($usrObj, $candidates, $noOfRecords, CandidateSearchParameters $searchParam, $pageNumber, $permissions) {
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
                'data-target' => '#deleteConfirmation',
                'class' => 'delete');
        }

        $runtimeDefinitions['buttons'] = $buttons;
        $configurationFactory = new CandidateHeaderFactory();

//        if (!($usrObj->isAdmin() || $usrObj->isHiringManager())) {
//            $configurationFactory->setRuntimeDefinitions(array(
//                'hasSelectableRows' => false,
//                'buttons' => array(),
//            ));
//        }
        $configurationFactory->setRuntimeDefinitions($runtimeDefinitions);
        ohrmListComponent::setPageNumber($pageNumber);
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setListData($candidates);
        ohrmListComponent::setItemsPerPage($noOfRecords);
        ohrmListComponent::setNumberOfRecords($this->getCandidateService()->getCandidateRecordsCount($searchParam));
    }

}

