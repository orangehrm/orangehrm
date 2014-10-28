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
class jobInterviewAction extends baseAction {

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
    public function getJobInterviewService() {
        if (is_null($this->jobInterviewService)) {
            $this->jobInterviewService = new JobInterviewService();
            $this->jobInterviewService->setJobInterviewDao(new JobInterviewDao());
        }
        return $this->jobInterviewService;
    }

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
        $allowedCandidateList = $usrObj->getAllowedCandidateList();
        $allowedVacancyList = $usrObj->getAllowedVacancyList();
        $empNumber = $usrObj->getEmployeeNumber();

        if ($this->getUser()->hasFlash('templateMessage')) {
            list($this->messageType, $this->message) = $this->getUser()->getFlash('templateMessage');
        }

        $this->historyId = $request->getParameter('historyId');
        $this->interviewId = $request->getParameter('interviewId');
        $candidateVacancyId = $request->getParameter('candidateVacancyId');
        $selectedAction = $request->getParameter('selectedAction');
        $this->editHiringManager = true;

        $param = array();
        if ($candidateVacancyId > 0 && $selectedAction != "") {
            $candidateVacancy = $this->getCandidateService()->getCandidateVacancyById($candidateVacancyId);

            // check if user can perform action on candidate
            if (!empty($this->interviewId) && $request->isMethod('post')) {
                $allowedStates = $userRoleManager->getActionableStates(WorkflowStateMachine::FLOW_RECRUITMENT, 
                        array($selectedAction));
                $actionAllowed = !empty($allowedStates);
            } else {
                $actionAllowed = $userRoleManager->isActionAllowed(WorkflowStateMachine::FLOW_RECRUITMENT, 
                        $candidateVacancy->getStatus(), $selectedAction);
            }
            if ($actionAllowed) {
                $this->candidatePermissions = new ResourcePermission(true, true, true, true);
            }
            
            $interviewHistory = $this->getJobInterviewService()->getInterviewScheduledHistoryByInterviewId($this->interviewId);
            $param = array('interviewId' => $this->interviewId, 'candidateVacancyId' => $candidateVacancyId, 'selectedAction' => $selectedAction, 'historyId' => (!empty($interviewHistory)) ? $interviewHistory->getId() : null, 'candidatePermissions' => $this->candidatePermissions);
        }

        if (!empty($this->historyId) && !empty($this->interviewId)) {
            $history = $this->getCandidateService()->getCandidateHistoryById($this->historyId);
            $candidateVacancyId = $this->getCandidateService()->getCandidateVacancyByCandidateIdAndVacancyId($history->getCandidateId(), $history->getVacancyId());
            $selectedAction = $history->getAction();
            
            // check if user can perform this history action
            $allowedStates = $userRoleManager->getActionableStates(WorkflowStateMachine::FLOW_RECRUITMENT, 
                    array($selectedAction));
            if (!empty($allowedStates)) {
                $this->candidatePermissions = new ResourcePermission(true, true, true, true);
            }
            
            $param = array('id' => $this->interviewId, 'candidateVacancyId' => $candidateVacancyId, 'selectedAction' => $selectedAction, 'candidatePermissions' => $this->candidatePermissions);
        }
        if (!$this->getCandidateService()->isHiringManager($candidateVacancyId, $empNumber) && $this->getCandidateService()->isInterviewer($candidateVacancyId, $empNumber)) {
            $this->editHiringManager = false;
        }
//        $lastAction = $this->getCandidateService()->getLastPerformedActionByCandidateVAcancyId($candidateVacancyId);
        $this->setForm(new JobInterviewForm(array(), $param, true));

        if (!in_array($this->form->candidateId, $allowedCandidateList) && !in_array($this->form->vacancyId, $allowedVacancyList)) {
            $this->redirect('recruitment/viewCandidates');
        }

        if ($request->isMethod('post')) {
            if ($this->candidatePermissions->canUpdate()) {
                $this->form->bind($request->getParameter($this->form->getName()));
                if ($this->form->isValid()) {
                    $result = $this->form->save();
                    if (isset($result['messageType'])) {
                        $this->getUser()->setFlash('templateMessage', array($result['messageType'], $result['message']));
                    } else {
                        $this->getUser()->setFlash('templateMessage', array('success', __('Successfully Scheduled')));
                    }
                    $this->redirect('recruitment/changeCandidateVacancyStatus?id=' . $this->form->historyId);
                }
            }
        }
    }

}

