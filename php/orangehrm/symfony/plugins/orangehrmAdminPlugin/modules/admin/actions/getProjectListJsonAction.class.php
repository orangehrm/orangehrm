<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of getProjectListJsonAction
 *
 * @author orangehrm
 */
class getProjectListJsonAction extends sfAction {
	
	private $projectService;

	public function getProjectService() {
		if (is_null($this->projectService)) {
			$this->projectService = new ProjectService();
			$this->projectService->setProjectDao(new ProjectDao());
		}
		return $this->projectService;
	}
	
	public function execute($request) {

		$this->setLayout(false);
		sfConfig::set('sf_web_debug', false);
		sfConfig::set('sf_debug', false);

		if ($this->getRequest()->isXmlHttpRequest()) {
			$this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
		}

		$customerId = $request->getParameter('customerId');

		$projectList = $this->getProjectService()->getProjectsByCustomerId($customerId);

		return $this->renderText(json_encode($projectList->toArray()));
	
	}
}

?>
