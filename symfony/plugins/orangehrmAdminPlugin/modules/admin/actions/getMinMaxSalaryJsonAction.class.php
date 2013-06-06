<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of getMinMaxSalaryJsonAction
 *
 * @author orangehrm
 */
class getMinMaxSalaryJsonAction extends sfAction {
	
	 /**
	 * Get Min and Max salary for given salary grade and currency
	 *
	 * @param sfWebRequest $request
	 * @return JSON formatted JobSpec object
	 */
	public function execute($request) {
		$this->setLayout(false);
		sfConfig::set('sf_web_debug', false);
		sfConfig::set('sf_debug', false);

		$salaryGrade = $request->getParameter('salaryGrade');
		$currency = $request->getParameter('currency');

		$minMax = array();

		if ($this->getRequest()->isXmlHttpRequest()) {
			$this->getResponse()->setHttpHeader('Content-Type','application/json; charset=utf-8');
		}

		if (!empty($salaryGrade) && !empty($currency)) {
			$service = new PayGradeService();

			$salaryCurrency = $service->getCurrencyByCurrencyIdAndPayGradeId($currency, $salaryGrade);
			if ($salaryCurrency) {
				$minMax = array('min' => $salaryCurrency->minSalary, 'max' => $salaryCurrency->maxSalary);
			}
		}
		
		return $this->renderText(json_encode($minMax));
	}
}

?>
