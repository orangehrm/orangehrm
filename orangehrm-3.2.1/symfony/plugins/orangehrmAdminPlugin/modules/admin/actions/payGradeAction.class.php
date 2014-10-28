<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */
class payGradeAction extends baseAdminAction {

    /**
     * @param sfForm $form
     * @return
     */
    public function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }

    public function getPayGradeService() {
        if (is_null($this->payGradeService)) {
            $this->payGradeService = new PayGradeService();
            $this->payGradeService->setPayGradeDao(new PayGradeDao());
        }
        return $this->payGradeService;
    }

    public function execute($request) {

        /* For highlighting corresponding menu item */
        $request->setParameter('initialActionName', 'viewPayGrades');

        $usrObj = $this->getUser()->getAttribute('user');

        $this->payGradePermissions = $this->getDataGroupPermissions('pay_grades');

        $this->payGradeId = $request->getParameter('payGradeId');
        
        if (!empty($this->payGradeId)) {
            $this->currencyForm = new PayGradeCurrencyForm();
            $this->deleteForm = new DeletePayGradeCurrenciesForm();
            $this->currencyList = $this->getPayGradeService()->getCurrencyListByPayGradeId($this->payGradeId);
            $this->title = $this->payGradePermissions->canUpdate() ? __('Edit Pay Grade') : ('View Pay Grade');
        } else {
            $this->title = __("Add Pay Grade");
        }
        $values = array('payGradeId' => $this->payGradeId, 'payGradePermissions' => $this->payGradePermissions);

        $this->setForm(new PayGradeForm(array(), $values));

        if ($this->getUser()->hasFlash('templateMessage')) {
            list($this->messageType, $this->message) = $this->getUser()->getFlash('templateMessage');
        }

        if ($request->isMethod('post')) {
            if ($this->payGradePermissions->canCreate() || $this->payGradePermissions->canUpdate()) {
                $this->form->bind($request->getParameter($this->form->getName()));
                if ($this->form->isValid()) {
                    $payGradeId = $this->form->save();
                    $this->getUser()->setFlash('paygrade.success', __(TopLevelMessages::SAVE_SUCCESS));
                    $this->redirect('admin/payGrade?payGradeId=' . $payGradeId);
                }
            }
        } else {
            // check permissions
            if ((empty($this->payGradeId) && !$this->payGradePermissions->canCreate()) 
                    || (!empty($this->payGradeId) && !$this->payGradePermissions->canRead())) {
                $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
            }             
        }
    }

}

?>
