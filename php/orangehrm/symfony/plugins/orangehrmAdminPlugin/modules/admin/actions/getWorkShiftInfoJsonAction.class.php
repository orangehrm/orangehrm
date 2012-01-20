<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of getWorkShiftInfoJsonAction
 *
 * @author orangehrm
 */
class getWorkShiftInfoJsonAction extends sfAction {
	
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

		$workShiftId = $request->getParameter('id');

		$service = new WorkShiftService();
		$workShift = $service->getWorkShiftById($workShiftId);

		return $this->renderText(json_encode($workShift->toArray()));
	}
}

?>
