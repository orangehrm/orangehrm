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
class displayProjectReportCriteriaAction extends displayReportCriteriaAction {

    public function execute($request) {
        $hasRight = false;
        
        $this->projectReportPermissions = $this->getDataGroupPermissions('time_project_reports');
        
        if($this->projectReportPermissions->canRead()){
            $hasRight = true;
        }

        if (!$hasRight) {
            return $this->renderText(__("You are not allowed to view this page").'!');
        }
        parent::execute($request);
    }

    public function setReportCriteriaInfoInRequest($formValues) {

        $projectService = new ProjectService();
        $projectId = $formValues["project_name"];
        $projectName = $projectService->getProjectNameWithCustomerName($projectId);

        $this->getRequest()->setParameter('projectName', $projectName);
        $this->getRequest()->setParameter('projectDateRangeFrom', $formValues["project_date_range"]["from"]);
        $this->getRequest()->setParameter('projectDateRangeTo', $formValues["project_date_range"]["to"]);
    }

    public function setForward() {
        $this->forward('time', 'displayProjectReport');
    }

    public function hasStaticColumns() {
        return true;
    }

    public function setStaticColumns($formValues) {

        $staticColumns["fromDate"] = "";
        $staticColumns["toDate"] = "";
        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
        $datepickerDateFormat = get_datepicker_date_format($inputDatePattern);

        if (($formValues["project_date_range"]["from"] != $datepickerDateFormat) && ($formValues["project_date_range"]["to"] != $datepickerDateFormat)) {

            if ($formValues["project_date_range"]["from"] != '') {
                $staticColumns["fromDate"] = $formValues["project_date_range"]["from"];
            }
            if ($formValues["project_date_range"]["to"] != '') {
                $staticColumns["toDate"] = $formValues["project_date_range"]["to"];
            }
        } else if (($formValues["project_date_range"]["from"] != $datepickerDateFormat) && ($formValues["project_date_range"]["to"] == $datepickerDateFormat)) {

            if ($formValues["project_date_range"]["from"] != '') {
                $staticColumns["fromDate"] = $formValues["project_date_range"]["from"];
            }
        } else if (($formValues["project_date_range"]["from"] == $datepickerDateFormat) && ($formValues["project_date_range"]["to"] != $datepickerDateFormat)) {

            if ($formValues["project_date_range"]["to"] != '') {
                $staticColumns["toDate"] = $formValues["project_date_range"]["to"];
            }
        }

        $staticColumns["projectId"] = $formValues["project_name"];

        if ($formValues["only_include_approved_timesheets"] == "on") {
            $staticColumns["onlyIncludeApprovedTimesheets"] = "on";
        } else {
            $staticColumns["onlyIncludeApprovedTimesheets"] = "off";
        }

        return $staticColumns;
    }

}

