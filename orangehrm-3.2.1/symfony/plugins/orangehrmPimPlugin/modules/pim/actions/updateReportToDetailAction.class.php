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

/**
 * Actions class for PIM module updateMembership
 */
class updateReportToDetailAction extends basePimAction {

    private $reportingMethodConfigurationService;

    public function getReportingMethodConfigurationService() {

        if (!($this->reportingMethodConfigurationService instanceof ReportingMethodConfigurationService)) {
            $this->reportingMethodConfigurationService = new ReportingMethodConfigurationService();
        }

        return $this->reportingMethodConfigurationService;
    }

    public function setReportingMethodConfigurationService($reportingMethodConfigurationService) {
        $this->reportingMethodConfigurationService = $reportingMethodConfigurationService;
    }

    /**
     * Add / update employee membership
     *
     * @param int $empNumber Employee number
     *
     * @return boolean true if successfully assigned, false otherwise
     */
    public function execute($request) {

        $memberships = $request->getParameter('reportto');
        $empNumber = (isset($memberships['empNumber'])) ? $memberships['empNumber'] : $request->getParameter('empNumber');
        $this->empNumber = $empNumber;

        $this->reportToPermissions = $this->getDataGroupPermissions(array('supervisor', 'subordinates'), $empNumber);
        $reportToSupervisorPermission = $this->getDataGroupPermissions('supervisor', $empNumber);
        $reportToSubordinatePermission = $this->getDataGroupPermissions('subordinates', $empNumber);
        
        $loggedInEmpNum = $this->getUser()->getEmployeeNumber();
        $adminMode = $this->getUser()->hasCredential(Auth::ADMIN_ROLE);
        $essMode = !$adminMode && !empty($loggedInEmpNum) && ($empNumber == $loggedInEmpNum);
        $param = array('empNumber' => $empNumber, 'ESS' => $essMode, 'reportToPermissions' => $this->reportToPermissions,
                'reportToSupervisorPermission' => $reportToSupervisorPermission, 'reportToSubordinatePermission' => $reportToSubordinatePermission);

        $this->form = new EmployeeReportToForm(array(), $param, true);

        if ($this->getRequest()->isMethod('post')) {

            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                If ($this->reportToPermissions->canUpdate() || $this->reportToPermissions->canCreate()) {

                    $this->_checkDuplicateEntry($empNumber);

                    $value = $this->form->save();
                    
                    if ($value[2] == 'failed') {
                        $this->getUser()->setFlash('failure', __(TopLevelMessages::SAVE_FAILURE));
                    } else if ($value[2] == 'updated') {
                        $this->getUser()->setFlash('success', __(TopLevelMessages::UPDATE_SUCCESS));
                    } else if ($value[2] == 'saved') {
                        $this->getUser()->setFlash('success', __(TopLevelMessages::SAVE_SUCCESS));
                    }
                }
            }
        }

        $empNumber = $request->getParameter('empNumber');

        $section = ($this->form->getValue('type_flag') == ReportTo::SUPERVISOR) ? 'supervisor' : 'subordinates';
        $this->getUser()->setFlash('reportTo', $section);
        $this->redirect('pim/viewReportToDetails?empNumber=' . $empNumber);
    }

    protected function _checkDuplicateEntry($empNumber) {

        if (empty($id) && $this->getReportingMethodConfigurationService()->isExistingReportingMethodName($this->form->getValue('reportingMethod'))) {
            $this->getUser()->setFlash('warning', __('Name Already Exists'));
            $this->redirect('pim/viewReportToDetails?empNumber=' . $empNumber);
        }
    }

}
