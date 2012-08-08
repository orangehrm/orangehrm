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
		$employeeNameList = $service->getWorkShiftEmployeeNameListById($workShiftId);
        
		$list = array();
		foreach ($employeeNameList as $employee){
		    
		    $empNumber = $employee['empNumber'];
		    $name = trim(trim($employee['firstName'] . ' ' . $employee['middleName'],' ') . ' ' . $employee['lastName']);
			
		    $list[] = array('empNo' => $empNumber, 'empName' => $name);
		}
		return $this->renderText(json_encode($list));
	}
}

?>
