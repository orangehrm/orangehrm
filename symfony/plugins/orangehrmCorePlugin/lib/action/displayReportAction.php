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
abstract class displayReportAction extends basePimReportAction {

    private $confFactory;
    private $form;
    protected $reportName = 'pim-report';
    protected $reportTitle = 'PIM Report';
    
    /**
     * Get Logger instance
     * @return Logger
     */
    protected function getLoggerInstance() {
        if (is_null($this->logger)) {
            $this->logger = Logger::getLogger('core.report.displayReportAction');
        }
        return $this->logger;
    }
    
    /**
     *
     * @return string
     */
    public function getReportName() {
        return $this->reportName;
    }

    /**
     *
     * @param string $reportName 
     */
    public function setReportName($reportName) {
        $this->reportName = $reportName;
    }

    /**
     *
     * @return string
     */
    public function getReportTitle() {
        return $this->reportTitle;
    }

    /**
     *
     * @param string $reportTitle 
     */
    public function setReportTitle($reportTitle) {
        $this->reportTitle = $reportTitle;
    }

    
    public function execute($request) {
        
        $this->setInitialActionDetails($request);

        $reportId = $request->getParameter("reportId");
        $backRequest = $request->getParameter("backRequest");

        $reportableGeneratorService = new ReportGeneratorService();

        $sql = $request->getParameter("sql");

        $reportableService = new ReportableService();
        $this->report = $reportableService->getReport($reportId);

        if (empty($this->report)) {
            return $this->renderText(__('Invalid Report Specified'));
        }

        $useFilterField = $this->report->getUseFilterField();
        if (!$useFilterField) {

            $this->setCriteriaForm();
            if ($request->isMethod('post')) {

                $this->form->bind($request->getParameter($this->form->getName()));

                if ($this->form->isValid()) {
                    $reportGeneratorService = new ReportGeneratorService();
                    $formValues = $this->form->getValues();
                    $this->setReportCriteriaInfoInRequest($formValues);
                    $sql = $reportGeneratorService->generateSqlForNotUseFilterFieldReports($reportId, $formValues);
                }else{
                    $this->redirect($request->getReferer());
                }
            }
        } else {

            if ($request->isMethod("get")) {
                $reportGeneratorService = new ReportGeneratorService();
//                $selectedRuntimeFilterFieldList = $reportGeneratorService->getSelectedRuntimeFilterFields($reportId);

                $selectedFilterFieldList = $reportableService->getSelectedFilterFields($reportId, false);
                
                $values = $this->setValues();

//                $linkedFilterFieldIdsAndFormValues = $reportGeneratorService->linkFilterFieldIdsToFormValues($selectedRuntimeFilterFieldList, $values);
//                $runtimeWhereClauseConditionArray = $reportGeneratorService->generateWhereClauseConditionArray($linkedFilterFieldIdsAndFormValues);

                $runtimeWhereClauseConditionArray = $reportGeneratorService->generateWhereClauseConditionArray($selectedFilterFieldList, $values);
                $sql = $reportGeneratorService->generateSql($reportId, $runtimeWhereClauseConditionArray);
            }
        }

        $paramArray = array();

        if ($reportId == 1) {
            if (!isset($backRequest)) {
                $this->getUser()->setAttribute("reportCriteriaSql", $sql);
                $this->getUser()->setAttribute("parametersForListComponent", $this->setParametersForListComponent());
            }
            if (isset($backRequest) && $this->getUser()->hasAttribute("reportCriteriaSql")) {
                $sql = $this->getUser()->getAttribute("reportCriteriaSql");
                $paramArray = $this->getUser()->getAttribute("parametersForListComponent");
            }
        }


        $params = (!empty($paramArray)) ? $paramArray : $this->setParametersForListComponent();
        
        try {
            $rawDataSet = $reportableGeneratorService->generateReportDataSet($reportId, $sql);
        } catch (Exception $e) {
            $this->getLoggerInstance()->error($e->getMessage(), $e);
            $this->getUser()->setFlash(displayMessageAction::MESSAGE_HEADING, __('Report could not be generated'), false);
            $this->getUser()->setFlash('error.nofade', __('Please run the report again.'), false);
            $this->forward('core', 'displayMessage');
        }
        
        $dataSet = self::escapeData($rawDataSet);
        
        $headerGroups = $reportableGeneratorService->getHeaderGroups($reportId);

        $this->setConfigurationFactory();
        $configurationFactory = $this->getConfFactory();
        $configurationFactory->setHeaderGroups($headerGroups);

        if ($reportId == 3) {
            if (empty($dataSet[0]['employeeName']) && $dataSet[0]['totalduration'] == 0) {
                $dataSet = null;
            }
        }

        ohrmListComponent::setConfigurationFactory($configurationFactory);

        $this->setListHeaderPartial();

        ohrmListComponent::setListData($dataSet);

        $this->parmetersForListComponent = $params;
        
        $this->initilizeDataRetriever($configurationFactory, $reportableGeneratorService, 'generateReportDataSet', array($reportId, $sql));
    }

    abstract public function setParametersForListComponent();

    abstract public function setConfigurationFactory();

    abstract public function setListHeaderPartial();

    abstract public function setValues();
    
    abstract public function setInitialActionDetails($request);

    public function getConfFactory() {

        return $this->confFactory;
    }

    public function setConfFactory(ListConfigurationFactory $configurationFactory) {

        $this->confFactory = $configurationFactory;
    }

    public function setReportCriteriaInfoInRequest($formValues) {
        
    }

    public function setCriteriaForm() {
        
    }

    public function setForm($form) {
        $this->form = $form;
    }
       
    public function initilizeDataRetriever(ohrmListConfigurationFactory $configurationFactory, BaseService $dataRetrievalService, $dataRetrievalMethod, array $dataRetrievalParams) {
        $dataRetriever = new ExportDataRetriever();
        $dataRetriever->setConfigurationFactory($configurationFactory);
        $dataRetriever->setDataRetrievalService($dataRetrievalService);
        $dataRetriever->setDataRetrievalMethod($dataRetrievalMethod);
        $dataRetriever->setDataRetrievalParams($dataRetrievalParams);

        $this->getUser()->setAttribute('persistant.exportDataRetriever', $dataRetriever);
        $this->getUser()->setAttribute('persistant.exportFileName', $this->getReportName());
        $this->getUser()->setAttribute('persistant.exportDocumentTitle', $this->getReportTitle());
        $this->getUser()->setAttribute('persistant.exportDocumentDescription', 'Generated at ' . date('Y-m-d H:i'));
    }
    
    public function escapeData($data) {
        if (is_array($data)) {
            $escapedArray = array();
            foreach ($data as $key => $rawData) {
                $escapedArray[$key] = self::escapeData($rawData);
            }
            return $escapedArray;
        } else {
            return htmlspecialchars($data);
        } 
    }

}