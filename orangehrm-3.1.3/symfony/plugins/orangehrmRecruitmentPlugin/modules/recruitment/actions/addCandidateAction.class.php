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
class addCandidateAction extends baseAction {

    /**
     * @param sfForm $form
     * @return
     */
    public function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }

    public function getForm() {
        return $this->form;
    }

    /**
     *
     * @return <type>
     */
    public function getCandidateService() {
        if (is_null($this->candidateService)) {
            $this->candidateService = new CandidateService();
            $this->candidateService->setCandidateDao(new CandidateDao());
        }
        return $this->candidateService;
    }

    /**
     *
     * @return <type>
     */
    public function getVacancyService() {
        if (is_null($this->vacancyService)) {
            $this->vacancyService = new VacancyService();
            $this->vacancyService->setVacancyDao(new VacancyDao());
        }
        return $this->vacancyService;
    }

    /**
     *
     * @param <type> $request
     */
    public function execute($request) {
        /* For highlighting corresponding menu item */
        $request->setParameter('initialActionName', 'viewCandidates');

        $userRoleManager = $this->getContext()->getUserRoleManager();
        $requiredPermissions = array(
            BasicUserRoleManager::PERMISSION_TYPE_DATA_GROUP => array(
                'recruitment_candidates' => new ResourcePermission(true, false, false, false)
            )
        );
            
        $allowedVacancyList = $userRoleManager->getAccessibleEntityIds('Vacancy', 
                null, null, array(), array(), $requiredPermissions);
        
        $this->candidatePermissions = $this->getDataGroupPermissions('recruitment_candidates');

        $userObj = $this->getUser()->getAttribute('user');

        $allowedCandidateListToDelete = $userObj->getAllowedCandidateListToDelete();
        $this->candidateId = $request->getParameter('id');
        $this->invalidFile = false;
        $reDirect = false;
        $this->edit = true;
        if ($this->candidateId > 0 && !(in_array($this->candidateId, $allowedCandidateListToDelete))) {
            $reDirect = true;
            $this->edit = false;
        }
        $param = array('candidateId' => $this->candidateId, 'allowedVacancyList' => $allowedVacancyList, 'empNumber' => $userObj->getEmployeeNumber(), 'isAdmin' => $userObj->isAdmin(), 'candidatePermissions' => $this->candidatePermissions);
        $this->setForm(new AddCandidateForm(array(), $param, true));


        $vacancyProperties = array('name', 'id', 'status');
        $this->jobVacancyList = $this->getVacancyService()->getVacancyPropertyList($vacancyProperties);

        $this->candidateStatus = JobCandidate::ACTIVE;

        if ($this->candidateId > 0) {
            $allowedCandidateList = $userObj->getAllowedCandidateList();
            if (!in_array($this->candidateId, $allowedCandidateList)) {
                $this->redirect('recruitment/viewCandidates');
            }
            $this->actionForm = new ViewCandidateActionForm(array(), $param, true);
            $allowedHistoryList = $userObj->getAllowedCandidateHistoryList($this->candidateId);

            $candidateHistory = $this->getCandidateService()->getCandidateHistoryForCandidateId($this->candidateId, $allowedHistoryList);
            $candidateHistoryService = new CandidateHistoryService();
            if ($this->candidatePermissions->canRead()) {
                $this->_setListComponent($candidateHistoryService->getCandidateHistoryList($candidateHistory));
            }
            $params = array();
            $this->parmetersForListCompoment = $params;
            $this->candidateStatus = $this->getCandidateService()->getCandidateById($this->candidateId)->getStatus();
        } else {
            if (!($userObj->isAdmin() || $userObj->isHiringManager() || $this->candidatePermissions->canRead())) {
                $this->redirect('recruitment/viewCandidates');
            }
        }

        if ($request->isMethod('post')) {
            if ($this->candidatePermissions->canCreate() || $this->candidatePermissions->canUpdate()) {

                $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
                $file = $request->getFiles($this->form->getName());

                if (($_FILES['addCandidate']['size']['resume'] > 1024000) || ($_FILES['addCandidate']['error']['resume'] && $_FILES['addCandidate']['name']['resume'])) {
                    $title = ($this->candidateId > 0) ? __('Editing Candidate') : __('Adding Candidate');
                    $this->getUser()->setFlash('addcandidate.warning', __(TopLevelMessages::FILE_SIZE_SAVE_FAILURE));
                } elseif ($_FILES == null) {
                    $title = ($this->candidateId > 0) ? __('Editing Candidate') : __('Adding Candidate');
                    $this->getUser()->setFlash('addcandidate.warning', __(TopLevelMessages::FILE_SIZE_SAVE_FAILURE));
                    $this->redirect('recruitment/addCandidate');
                } else {
                    $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
                    $file = $request->getFiles($this->form->getName());

                    if ($this->form->isValid()) {

                        $result = $this->form->save();

                        if (isset($result['messageType'])) {
                            $this->getUser()->setFlash('addcandidate.warning', $result['message']);
                            $this->invalidFile = true;
                        } else {
                            $this->candidateId = $result['candidateId'];
                            $this->getUser()->setFlash('addcandidate.success', __(TopLevelMessages::SAVE_SUCCESS));
                            $this->redirect('recruitment/addCandidate?id=' . $this->candidateId);
                        }
                    }
                }
            }
        }
    }

    /**
     *
     * @param <type> $candidateHistory
     */
    private function _setListComponent($candidateHistory) {
        $configurationFactory = new CandidateHistoryHeaderFactory();
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setListData($candidateHistory);
    }

}
