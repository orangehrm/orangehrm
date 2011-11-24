<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of savePayGradeCurrencyAction
 *
 * @author orangehrm
 */
class savePayGradeCurrencyAction extends sfAction {

	public function execute($request) {

		$usrObj = $this->getUser()->getAttribute('user');
		if (!$usrObj->isAdmin()) {
			$this->redirect('pim/viewPersonalDetails');
		}

		$this->form = new PayGradeCurrencyForm();
		
		if ($request->isMethod('post')) {

			$this->form->bind($request->getParameter($this->form->getName()));
			if ($this->form->isValid()) {
				$payGradeId = $this->form->save();
				$this->getUser()->setFlash('templateMessage', array('success', __("Pay Grade Currency Saved Successfully")));
				$this->redirect('admin/payGrade?payGradeId='.$payGradeId);
			} else {
				print_r("k");die;
			}
		}
	}

}

?>
