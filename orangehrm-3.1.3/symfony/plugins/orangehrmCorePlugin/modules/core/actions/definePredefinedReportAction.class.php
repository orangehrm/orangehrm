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
class definePredefinedReportAction extends basePimReportAction {

    public function execute($request) {

        /* For highlighting corresponding menu item 
         * TODO: Currently menu item is hard-coded since this action is only used by PIM Reports
         */
        $request->setParameter('initialActionName', 'viewDefinedPredefinedReports');

        $this->reportPermissions = $this->getDataGroupPermissions('pim_reports');

        $this->getReportGroupAndType($request);

        $reportGeneratorService = new ReportGeneratorService();

        $reportableService = new ReportableService();

        $displayFieldGroups = $reportGeneratorService->getGroupedDisplayFieldsForReportGroup($this->reportGroup);
        $filterWidgets = $reportableService->getFilterFieldsForReportGroup($this->reportGroup);

        $reportId = $request->getParameter('reportId');
        $this->reportId = $reportId;       

        if (!empty($reportId)) {
            $report = $reportableService->getReport($reportId);
            if (empty($report)) {
                $this->getUser()->setFlash('templateMessage', array('warning', __('Invalid Report Specified')));
                $this->redirect('core/viewDefinedPredefinedReports');
            }

            $reportName = $reportGeneratorService->getReportName($reportId);
        }
        if ($this->reportPermissions->canRead()) {
            $ohrmFormGenerator = new ohrmFormGenerator();
            $this->form = $ohrmFormGenerator->generatePredefinedForm($filterWidgets, $displayFieldGroups, $reportId, $reportName);
            $this->form->requiredFilterWidgets = $reportableService->getRequiredFilterFieldsForReportGroup($this->reportGroup);
        }


        if ($request->isMethod('post')) {
            if ($this->reportPermissions->canCreate() || $this->reportPermissions->canUpdate()) {
                $this->form->bind($request->getParameter($this->form->getName()));

                $selectedDisplayFieldGroups = $request->getParameter('display_groups');
                $selectedDisplayFields = $request->getParameter('display_fields');

                $this->form->selectedDisplayFieldGroups = $selectedDisplayFieldGroups;
                $this->form->selectedDisplayFields = $selectedDisplayFields;

                if ($this->form->isValid()) {

                    $reportId = $this->form->getValue('report_id');
                    $reportName = $this->form->getValue('report_name');


                    $selectedFilterValues = $this->form->getSelectedFilterValues();

                    if ((empty($reportId) && !$this->reportPermissions->canCreate()) || 
                            (!empty($reportId) && !$this->reportPermissions->canUpdate())) {
                        $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
                    }                    
                    
                    // If report_id not available, create report
                    if (empty($reportId)) {
                        $report = $reportableService->saveReport($reportName, $this->reportGroup, true, $this->reportType);
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

                    $this->getUser()->setFlash('success', __(TopLevelMessages::SAVE_SUCCESS));
                    $this->redirect('core/viewDefinedPredefinedReports');
                    return;
                }
            }
        } else {
            
            if ((empty($this->reportId) && !$this->reportPermissions->canCreate()) || 
                    (!empty($this->reportId) && !$this->reportPermissions->canUpdate())) {
                $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
            }             
            
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

    function getReportGroupAndType($request) {
        $this->reportType = $request->getParameter('reportType');
        $this->reportGroup = $request->getParameter('reportGroup');

        if (empty($this->reportType)) {
            $this->reportType = $this->getUser()->getAttribute('PredefinedReportType');
        } else {
            $this->getUser()->setAttribute('PredefinedReportType', $this->reportType);
        }
        if (empty($this->reportGroup)) {
            $this->reportGroup = $this->getUser()->getAttribute('PredefinedReportGroup');
        } else {
            $this->getUser()->setAttribute('PredefinedReportGroup', $this->reportGroup);
        }
    }

}

?>
