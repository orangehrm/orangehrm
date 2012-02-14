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
		$payGradeId = $request->getParameter('payGradeId');
		$values = array('payGradeId' => $payGradeId);
		$this->form = new PayGradeCurrencyForm(array(), $values);
	
		if ($request->isMethod('post')) {

			$this->form->bind($request->getParameter($this->form->getName()));
			if ($this->form->isValid()) {
				$payGradeId = $this->form->save();
				$this->getUser()->setFlash('templateMessage', array('success', __(TopLevelMessages::SAVE_SUCCESS)));
				$this->redirect('admin/payGrade?payGradeId='.$payGradeId);
			}
		}
	}

}

?>
