<?php
/*
 *
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
 *
*/

/**
 * Displaying viewLeaveSummary UI
 *
 * @author sujith
 */
class viewLeaveSummaryAction extends sfAction {

    /**
     * @param sfForm $form
     * @return
     */
    public function setForm(sfForm $form) {
        if(is_null($this->form)) {
            $this->form	= $form;
        }
    }

    /**
     * Get instance of form used by this action.
     * Allows subclasses to override the form class used in the action.
     *
     * See: sfForm::__construct for parameter description
     */
    protected function getFormInstance($defaults = array(), $options = array(), $CSRFSecret = null) {
        return new LeaveSummaryForm($defaults, $options, $CSRFSecret);
    }

    public function execute($request) {
        $userDetails = $this->getLoggedInUserDetails();
        $searchParam = array();
        $searchParam['employeeId'] = (trim($request->getParameter("employeeId")) != "")?trim($request->getParameter("employeeId")):null;
        $params = array_merge($searchParam, $userDetails);

        $this->setForm($this->getFormInstance(array(), $params, true));
        $this->_setLeaveSummaryRecordsLimit($request);
        $this->form->setRecordsLimitDefaultValue();

        if ($request->isMethod('post')) {
            $this->searchFlag = 1;
            $this->form->bind($request->getParameter($this->form->getName()));

            if ($this->form->isValid()) {

                if ($request->getParameter('hdnAction') == 'save') {
                    $this->form->saveEntitlements($request);
                }

            }
        }

        $this->form->recordsCount = $this->form->getLeaveSummaryRecordsCount();
        $this->form->setPager($request);
    }

    /**
     * Returns Logged in user details
     */
    protected function getLoggedInUserDetails() {
        $userDetails['userType'] = 'ESS';

        if (!empty($_SESSION['empNumber'])) {
            $userDetails['loggedUserId'] = $_SESSION['empNumber'];
        } else {
            $userDetails['loggedUserId'] = 0; // Means default admin
        }

        if ($_SESSION['isSupervisor']) {
            $userDetails['userType'] = 'Supervisor';
        }

        if(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']=='Yes') {
            $userDetails['userType'] = 'Admin';
        }
        return $userDetails;
    }

    private function _setLeaveSummaryRecordsLimit($request) {

        $params = $request->getParameter('leaveSummary');

        if (isset($params['cmbRecordsCount'])) {
            $this->form->recordsLimit = $params['cmbRecordsCount'];
            $this->getUser()->setAttribute('leaveSummaryLimit', $this->form->recordsLimit);
        } elseif ($this->getUser()->hasAttribute('leaveSummaryLimit')) {
            $this->form->recordsLimit = $this->getUser()->getAttribute('leaveSummaryLimit');
        }

    }

    /**
     * Sets user details for testing purposes
     */
    public function setUserDetails($key, $value) {
        $_SESSION[$key] = $value;
    }
}
?>
