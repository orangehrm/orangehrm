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
class savePayGradeCurrencyAction extends baseAdminAction {

    private $payGradeService;

    public function getPayGradeService() {
        if (is_null($this->payGradeService)) {
            $this->payGradeService = new PayGradeService();
        }
        return $this->payGradeService;
    }
        
    public function execute($request) {

        $payGradePermissions = $this->getDataGroupPermissions('pay_grades');

        $payGradeId = $request->getParameter('payGradeId');
        
        // Check paygrade exists: handle case where it is deleted.
        $payGrade = $this->getPayGradeService()->getPayGradeById($payGradeId);
        
        if (empty($payGrade)) {
            $this->getUser()->setFlash('warning', __("PayGrade not found!"));
            $this->redirect('admin/viewPayGrades');
        }
        $values = array('payGradeId' => $payGradeId);
        $this->form = new PayGradeCurrencyForm(array(), $values);

        if ($request->isMethod('post')) {
            if ($payGradePermissions->canCreate() || $payGradePermissions->canUpdate()) {

                $this->form->bind($request->getParameter($this->form->getName()));
                if ($this->form->isValid()) {
                    $payGradeId = $this->form->save();
                    $this->getUser()->setFlash('success', __(TopLevelMessages::SAVE_SUCCESS));
                    $this->redirect('admin/payGrade?payGradeId=' . $payGradeId . '#Currencies');
                }
            }
        }
    }

}

?>
