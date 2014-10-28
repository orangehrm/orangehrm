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
class displayProjectActivityDetailsReportAction extends displayReportAction {

    public function setConfigurationFactory() {

        $confFactory = new ProjectActivityDetailsReportListConfigurationFactory();

        $this->setConfFactory($confFactory);
    }

    public function setParametersForListComponent() {

        $projectService = new ProjectService();

        $projectId = $this->getRequest()->getParameter("projectId");
        
        $userRoleManager = $this->getContext()->getUserRoleManager();
        $requiredPermissions = array(
            BasicUserRoleManager::PERMISSION_TYPE_DATA_GROUP => array(
                'time_project_reports' => new ResourcePermission(true, false, false, false)
            )
        );
        
        $accessible = $userRoleManager->isEntityAccessible('Project', $projectId, 
                null, array(), array(), $requiredPermissions);                


        $activityId = $this->getRequest()->getParameter("activityId");        
        $activity = $projectService->getProjectActivityById($activityId);
        
                
        if (!$accessible || $activity->getProjectId() != $projectId) {
            $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
        }        
        
        $projectName = $projectService->getProjectNameWithCustomerName($projectId);
        
        $reportGeneratorService = new ReportGeneratorService();
        $activityName = $reportGeneratorService->getProjectActivityNameByActivityId($activityId);
        $params = array(
            'projectName' => $projectName,
            'activityName' => $activityName,
            'projectDateRangeFrom' => $this->getRequest()->getParameter("from"),
            'projectDateRangeTo' => $this->getRequest()->getParameter("to"),
            'total' => $this->getRequest()->getParameter("total")
        );

        return $params;
    }

    public function setListHeaderPartial() {

        ohrmListComponent::setHeaderPartial("time/projectActivityDetailsReportHeader");
    }

    public function setValues() {

        $activityId = $this->getRequest()->getParameter("activityId");
        $fromDate = $this->getRequest()->getParameter("from");
        $toDate = $this->getRequest()->getParameter("to");
        $approved = $this->getRequest()->getParameter("onlyIncludeApprovedTimesheets");
        
        $values = array("activity_name" => $activityId, "project_date_range" => array("from" => $fromDate, "to" => $toDate), "only_include_approved_timesheets" => $approved);

        return $values;
    }
      public function setInitialActionDetails($request) {
        $this->projectReportPermissions = $this->getDataGroupPermissions('time_project_reports');

        $initialActionName = $request->getParameter('initialActionName', '');

        if (empty($initialActionName)) {
            $request->setParameter('initialActionName', 'displayProjectReportCriteria');
        } else {
            $request->setParameter('initialActionName', $initialActionName);
        }        
        
    }

}

