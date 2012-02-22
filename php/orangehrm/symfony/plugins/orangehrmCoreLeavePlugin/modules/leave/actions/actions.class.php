<?php

/**
 * defineLeavePeriod actions.
 *
 * @package    orangehrm
 * @subpackage coreLeave
 * @author     sujith
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class leaveActions extends sfActions {

    private $leaveRequestService;
    private $leavePeriodService;

    /**
     * Returns Leave Period
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
     * @return LeaveRequestService
     */
    public function getLeaveRequestService() {
        if (is_null($this->leaveRequestService)) {
            $leaveRequestService = new LeaveRequestService();
            $leaveRequestService->setLeaveRequestDao(new LeaveRequestDao());
            $this->leaveRequestService = $leaveRequestService;
        }

        return $this->leaveRequestService;
    }

    /**
     *
     * @param LeaveRequestService $leaveRequestService
     * @return void
     */
    public function setLeaveRequestService(LeaveRequestService $leaveRequestService) {
        $this->leaveRequestService = $leaveRequestService;
    }

    /**
     * Gets the array of dates for a given month
     *
     * @param int $month Month as integer (eg: January = 1, February = 2, ...)
     * @return array Array of days for the given month
     */
    public function executeLoadDatesforMonth(sfWebRequest $request) {

        $month = (int) $request->getParameter('month');
        $isLeapYear = ($request->getParameter('isLeapYear') !== 'false');

        $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
        return $this->renderText(json_encode($this->getLeavePeriodService()->getListOfDates($month, $isLeapYear)));

    }

    /**
     * Gets the end date of the leave period given the start month and start date
     */
    public function executeLoadLeavePeriodEndDate(sfWebRequest $request) {

        $month = (int) $request->getParameter('month');
        $date = (int) $request->getParameter('date');
        $format = $request->getParameter('format', 'F d');

        $endDateElements = explode(' ', $this->getLeavePeriodService()->calculateEndDate($month, $date, null, $format));
        $endDate = __($endDateElements[0]) . ' ' . $endDateElements[1];

        return $this->renderText($endDate);

    }

    /**
     * Checks whether the start date of the current leave period will be a past date, given the start month and start date
     */
    public function executeGetCurrentStartDate(sfWebRequest $request) {

        $month = (int) $request->getParameter('month');
        $date = (int) $request->getParameter('date');

        return $this->renderText($this->getLeavePeriodService()->calculateStartDate($month, $date, null));

    }



    /**
     * Change leave status
     * 
     * @param sfWebRequest $request
     */
    public function executeChangeLeaveStatus(sfWebRequest $request) {

        if ($request->isMethod('post')) {

            $changes = $request->getParameter('leaveRequest');
            $changeType = 'change_leave_request';
            $leaveComments  = $request->getParameter('leaveComments');
            $changeComments = array();

            if (empty($changes)) {
                $changes = $request->getParameter('leave');
                $changeType = 'change_leave';
            }
            
            //this is to bypass the approval/rejection comment
            foreach($changes as $k => $v) {
                if(trim($v) != "") {
                    $changeComments[$k] = $leaveComments[$k];
                }
            }

            $changedByUserType = SystemUser::USER_TYPE_EMPLOYEE;
            
            $mode = $request->getParameter('hdnMode', null);
            
            if ($mode != LeaveListForm::MODE_MY_LEAVE_LIST && $mode != LeaveListForm::MODE_MY_LEAVE_DETAILED_LIST) {
                
                if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']=='Yes') {
                    $changedByUserType = SystemUser::USER_TYPE_ADMIN;
                }
                if ($_SESSION['isSupervisor']) {                                
                    $changedByUserType = SystemUser::USER_TYPE_SUPERVISOR;
                }
            }

            try {

                $this->getLeaveRequestService()->changeLeaveStatus($changes, $changeType, $changeComments, $changedByUserType, $_SESSION['empNumber']);
                $this->getUser()->setFlash('message', __(TopLevelMessages::UPDATE_SUCCESS));
                $this->getUser()->setFlash('messageType', 'success');
            } catch (Exception $e) {
                $this->getUser()->setFlash('message', $e->getMessage());
                $this->getUser()->setFlash('messageType', 'failure');
            }

        }
        
        if ($changedByUserType == SystemUser::USER_TYPE_EMPLOYEE) {

            $url = ($changeType == 'change_leave') ? "leave/viewLeaveRequest" : "leave/viewMyLeaveList";

            if(trim($request->getParameter("id")) != "") {
                $url = $url . "?id=" . $request->getParameter("id");
            }

            if(trim($request->getParameter("hdnMode")) != "") {
                $url = $url . "?hdnMode=" . $request->getParameter("hdnMode");
            }

        } else {

            $url = ($changeType == 'change_leave') ? "leave/viewLeaveRequest" : "leave/viewLeaveList";

            $page = $request->getParameter("currentPage");

            if(trim($request->getParameter("id")) != "") {
                $url = $url . "?id=" . $request->getParameter("id") . "&pageNo=" . $page;
            }else {
                $url = $url . "?pageNo=" . $page;
            }
        }

        $this->redirect($url);
    }

    public function executeUpdateComment(sfWebRequest $request) {

        $leaveRequestService = $this->getLeaveRequestService();
        $leaveRequestId = trim($request->getParameter("leaveRequestId"));
        $leaveId = trim($request->getParameter("leaveId"));
        $comment = trim($request->getParameter("leaveComment"));

        $flag = 0;
        if($leaveRequestId != "") {
            $leaveRequest = $leaveRequestService->fetchLeaveRequest($leaveRequestId);
            $leaveRequest->setLeaveComments($comment);
            $leaves = $leaveRequestService->searchLeave($leaveRequestId);
            $flag = $leaveRequestService->saveLeaveRequest($leaveRequest, $leaves);
        }

        if($leaveId != "") {
            $leave = $leaveRequestService->readLeave($leaveId);
            $leave->setLeaveComments($comment);
            $flag = $leaveRequestService->saveLeave($leave);
        }

        return $this->renderText($flag);

    }

    /**
     *
     * @param sfForm $form
     * @return
     */
    public function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }

    /**
     * Displays a warning for non admin users if Leave Period is not defined
     *
     * @param sfWebRequest $request
     */
    public function executeShowLeavePeriodNotDefinedWarning(sfWebRequest $request) {
        
    }
}
