<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of saveCustomerJsonAction
 *
 * @author orangehrm
 */
class saveCustomerJsonAction extends sfAction {

	/**
	 *
	 * @param <type> $request
	 * @return <type>
	 */
	public function execute($request) {

		$this->setLayout(false);
		sfConfig::set('sf_web_debug', false);
		sfConfig::set('sf_debug', false);
                
                $csrfToken = $request->getParameter('csrfToken');
                $form = new TimesheetFormToImplementCsrfTokens();
                if ($form->getCSRFToken() != $csrfToken) {
                    return sfView::NONE;
                }

		if ($this->getRequest()->isXmlHttpRequest()) {
			$this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
		}

		$customerName = $request->getParameter('customerName');
		$description = $request->getParameter('description');

		$customer = new Customer();
		$customer->setName($customerName);
		$customer->setDescription($description);
		$customer->save();
		
		$array = array('id' => $customer->getCustomerId());
		return $this->renderText(json_encode($array));
	}

}

?>
