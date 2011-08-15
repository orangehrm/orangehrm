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
class jobInterviewAction extends sfAction {

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

		$historyId = $request->getParameter('historyId');
		$this->interviewId = $request->getParameter('interviewId');
		$candidateVacancyId = $request->getParameter('candidateVacancyId');
		$selectedAction = $request->getParameter('selectedAction');
		$param = array();
		if ($candidateVacancyId > 0 && $selectedAction != "") {
			$param = array('interviewId' => $this->interviewId, 'candidateVacancyId' => $candidateVacancyId, 'selectedAction' => $selectedAction);
		}	
		if (!empty($historyId) && !empty($this->interviewId)) {
			$history = $this->getCandidateService()->getCandidateHistoryById($historyId);
			$candidateVacancyId = $history->getCandidateVacancyId();
			$selectedAction = $history->getAction();
			$param = array('id' => $this->interviewId, 'candidateVacancyId' => $candidateVacancyId, 'selectedAction' => $selectedAction);
		}

		$this->setForm(new JobInterviewForm(array(), $param, true));

		if ($request->isMethod('post')) {

			$this->form->bind($request->getParameter($this->form->getName()));
			if ($this->form->isValid()) {
				$this->form->save();
//                $result = $this->form->performAction();
				$this->redirect('recruitment/addCandidate?id=' . $this->form->candidateId);
			}
		}
	}

}

