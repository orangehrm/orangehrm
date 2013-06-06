<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of getEmploymentStatusJsonAction
 *
 * @author orangehrm
 */
class getEmploymentStatusJsonAction extends sfAction {
	
	/**
	 *
	 * @param <type> $request
	 * @return <type>
	 */
	public function execute($request) {

		$this->setLayout(false);
		sfConfig::set('sf_web_debug', false);
		sfConfig::set('sf_debug', false);

		if ($this->getRequest()->isXmlHttpRequest()) {
			$this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
		}

		$empStatId = $request->getParameter('id');

		$service = new EmploymentStatusService();
		$status = $service->getEmploymentStatusById($empStatId);

		return $this->renderText(json_encode($status->toArray()));
	}

}

?>
