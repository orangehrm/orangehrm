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
class timeActions extends sfActions {

    private $timesheetService;

    public function getTimesheetService() {

        if (is_null($this->timesheetService)) {

            $this->timesheetService = new TimesheetService();
        }

        return $this->timesheetService;
    }

    public function executeAddRow($request) {

        $this->backAction = $this->getContext()->getUser()->getFlash('actionName');
        $this->getContext()->getUser()->setFlash('actionName', $this->backAction);
        $startDate = $request->getParameter("startDate");
        $endDate = $request->getParameter("endDate");
        $employeeId = $request->getParameter("employeeId");
        $timesheetId = $request->getParameter("timesheetId");
        $noOfDays = $this->getTimesheetService()->dateDiff($startDate, $endDate);
        $values = array('date' => $startDate, 'employeeId' => $employeeId, 'timesheetId' => $timesheetId, 'noOfDays' => $noOfDays);
        $form = new TimesheetForm(array(), $values);
        $form->addRow($request->getParameter("num"), $values);
        return $this->renderPartial('addRow', array('form' => $form, 'num' => $request->getParameter("num"), 'noOfDays' => $noOfDays));
    }

    public function executeTestDao() {

        $ex = new TimesheetService();
    }

    public function executeUpdateTimesheetItemComment($request) {

        $this->backAction = $this->getContext()->getUser()->getFlash('actionName');
        $this->getContext()->getUser()->setFlash('actionName', $this->backAction);

        $timesheetId = $request->getParameter('timesheetId');
        $activityId = $request->getParameter('activityId');
        $projectId = $this->getProjectId($activityId);
        $date = $request->getParameter('date');
        $comment = $request->getParameter('comment');
        
        $csrfToken = $request->getParameter('csrfToken');
        $form = new TimesheetFormToImplementCsrfTokens();
        if($form->getCSRFToken() != $csrfToken){
            return sfView::NONE;
        }
        
        $employeeId = $request->getParameter('employeeId');
        $dao = new TimesheetDao();
        $timesheetItem = $dao->getTimesheetItemByDateProjectId($timesheetId, $employeeId, $projectId, $activityId, $date);
        if ($timesheetItem[0]->getTimesheetItemId() == null) {
            $newTimesheetItem = new TimesheetItem();
            $newTimesheetItem->setTimesheetId($timesheetId);
            $newTimesheetItem->setDate($date);
            $newTimesheetItem->setComment(trim($comment));
            $newTimesheetItem->setProjectId($projectId);
            $newTimesheetItem->setEmployeeId($employeeId);
            $newTimesheetItem->setActivityId($activityId);

            $resultItem = $dao->saveTimesheetItem($newTimesheetItem);
        } else {
            $timesheetItem[0]->setComment(trim($comment));
            $resultItem = $dao->saveTimesheetItem($timesheetItem[0]);
        }
        
        return $this->renderText($resultItem->getTimesheetItemId());
    }

    public function executeShowTimesheetItemComment($request) {

        $this->backAction = $this->getContext()->getUser()->getFlash('actionName');
        $this->getContext()->getUser()->setFlash('actionName', $this->backAction);

        $timesheetItemId = $request->getParameter('timesheetItemId');
        $timesheetItem = $this->getTimesheetService()->getTimesheetItemById($timesheetItemId);
        $comment = $timesheetItem->getComment();
        $date = $timesheetItem->getDate();
        $this->dataArray = $comment . "##" . $date;
    }

    public function executeGetTimesheetItemComment($request) {


        $timesheetId = $request->getParameter('timesheetId');
        $activityId = $request->getParameter('activityId');
        $projectId = $this->getProjectId($activityId);
        $date = $request->getParameter('date');
        $employeeId = $request->getParameter('employeeId');
        $dao = new TimesheetDao();
        $timesheetItem = $dao->getTimesheetItemByDateProjectId($timesheetId, $employeeId, $projectId, $activityId, $date);

        $this->comment = $timesheetItem[0]->getComment();
        return $this->renderText($this->comment);
    }

