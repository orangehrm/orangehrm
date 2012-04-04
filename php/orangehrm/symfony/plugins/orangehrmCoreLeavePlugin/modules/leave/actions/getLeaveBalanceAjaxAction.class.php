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
 * Get leave balance for given employee for given leave type
 *
 */
class getLeaveBalanceAjaxAction extends sfAction {

    protected $leavePeriodService;
    protected $leaveEntitlementService;

    /**
     * Get leave balance for given leave type
     * Request parameters:
     * *) leaveType: Leave Type ID
     * *) empNumber: (optional) employee number. If not present, currently
     *               logged in employee is used.
     * 
     * @param sfWebRequest $request
     */
    public function execute($request) {
        sfConfig::set('sf_web_debug', false);
        sfConfig::set('sf_debug', false);

        $leaveTypeId = $request->getParameter('leaveType');
        $empNumber = $request->getParameter('empNumber');

        $user = $this->getUser();
        $loggedEmpNumber = $user->getAttribute('auth.empNumber');

        $allowed = false;

        if (empty($empNumber)) {
            $empNumber = $loggedEmpNumber;
            $allowed = true;
        } else {

            $manager = $this->getContext()->getUserRoleManager();
            if ($manager->isEntityAccessible('Employee', $empNumber)) {
                $allowed = true;
            } else {
                $allowed = ($loggedEmpNumber == $empNumber);
            }
        }

        $response = $this->getResponse();
        $response->setHttpHeader('Expires', '0');
        $response->setHttpHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0");
        $response->setHttpHeader("Cache-Control", "private", false);

        if ($allowed) {
            $localizationService = new LocalizationService();
            $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
            $startDateTimeStamp = strtotime($localizationService->convertPHPFormatDateToISOFormatDate($inputDatePattern, $request->getParameter("startDate")));

            if ($startDateTimeStamp) {
                $leavePeriod = $this->getLeavePeriodService()->getLeavePeriod($startDateTimeStamp);
            } else {
                $leavePeriod = $this->getLeavePeriodService()->getCurrentLeavePeriod();
            }

            $balance = '--';
            if ($leavePeriod instanceof LeavePeriod) {
                $balance = $this->getLeaveEntitlementService()->getLeaveBalance($empNumber, $leaveTypeId, $leavePeriod->getLeavePeriodId());
            }

            echo json_encode($balance);
        }

        return sfView::NONE;
    }

    /**
     * @return LeavePeriodService
     */
    public function getLeavePeriodService() {
        if (is_null($this->leavePeriodService)) {
            $leavePeriodService = new LeavePeriodService();
            $leavePeriodService->setLeavePeriodDao(new LeavePeriodDao());
            $this->leavePeriodService = $leavePeriodService;
        }
        return $this->leavePeriodService;
    }

    /**
     *
     * @param LeavePeriodService $leavePeriodService
     */
    public function setLeavePeriodService(LeavePeriodService $leavePeriodService) {
        $this->leavePeriodService = $leavePeriodService;
    }

    /**
     * @return LeaveEntitlementService
     */
    public function getLeaveEntitlementService() {
        if (is_null($this->leaveEntitlementService)) {
            $this->leaveEntitlementService = new LeaveEntitlementService();
        }
        return $this->leaveEntitlementService;
    }

    /**
     *
     * @param LeavePeriodService $leavePeriodService
     */
    public function setLeaveEntitlementService(LeaveEntitlementService $leaveEntitlementService) {
        $this->leaveEntitlementService = $leaveEntitlementService;
    }

}

