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
class changeCandidateVacancyStatusAction extends baseRecruitmentAction {

    private $performedAction;

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
     * @param <type> $request
     */
    public function execute($request) {

        /* For highlighting corresponding menu item */
        $request->setParameter('initialActionName', 'viewCandidates');
        $userRoleManager = $this->getContext()->getUserRoleManager();

        $this->candidatePermissions = $this->getDataGroupPermissions('recruitment_candidates');

        $usrObj = $this->getUser()->getAttribute('user');
        if (!($usrObj->isAdmin() || $usrObj->isHiringManager() || $usrObj->isInterviewer() || $this->candidatePermissions->canRead())) {
            $this->redirect('pim/viewPersonalDetails');
        }
        $allowedCandidateList = $usrObj->getAllowedCandidateList();
        $allowedVacancyList = $usrObj->getAllowedVacancyList();
        $allowedCandidateListToDelete = $usrObj->getAllowedCandidateListToDelete();
        $this->enableEdit = true;
        if ($this->getUser()->hasFlash('templateMessage')) {
            list($this->messageType, $this->message) = $this->getUser()->getFlash('templateMessage');
        }

        $id = $request->getParameter('id');
        $this->id = $id;
        if (!empty($id)) {
            $history = $this->getCandidateService()->getCandidateHistoryById($id);
            $action = $history->getAction();
            
            // check if user can perform this history action
            $allowedStates = $userRoleManager->getActionableStates(WorkflowStateMachine::FLOW_RECRUITMENT, 
                    array($action));
            if (!empty($allowedStates)) {
                $this->candidatePermissions = new ResourcePermission(true, true, true, true);
            }
            
            $this->interviewId = $history->getInterviewId();
            if ($action == WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHEDULE_INTERVIEW || $action == WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHEDULE_2ND_INTERVIEW) {
                if ($this->getUser()->hasFlash('templateMessage')) {
                    list($this->messageType, $this->message) = $this->getUser()->getFlash('templateMessage');
                    $this->getUser()->setFlash($this->messageType, $this->message);
                }
                $this->redirect('recruitment/jobInterview?historyId=' . $id . '&interviewId=' . $this->interviewId);
            }
            $this->performedAction = $action;
            if ($this->getCandidateService()->isInterviewer($this->getCandidateService()->getCandidateVacancyByCandidateIdAndVacancyId($history->getCandidateId(), $history->getVacancyId()), $usrObj->getEmployeeNumber())) {
                $this->enableEdit = false;
                if ($action == WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_MARK_INTERVIEW_PASSED || $action == WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_MARK_INTERVIEW_FAILED) {
                    $this->enableEdit = true;
                }
            }
            
        }
        $candidateVacancyId = $request->getParameter('candidateVacancyId');
        $this->selectedAction = $request->getParameter('selectedAction');
        $param = array();
        if ($id > 0) {
            $param = array('id' => $id, 'candidatePermissions' => $this->candidatePermissions);
        }
        if ($candidateVacancyId > 0 && $this->selectedAction != "") {
            $candidateVacancy = $this->getCandidateService()->getCandidateVacancyById($candidateVacancyId);
            $nextActionList = $this->getCandidateService()->getNextActionsForCandidateVacancy($candidateVacancy->getStatus(), $usrObj);
            if ($nextActionList[$this->selectedAction] == "" || !in_array($candidateVacancy->getCandidateId(), $allowedCandidateList)) {
                $this->redirect('recruitment/viewCandidates');
            }
            
            // check if user can perform action on candidate
            $actionAllowed = $userRoleManager->isActionAllowed(WorkflowStateMachine::FLOW_RECRUITMENT, 
                    $candidateVacancy->getStatus(), $this->selectedAction);
           
            if ($actionAllowed) {
                $this->candidatePermissions = new ResourcePermission(true, true, true, false);
            }
            
            $param = array('candidateVacancyId' => $candidateVacancyId, 'selectedAction' => $this->selectedAction, 'candidatePermissions' => $this->candidatePermissions);
            $this->performedAction = $this->selectedAction;                                     
            
        }

        $this->setForm(new CandidateVacancyStatusForm(array(), $param, true));
//        if (!in_array($this->form->candidateId, $allowedCandidateList) && !in_array($this->form->vacancyId, $allowedVacancyList)) {
//            $this->redirect('recruitment/viewCandidates');
//        }
//        if (!in_array($this->form->candidateId, $allowedCandidateListToDelete)) {
//            $this->enableEdit = false;
//        }        
        if ($request->isMethod('post')) {
            if ($this->candidatePermissions->canUpdate()) {
                
                $this->form->bind($request->getParameter($this->form->getName()));
                if ($this->form->isValid()) {
                    $result = $this->form->performAction();
                    if (isset($result['messageType'])) {
                        $this->getUser()->setFlash($result['messageType'], $result['message']);
                    } else {
                        $message = __(TopLevelMessages::UPDATE_SUCCESS);
                        $this->getUser()->setFlash('success', $message);
                    }
                    $this->redirect('recruitment/changeCandidateVacancyStatus?id=' . $this->form->historyId);
                }
            }
        }
    }

}