    public function executeGetRelatedActiviesForAutoCompleteAjax(sfWebRequest $request) {

//        $this->backAction = $this->getContext()->getUser()->getFlash('actionName');
        $this->getContext()->getUser()->setFlash('actionName', $this->backAction);
//
//        $customerName = $request->getParameter('customerName');
//
//        $projectName = $request->getParameter('projectName');
////        $projectName = htmlspecialchars($projectName, ENT_QUOTES);
////        $customerName = htmlspecialchars($customerName, ENT_QUOTES);
        $timesheetDao = new TimesheetDao();
//        $customer = $timesheetDao->getCustomerByName($customerName);
//        $customerId = $customer->getCustomerId();
//
//        $project = $timesheetDao->getProjectByProjectNameAndCustomerId($projectName, $customerId);
//
        $projectId = $request->getParameter('projectId');

        $this->activityList = $timesheetDao->getProjectActivitiesByPorjectId($projectId);
    }

    public function executeViewPendingApprovelTimesheet(sfWebRequest $request) {


        $employeeId = $request->getParameter("employeeId");
        $timesheetId = $request->getParameter("timesheetId");
        $startDate = $request->getParameter("timesheetStartday");

        $this->getContext()->getUser()->setFlash('timesheetId', $timesheetId);
        $this->getContext()->getUser()->setFlash('TimesheetStartDate', $startDate);

        $this->redirect('time/viewTimesheet?' . http_build_query(array('employeeId' => $employeeId)));
    }

    public function getProjectId($activityId) {

        $timesheetService = new TimesheetService();
        $activity = $timesheetService->getActivityByActivityId($activityId);
        if ($activity != null) {
            $projectId = $activity->getProjectId();
            return $projectId;
        } else {
            return null;
        }
    }

    public function executeDeleteRows(sfWebRequest $request) {
        $form = new DefaultListForm();
        
        $employeeId = $request->getParameter("employeeId");
        $timesheetId = $request->getParameter("timesheetId");
        $projectId = $request->getParameter('projectId');
        $activityId = $request->getParameter('activityId');
        
        $timesheetService = new TimesheetService();
            if( $form->getCSRFToken() == $request->getParameter('t')){
                $this->state = $timesheetService->deleteTimesheetItems($employeeId, $timesheetId, $projectId, $activityId);
            }
        
    }

    public function executeOverLappingTimesheetError(sfWebRequest $request) {

        $this->messageData = array('NOTICE', __("No Timesheet Found For Current Date"));
    }

    public function executeCreateTimesheet(sfWebRequest $request) {

        $this->setLayout(false);
        sfConfig::set('sf_web_debug', false);
        sfConfig::set('sf_debug', false);



        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
        }

        $employeeId = $request->getParameter("employeeId");
        $startDate = $request->getParameter("startDate");

//        $userRoleFactory = new UserRoleFactory();
//        $decoratedUser = $userRoleFactory->decorateUserRole($userId, $employeeId, $userEmployeeNumber);
//
//        $allowedActions = $decoratedUser->getAllowedActions(PluginWorkflowStateMachine::FLOW_TIME_TIMESHEET, PluginTimesheet::STATE_INITIAL);
        $statusArray = $this->getTimesheetService()->createTimesheets($startDate, $employeeId);
        switch ($statusArray['state']) {

            case $statusArray['state'] == 1:
                $msg = "1";
                return $this->renderText(json_encode($msg));
            case $statusArray['state'] == 2:
                $msg = array("2", $statusArray['startDate']);

                return $this->renderText(json_encode($msg));
                //  $msg = __("Timesheet created successfully");

                break;
            case $statusArray['state'] == 3:
                $msg = "3";
//                $msg = __("Timesheet already exists");
                return $this->renderText(json_encode($msg));
                break;
        }
    }

//    public function executeValidateStartDate(sfWebRequest $request) {
//
////        $this->setLayout(false);
////        sfConfig::set('sf_web_debug', false);
////        sfConfig::set('sf_debug', false);
////
////        if ($this->getRequest()->isXmlHttpRequest()) {
////            $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
////        }
//        $startDate = $request->getParameter("startDate");
//        $this->status = $this->getTimesheetService()->validateStartDate($startDate);
//        return $this->status;
//        // return $this->renderText(json_encode($status));
//    }

    public function executeReturnEndDate($request) {

        $startDate = $request->getParameter("startDate");
        $this->endDate = $this->getTimesheetService()->returnEndDate($startDate);
    }

}

