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
class copyActivityAction extends sfAction {

	private $projectService;

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

	/**
	 *
	 * @param <type> $request
	 */
	public function execute($request) {

		$this->setForm(new CopyActivityForm());
		$projectId = $request->getParameter('projectId');
		$this->form->bind($request->getParameter($this->form->getName()));

		$projectActivityList = $this->getProjectService()->getActivityListByProjectId($projectId);
		if ($this->form->isValid()) {
			$activityNameList = $request->getParameter('activityNames', array());
			$activities = new Doctrine_Collection('ProjectActivity');

			$isUnique = true;
			foreach ($activityNameList as $activityName) {
				foreach ($projectActivityList as $projectActivity) {
					if (strtolower($activityName) == strtolower($projectActivity->getName())) {
						$isUnique = false;
						break;
					}
				}
			}
			if ($isUnique) {
				foreach ($activityNameList as $activityName) {

					$activity = new ProjectActivity();
					$activity->setProjectId($projectId);
					$activity->setName($activityName);
					$activity->setIsDeleted(ProjectActivity::ACTIVE_PROJECT);
					$activities->add($activity);
				}
				$activities->save();
				$this->getUser()->setFlash('success', __('Successfully Copied'));
			} else {
				$this->getUser()->setFlash('error', __('Name Already Exists'));
			}
			
			$this->redirect('admin/saveProject?projectId=' . $projectId . '#ProjectActivities');
		}
	}

}

?>
