<?php

class displayAttendanceSummaryReportAction extends displayReportAction {

    public function execute($request) {

        $reportId = $request->getParameter("reportId");
        
        $form = new AttendanceTotalSummaryReportForm();
        $reportableService = new ReportableService();
        $report = $reportableService->getReport($reportId);
        $useFilterField = $report->getUseFilterField();
        
        if (!$useFilterField) {
           
            if ($request->isMethod('post')) {

                $form->bind($request->getParameter($form->getName()));
                
                if ($form->isValid()) {
                     
                    $reportGeneratorService = new ReportGeneratorService();
                    $formValues = $form->getValues();
                    $sql = $reportGeneratorService->generateSqlForNotUseFilterFieldReports($reportId, $formValues);
                }
            }
        }
    }

    public function setConfigurationFactory() {

    }

    public function setListHeaderPartial() {

    }

    public function setParametersForListComponent() {

    }

    public function setValues() {

    }

}
