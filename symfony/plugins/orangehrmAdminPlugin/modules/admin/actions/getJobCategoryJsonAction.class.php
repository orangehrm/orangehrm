<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of getJobCategoryJsonAction
 *
 * @author orangehrm
 */
class getJobCategoryJsonAction extends sfAction {
	
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

		$jobCatId = $request->getParameter('id');

		$service = new JobCategoryService();
		$status = $service->getJobCategoryById($jobCatId);

		if ($status instanceof JobCategory) {
			return $this->renderText(
				json_encode(
					[
						'id' => $status->getId(),
						'name' => __($status->getName())
					]
				)
			);
		}

		return $this->renderText(json_encode($status->toArray()));
	}
}
