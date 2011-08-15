<?php

class displayAttendanceSummaryReportAction extends displayReportAction {

    public function execute($request) {

        $reportId = $request->getParameter("reportId");

//        $form = new AttendanceTotalSummaryReportForm();
//        $reportableService = new ReportableService();
//        $report = $reportableService->getReport($reportId);
//        $useFilterField = $report->getUseFilterField();
//
//        if (!$useFilterField) {
//
//            if ($request->isMethod('post')) {
//
//                $form->bind($request->getParameter($form->getName()));
//
//                if ($form->isValid()) {
//
//                    $reportGeneratorService = new ReportGeneratorService();
//                    $formValues = $form->getValues();
//                    $sql = $reportGeneratorService->generateSqlForNotUseFilterFieldReports($reportId, $formValues);
//                }
//            }
//        }
    }

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

        if ($this->getRequest()->hasParameter("empStatusName")) {
            $param['empStatusName'] = $this->getRequest()->getParameter("empStatusName");
        }

        if ($this->getRequest()->hasParameter("jobTitName")) {
            $param['jobTitName'] = $this->getRequest()->getParameter("jobTitName");
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
        $jobService = new JobService();
        $companyService = new CompanyService();

        if (isset($formValues["employee"])) {
            $empNumber = $formValues["employee"];
            $employee = $employeeService->getEmployee($empNumber);
            $empName = $employee->getFirstAndLastNames();
            $this->getRequest()->setParameter('empName', $empName);
        }

        if (isset($formValues["employment_status"]) && ($formValues["employment_status"]!=0)) {
            $estatCode = $formValues["employment_status"];
            $estat = $jobService->readEmployeeStatus($estatCode);
            $estatName = $estat->getEstatName();
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
            $companyStructure = $companyService->readCompanyStructure($id);
            $subUnitName = $companyStructure->getTitle();
            $this->getRequest()->setParameter("subUnit", $subUnitName);
        }

        $this->getRequest()->setParameter('attendanceDateRangeFrom', $formValues["attendance_date_range"]["from"]);
        $this->getRequest()->setParameter('attendanceDateRangeTo', $formValues["attendance_date_range"]["to"]);
    }

}
