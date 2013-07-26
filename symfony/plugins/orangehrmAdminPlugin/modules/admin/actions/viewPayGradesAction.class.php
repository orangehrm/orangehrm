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
class viewPayGradesAction extends baseAdminAction {

    private $payGradeService;

    public function getPayGradeService() {
        if (is_null($this->payGradeService)) {
            $this->payGradeService = new PayGradeService();
            $this->payGradeService->setPayGradeDao(new PayGradeDao());
        }
        return $this->payGradeService;
    }

    public function execute($request) {

        $usrObj = $this->getUser()->getAttribute('user');

        $this->payGradePermissions = $this->getDataGroupPermissions('pay_grades');

        if ($this->payGradePermissions->canRead()) {
            $sortField = $request->getParameter('sortField');
            $sortOrder = $request->getParameter('sortOrder');

            $payGradeList = $this->getPayGradeService()->getPayGradeList($sortField, $sortOrder);
            $this->_setListComponent($payGradeList, $this->payGradePermissions);
            $params = array();
            $this->parmetersForListCompoment = $params;
        }
    }

    private function _setListComponent($payGradeList, $permissions) {
        $runtimeDefinitions = array();
        $buttons = array();

        if ($permissions->canCreate()) {
            $buttons['Add'] = array('label' => 'Add');
        }

        if (!$permissions->canDelete()) {
            $runtimeDefinitions['hasSelectableRows'] = false;
        } else if ($permissions->canDelete()) {
            $buttons['Delete'] = array('label' => 'Delete',
                'type' => 'submit',
                'data-toggle' => 'modal',
                'data-target' => '#deleteConfModal',
                'class' => 'delete');
        }

        $runtimeDefinitions['buttons'] = $buttons;

        $configurationFactory = new PayGradeHeaderFactory();
        $configurationFactory->setRuntimeDefinitions($runtimeDefinitions);
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setListData($payGradeList);
    }

}

?>
