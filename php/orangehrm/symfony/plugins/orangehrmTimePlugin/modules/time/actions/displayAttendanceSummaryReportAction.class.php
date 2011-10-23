<?php

class displayAttendanceSummaryReportAction extends displayReportAction {

//    public function execute($request) {
//        $reportId = $request->getParameter("reportId");
//
//        $reportableGeneratorService = new ReportGeneratorService();
//
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
//                if ($form->isValid()) {
//                    $reportGeneratorService = new ReportGeneratorService();
//                    $formValues = $form->getValues();
//                    $sql = $reportGeneratorService->generateSqlForNotUseFilterFieldReports($reportId, $formValues);
//                }
//            }
//        }
//
//        $dataSet = $reportableGeneratorService->generateReportDataSet($sql);
//        $headers = $reportableGeneratorService->getHeaders($reportId);
//
//        $this->setConfigurationFactory();
//        $configurationFactory = $this->getConfFactory();
//        $configurationFactory->setHeaders($headers);
//
//        ohrmListComponent::setConfigurationFactory($configurationFactory);
//
//        $this->setListHeaderPartial();
//        ohrmListComponent::setListData($dataSet);
//
//        $this->parmetersForListComponent = $this->setParametersForListComponent();
//    }

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
        $jobService = new JobService();
        $companyService = new CompanyService();

        if (isset($formValues["employeeId"]) || ($formValues["employeeId"] == '-1')) {

            if ($formValues["employeeId"] != '-1') {
                $empNumber = $formValues["employeeId"];
                $employee = $employeeService->getEmployee($empNumber);
                $empName = $employee->getFirstAndLastNames();
            } else {
                $empName = "All";
            }
            $this->getRequest()->setParameter('empName', $empName);
        }

        if (isset($formValues["employeeStatus"]) && ($formValues["employeeStatus"] != 0)) {
            $estatCode = $formValues["employeeStatus"];
            $estat = $jobService->readEmployeeStatus($estatCode);
            $estatName = $estat->getEstatName();
            $this->getRequest()->setParameter("employeeStatus", $estatName);
        }

        if (isset($formValues["jobTitle"]) && ($formValues["jobTitle"] != 0)) {
            $jobTitCode = $formValues["jobTitle"];
            $jobTitle = $jobService->readJobTitle($jobTitCode);
            $jobTitName = $jobTitle->getJobTitName();
            $this->getRequest()->setParameter("jobTitle", $jobTitName);
        }

        if (isset($formValues["subUnit"]) && ($formValues["subUnit"] != 0)) {
            $value = $formValues["subUnit"];
            $id = $value;
            $companyStructure = $companyService->readCompanyStructure($id);
            $subUnitName = $companyStructure->getTitle();
            $this->getRequest()->setParameter("subUnit", $subUnitName);
        }
        //$formValues["fromDate"] = ($formValues["fromDate"] == "") ? '1970-01-01': $formValues["fromDate"];
       // $formValues["toDate"] = ($formValues["toDate"] == "") ? date('Y-m-d'): $formValues["toDate"];
        $this->getRequest()->setParameter('attendanceDateRangeFrom', $formValues["fromDate"]);
        $this->getRequest()->setParameter('attendanceDateRangeTo', $formValues["toDate"]);
    }

    public function setCriteriaForm() {
        $form = new AttendanceTotalSummaryReportForm();
        $this->setForm($form);
    }

}
