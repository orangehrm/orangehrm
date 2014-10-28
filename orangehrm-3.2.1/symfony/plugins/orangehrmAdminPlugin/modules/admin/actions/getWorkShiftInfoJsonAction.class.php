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
                
                $workShiftFields = array(
                    'id' => $workShift->getId(),
                    'name' => $workShift->getName(),
                    'hoursPerDay' => $workShift->getHoursPerDay(),
                    'start_time' => date('H:i', strtotime($workShift->getStartTime())),
                    'end_time' => date('H:i', strtotime($workShift->getEndTime()))
                );

		return $this->renderText(json_encode($workShiftFields));
	}
}

?>
