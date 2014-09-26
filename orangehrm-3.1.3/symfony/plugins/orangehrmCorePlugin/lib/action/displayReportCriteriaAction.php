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
abstract class displayReportCriteriaAction extends sfAction {

    protected $request;

    public function execute($request) {

        $reportId = $request->getParameter("reportId");

        $reportGeneratorService = new ReportGeneratorService();
        $runtimeFilterFieldWidgetNamesAndLabelsList = $reportGeneratorService->getRuntimeFilterFieldWidgetNamesAndLabels($reportId);
        $this->reportName = $reportGeneratorService->getReportName($reportId);

        $this->runtimeFilterFieldWidgetNamesAndLabelsList = $runtimeFilterFieldWidgetNamesAndLabelsList;
        $selectedRuntimeFilterFieldList = $reportGeneratorService->getSelectedRuntimeFilterFields($reportId);

        $ohrmFormGenerator = new ohrmFormGenerator();
        $this->reportForm = $ohrmFormGenerator->generateForm($runtimeFilterFieldWidgetNamesAndLabelsList, $this->getDataGroups());

        if ($request->isMethod('post')) {

            $this->reportForm->bind($request->getParameter($this->reportForm->getName()));

            if ($this->reportForm->isValid()) {

                $formValues = $this->reportForm->getValues();

                $reportableService = new ReportableService();
                $selectedFilterFieldList = $reportableService->getSelectedFilterFields($reportId, true);
                $runtimeWhereClause = $reportGeneratorService->generateWhereClauseConditionArray($selectedFilterFieldList,$formValues);

                $staticColumns = null;
                if($this->hasStaticColumns()){
                    $staticColumns = $this->setStaticColumns($formValues);
                }
                $sql = $reportGeneratorService->generateSql($reportId, $runtimeWhereClause, $staticColumns);
                $this->setReportCriteriaInfoInRequest($formValues);
                $this->getRequest()->setParameter('sql', $sql);
                $this->getRequest()->setParameter('reportId', $reportId);
                $this->setForward();
            }
        }
    }

    abstract public function setReportCriteriaInfoInRequest($formValues);

    abstract public function setForward();

    abstract public function setStaticColumns($formValues);

    public function hasStaticColumns(){
        return false;
    }
    
    public function getDataGroupPermissions($dataGroups) {
        return $this->getContext()->getUserRoleManager()->getDataGroupPermissions($dataGroups, array(), array(), false, array());
    }
    
    public function getDataGroups() {
        return array();
    }    
}
