<?php

class displayAttendanceSummaryReportAction extends displayReportAction {

    public function setConfigurationFactory() {

        $confFactory = new AttendanceSummaryReportListConfigurationFactory();

        $this->setConfFactory($confFactory);
    }

    public function setListHeaderPartial() {

        ohrmListComponent::setHeaderPartial("time/attendanceSummaryReportHeader");
    }

    public function setParametersForListComponent() {
        $param = array();

        if ($this->getRequest()->hasParameter("empName")) {
            $param['empName'] = $this->getRequest()->getParameter("empName");
        }

        if ($this->getRequest()->hasParameter("employeeStatus")) {
            $param['employeeStatus'] = $this->getRequest()->getParameter("employeeStatus");
        }

        if ($this->getRequest()->hasParameter("jobTitle")) {
            $param['jobTitle'] = $this->getRequest()->getParameter("jobTitle");
        }

        if ($this->getRequest()->hasParameter("subUnit")) {
            $param['subUnit'] = $this->getRequest()->getParameter("subUnit");
        }

        $param['attendanceDateRangeFrom'] = $this->getRequest()->getParameter("attendanceDateRangeFrom");
        $param['attendanceDateRangeTo'] = $this->getRequest()->getParameter("attendanceDateRangeTo");

        return $param;
    }

    public function setValues() {

    }

    public function setReportCriteriaInfoInRequest($formValues) {
        $employeeService = new EmployeeService();
        $empStatusService = new EmploymentStatusService();
        $jobTitleService = new JobTitleService();
        $companyStructureService = new CompanyStructureService();

        if (isset($formValues["employeeId"]) || ($formValues["employeeId"] == '-1')) {

            if ($formValues["employeeId"] != '-1') {
                $empNumber = $formValues["employeeId"];
                $employee = $employeeService->getEmployee($empNumber);
                $empName = $employee->getFirstAndLastNames();
            } else {
                $empName = __("All");
            }
            $this->getRequest()->setParameter('empName', $empName);
        }

        if (isset($formValues["employeeStatus"]) && ($formValues["employeeStatus"] != 0)) {
            $estatCode = $formValues["employeeStatus"];
            $estat = $empStatusService->getEmploymentStatusById($estatCode);
            $estatName = $estat->getName();
            $this->getRequest()->setParameter("employeeStatus", $estatName);
        }

        if (isset($formValues["jobTitle"]) && ($formValues["jobTitle"] != 0)) {
            $jobTitCode = $formValues["jobTitle"];
            $jobTitle = $jobTitleService->getJobTitleById($jobTitCode);
            $jobTitName = $jobTitle->getJobTitleName();
            $this->getRequest()->setParameter("jobTitle", $jobTitName);
        }

        if (isset($formValues["subUnit"]) && ($formValues["subUnit"] != 0)) {
            $value = $formValues["subUnit"];
            $id = $value;
            $subunit = $companyStructureService->getSubunitById($id);
            $subUnitName = $subunit->getName();
            $this->getRequest()->setParameter("subUnit", $subUnitName);
        }
        $this->getRequest()->setParameter('attendanceDateRangeFrom', $formValues["fromDate"]);
        $this->getRequest()->setParameter('attendanceDateRangeTo', $formValues["toDate"]);
    }

    public function setCriteriaForm() {
        $form = new AttendanceTotalSummaryReportForm();
        $this->setForm($form);
    }
    public function setInitialActionDetails($request) {
        $this->attendancePermissions = $this->getDataGroupPermissions('attendance_summary');

        $initialActionName = $request->getParameter('initialActionName', '');

        if (empty($initialActionName)) {
            $request->setParameter('initialActionName', 'displayAttendanceSummaryReportCriteria');
        } else {
            $request->setParameter('initialActionName', $initialActionName);
        }        
        
    }

}
