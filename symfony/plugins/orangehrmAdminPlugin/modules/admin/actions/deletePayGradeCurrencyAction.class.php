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
class deletePayGradeCurrencyAction extends baseAdminAction {

    private $payGradeService;

    public function getPayGradeService() {
        if (is_null($this->payGradeService)) {
            $this->payGradeService = new PayGradeService();
            $this->payGradeService->setPayGradeDao(new PayGradeDao());
        }
        return $this->payGradeService;
    }

    /**
     *
     * @param <type> $request 
     */
    public function execute($request) {

        $payGradePermissions = $this->getDataGroupPermissions('pay_grades');

        $payGradeId = $request->getParameter('payGradeId');
        $this->form = new DeletePayGradeCurrenciesForm();

        $this->form->bind($request->getParameter($this->form->getName()));
        if ($this->form->isValid()) {
            if ($payGradePermissions->canCreate() || $payGradePermissions->canUpdate()) {
                $currenciesToDelete = $request->getParameter('delCurrencies', array());
                if ($currenciesToDelete) {
                    for ($i = 0; $i < sizeof($currenciesToDelete); $i++) {
                        $currency = $this->getPayGradeService()->getCurrencyByCurrencyIdAndPayGradeId($currenciesToDelete[$i], $payGradeId);
                        $currency->delete();
                    }
                }

                $this->getUser()->setFlash('success', __(TopLevelMessages::DELETE_SUCCESS));
            }

            $this->redirect('admin/payGrade?payGradeId=' . $payGradeId . '#Currencies');
        }else{
            $this->redirect($request->getReferer());
        }
    }

}

?>
