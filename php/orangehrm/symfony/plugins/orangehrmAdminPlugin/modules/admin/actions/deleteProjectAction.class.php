<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of deleteProjectAction
 *
 * @author orangehrm
 */
class deleteProjectAction extends sfAction {
	
	private $projectService;

	public function getProjectService() {
		if (is_null($this->projectService)) {
			$this->projectService = new ProjectService();
			$this->projectService->setProjectDao(new ProjectDao());
		}
		return $this->projectService;
	}
	
	/**
	 *
	 * @param <type> $request
	 */
	public function execute($request) {

		$toBeDeletedProjectIds = $request->getParameter('chkSelectRow');

		if (!empty($toBeDeletedProjectIds)) {

			foreach ($toBeDeletedProjectIds as $toBeDeletedProjectId) {
				
				$customer = $this->getProjectService()->deleteProject($toBeDeletedProjectId);
			}
			$this->getUser()->setFlash('templateMessage', array('success', __('Selected Project(s) Deleted Successfully')));
		}

		$this->redirect('admin/viewProjects');
	}

}

?>
