<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of getWorkShiftEmpInfoJsonAction
 *
 * @author orangehrm
 */
class getWorkShiftEmpInfoJsonAction extends sfAction {
	
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
		$workShiftList = $service->getWorkShiftEmployeeListById($workShiftId);

		$list = array();
		foreach ($workShiftList as $workShift){
			$list[] = array('empNo' => $workShift->getEmpNumber(), 'empName' => $workShift->getEmployee()->getFullName());
		}
		return $this->renderText(json_encode($list));
	}
}

?>
