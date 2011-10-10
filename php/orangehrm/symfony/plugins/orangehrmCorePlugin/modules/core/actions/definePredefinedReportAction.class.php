<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of displayPredefinedReportAction
 *
 * @author ruchira
 */
class definePredefinedReportAction extends sfAction {

    public function execute($request) {

        $adminMode = $this->getUser()->hasCredential(Auth::ADMIN_ROLE);

        if (!$adminMode) {
            return $this->renderText("You are not allowed to view this page!");
        }
        
        $reportGeneratorService = new ReportGeneratorService();

        // Hard coded for now. Should be taken from request
        $pimReportGroup = 3;

        $reportableService = new ReportableService();

        $displayFieldGroups = $reportableService->getGroupedDisplayFieldsForReportGroup($pimReportGroup);
        $filterWidgets = $reportableService->getFilterFieldsForReportGroup($pimReportGroup);

        $reportId = $request->getParameter('reportId');
        $reportName;

        if (!empty($reportId)) {
            $report = $reportableService->getReport($reportId);
            if (empty($report)) {
                $this->getUser()->setFlash('templateMessage', array('warning', __('Invalid Report Specified')));
                $this->redirect('core/viewDefinedPredefinedReports'); 
            }
            
            $reportName = $reportGeneratorService->getReportName($reportId);
            
        }
        $ohrmFormGenerator = new ohrmFormGenerator();
        $this->form = $ohrmFormGenerator->generatePredefinedForm($filterWidgets, $displayFieldGroups, $reportId, $reportName);

        if ($request->isMethod('post')) {
            $this->form->bind($request->getParameter($this->form->getName()));
            
            $selectedDisplayFieldGroups = $request->getParameter('display_groups');
            $selectedDisplayFields = $request->getParameter('display_fields');
            
            $this->form->selectedDisplayFieldGroups = $selectedDisplayFieldGroups;
            $this->form->selectedDisplayFields = $selectedDisplayFields;
    
            if ($this->form->isValid()) {

                $reportId = $this->form->getValue('report_id');
                $reportName = $this->form->getValue('report_name');


                $selectedFilterValues = $this->form->getSelectedFilterValues();
                
                // If report_id not available, create report
                if (empty($reportId)) {
                    $report = $reportableService->saveReport($reportName, $pimReportGroup, true, 'PIM_DEFINED');
                    $reportId = $report->getReportId();
                } else {
                    $report = $reportableService->getReport($reportId);

                    // update report name if required
                    if ($reportName != $report->getName()) {
                        $reportableService->updateReportName($reportId, $reportName);
                    }
                }

                // save selected values.                
                $reportGeneratorService->saveSelectedDisplayFieldGroups($selectedDisplayFieldGroups, $reportId);
                $reportGeneratorService->saveSelectedDisplayFields($selectedDisplayFields, $reportId);
                $reportGeneratorService->saveSelectedFilterFields($selectedFilterValues, $reportId, 'Predefined');
                
                $this->getUser()->setFlash('templateMessage', array('success', __('Report Successfully Saved')));
                $this->redirect('core/viewDefinedPredefinedReports'); 
                return;
            } else {
                list($this->messageType, $this->message) = array('warning', __('Please Correct The Following Errors'));

/*                $errors = $this->form->getErrorSchema();
                foreach ($errors as $error) {
                    echo $error . "<br />";
                }

                foreach ($this->form->getWidgetSchema()->getPositions() as $widgetName) {
                    $err = $this->form[$widgetName]->renderError();

                    if (!empty($err)) {
                        echo $widgetName . '=' . $err;
                    }
                } */
            }
        } else {
            // Get filters, display field groups and display fields for report
            if (!empty($reportId)) {
                $selectedDisplayFieldGroups = $reportableService->getSelectedDisplayFieldGroups($reportId);
                $selectedDisplayFields = $reportableService->getSelectedDisplayFields($reportId);
                $selectedFilterFields = $reportableService->getSelectedFilterFieldNames($reportId, "Predefined");  
                $this->form->updateSelectedDisplayFieldGroups($selectedDisplayFieldGroups);
                $this->form->updateSelectedDisplayFields($selectedDisplayFields);
                $this->form->updateSelectedFilterFields($selectedFilterFields);
                
               
            }
        }
    }

}

?>
