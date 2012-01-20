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
class displayAttendanceTotalSummaryReportCriteriaAction extends displayReportCriteriaAction {

    public function setReportCriteriaInfoInRequest($formValues) {

        $employeeService = new EmployeeService();
        $jobService = new JobService();
	$empStatusService = new EmploymentStatusService();
        $companyStructureService = new CompanyStructureService();
        
        if (isset($formValues["employee"])) {
            $empNumber = $formValues["employee"];
            $employee = $employeeService->getEmployee($empNumber);
            $empName = $employee->getFirstAndLastNames();
            $this->getRequest()->setParameter('empName', $empName);
        }

        if (isset($formValues["employment_status"]) && ($formValues["employment_status"]!=0)) {
            $estatCode = $formValues["employment_status"];
            $estat = $empStatusService->getEmploymentStatusById($estatCode);
            $estatName = $estat->getName();
            $this->getRequest()->setParameter("empStatusName", $estatName);
        }

        if (isset($formValues["job_title"]) && ($formValues["job_title"]!=0)) {
            $jobTitCode = $formValues["job_title"];
            $jobTitle = $jobService->readJobTitle($jobTitCode);
            $jobTitName = $jobTitle->getJobTitName();
            $this->getRequest()->setParameter("jobTitName", $jobTitName);
        }

        if (isset($formValues["sub_unit"]) && ($formValues["job_title"]!=0)) {
            $value = $formValues["sub_unit"];
            $id = $value;
            $subunit = $companyStructureService->getSubunitById($id);
            $subUnitName = $subunit->getName();
            $this->getRequest()->setParameter("subUnit", $subUnitName);
        }

        $this->getRequest()->setParameter('attendanceDateRangeFrom', $formValues["attendance_date_range"]["from"]);
        $this->getRequest()->setParameter('attendanceDateRangeTo', $formValues["attendance_date_range"]["to"]);
    }

    public function setForward() {

        $this->forward('time', 'displayAttendanceTotalSummaryReport');
    }

    public function setStaticColumns($formValues) {

    }

}

