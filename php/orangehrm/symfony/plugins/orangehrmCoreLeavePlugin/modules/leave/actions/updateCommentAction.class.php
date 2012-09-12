<?php

/**
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM)
 * System that captures all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property
 * rights to any design, new software, new protocol, new interface, enhancement, update,
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are
 * reserved to OrangeHRM Inc.
 *
 * You should have received a copy of the OrangeHRM Enterprise  proprietary license file along
 * with this program; if not, write to the OrangeHRM Inc. 538 Teal Plaza, Secaucus , NJ 0709
 * to get the file.
 *
 */

/**
 * updateComment
 */
class updateCommentAction extends baseLeaveAction {

    public function execute($request) {

        $leaveRequestService = $this->getLeaveRequestService();
        $leaveRequestId = trim($request->getParameter("leaveRequestId"));
        $leaveId = trim($request->getParameter("leaveId"));
        $comment = trim($request->getParameter("leaveComment"));

        $essMode = $this->isEssMode();

        $flag = 0;
        if ($leaveRequestId != "") {
            $leaveRequest = $leaveRequestService->fetchLeaveRequest($leaveRequestId);
            $statusId = $leaveRequest->getLeaveStatusId();

            if ($essMode && $statusId != PluginLeave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL) { //ess can not comment on leaves other than status 'pending'
                $flag = 0;
            } else {
                $leaveRequest->setLeaveComments($comment);
                $leaves = $leaveRequestService->searchLeave($leaveRequestId);
                $flag = $leaveRequestService->saveLeaveRequest($leaveRequest, $leaves);
            }
        }

        if ($leaveId != "") {
            $leave = $leaveRequestService->readLeave($leaveId);
            $statusId = $leave->getLeaveStatus();

            if ($essMode && $statusId != PluginLeave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL) { //ess can not comment on leaves other than status 'pending'
                $flag = 0;
            } else {
                $leave->setLeaveComments($comment);
                $flag = $leaveRequestService->saveLeave($leave);
            }
        }

        if ($this->getUser()->hasFlash('myLeave')) {
            $this->getUser()->setFlash('myLeave', true);
        }
        
        return $this->renderText($flag);
    }

    protected function isEssMode() {
        $userMode = 'ESS';

        if ($_SESSION['isSupervisor']) {
            $userMode = 'Supervisor';
        }

        if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 'Yes') {
            $userMode = 'Admin';
        }

        return ($userMode == 'ESS');
    }

}