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

        //  $comment = utf8_encode($comment1);
        $employeeId = $request->getParameter('employeeId');
        $dao = new TimesheetDao();
        $timesheetItem = $dao->getTimesheetItemByDateProjectId($timesheetId, $employeeId, $projectId, $activityId, $date);
        if ($timesheetItem[0]->getTimesheetItemId() == null) {
            $newTimesheetItem = new TimesheetItem();
            $newTimesheetItem->setTimesheetId($timesheetId);
            $newTimesheetItem->setDate($date);
            $newTimesheetItem->setComment($comment);
            $newTimesheetItem->setProjectId($projectId);
            $newTimesheetItem->setEmployeeId($employeeId);
            $newTimesheetItem->setActivityId($activityId);

            $dao->saveTimesheetItem($newTimesheetItem);
        } else {
            $timesheetItem[0]->setComment($comment);
            $dao->saveTimesheetItem($timesheetItem[0]);
        }
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
    }

    public function executeGetRelatedActiviesForAutoCompleteAjax(sfWebRequest $request) {

        $this->backAction = $this->getContext()->getUser()->getFlash('actionName');
        $this->getContext()->getUser()->setFlash('actionName', $this->backAction);

        $customerName = $request->getParameter('customerName');

        $projectName = $request->getParameter('projectName');
        $projectName = htmlspecialchars($projectName, ENT_QUOTES);
        $customerName = htmlspecialchars($customerName, ENT_QUOTES);
        $timesheetDao = new TimesheetDao();
        $customer = $timesheetDao->getCustomerByName($customerName);
        $customerId = $customer->getCustomerId();

        $project = $timesheetDao->getProjectByProjectNameAndCustomerId($projectName, $customerId);

        $projectId = $project->getProjectId();

        $this->activityList = $timesheetDao->getProjectActivitiesByPorjectId($projectId, true);
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
        $employeeId = $request->getParameter("employeeId");
        $timesheetId = $request->getParameter("timesheetId");
        $projectId = $request->getParameter('projectId');
        $activityId = $request->getParameter('activityId');

        $timesheetService = new TimesheetService();
        $this->state = $timesheetService->deleteTimesheetItems($employeeId, $timesheetId, $projectId, $activityId);
    }

    public function executeOverLappingTimesheetError(sfWebRequest $request) {

        $this->messageData = array('NOTICE', __("No Timesheet Found For Current Date"));
    }

}

