<?php

class viewDefinedPredefinedReportsAction extends basePimReportAction {

    const SEARCH_PARAM_ATTR_NAME = 'defineReportListParameters';

    public function execute($request) {

        $this->reportPermissions = $this->getDataGroupPermissions('pim_reports');

        $this->getReportGroupAndType($request);

        $pageNumber = 1;
        $sortField = 'name';
        $sortOrder = 'ASC';

        $reportableService = new ReportableService();
        $searchString = null;

        if ($this->reportPermissions->canRead()) {
            $this->searchForm = new ViewPredefinedReportsSearchForm();
        }
        $reportList = $reportableService->getAllPredefinedReports($this->reportType);
        $totalRecords = count($reportList);
        $this->reportJsonList = $this->searchForm->getReportListAsJson($reportList);


        if ($request->isMethod('post')) {

            if ($request->hasParameter("chkSelectRow")) {
                if ($this->reportPermissions->canDelete()) {
		    $form = new DefaultListForm() ;
                    $form->bind($request->getParameter($form->getName()));
                    if ($form->isValid()) {
                        $reportIds = $request->getParameter("chkSelectRow");
                        $results = $reportableService->deleteReports($reportIds);
                        if ($results > 0) {
                            $this->getUser()->setFlash('success', __(TopLevelMessages::DELETE_SUCCESS));
                            $this->redirect('core/viewDefinedPredefinedReports');
                            return;
                        }
                    }
                }
            } else {
                $this->searchForm->bind($request->getParameter($this->searchForm->getName()));
                if ($this->searchForm->isValid()) {
                    $searchString = $this->searchForm->getValue("search");
                }
            }
        } else {
            // Get saved search params if not a new request.
            if ($request->hasParameter('pageNo') ||
                    $request->hasParameter('sortField') ||
                    $request->hasParameter('sortOrder')) {

                $this->_getSearchParams($searchString, $pageNumber, $sortField, $sortOrder);
                $pageNumber = $request->getParameter('pageNo', $pageNumber);
                $sortField = $request->getParameter('sortField', $sortField);
                $sortOrder = $request->getParameter('sortOrder', $sortOrder);

                $this->searchForm->setDefault('search', $searchString);
            }
        }

        $noOfRecords = sfConfig::get('app_items_per_page');
        $offset = ($pageNumber >= 1) ? (($pageNumber - 1) * $noOfRecords) : ($request->getParameter('pageNo', 1) - 1) * $noOfRecords;

        if ($searchString != null) {
            $reports = $reportableService->getPredefinedReportsByPartName($this->reportType, $searchString, $noOfRecords, $offset, $sortField, $sortOrder);
            $totalRecords = $reportableService->getPredefinedReportCountByPartName($this->reportType, $searchString);
        } else {
            $reports = $reportableService->getPredefinedReports($this->reportType, $noOfRecords, $offset, $sortField, $sortOrder);
        }

        $this->_saveSearchParams($searchString, $pageNumber, $sortField, $sortOrder);

        if ($this->reportPermissions->canRead()) {
            $this->_setListComponent($reports, $noOfRecords, $pageNumber, $totalRecords, $this->reportPermissions);
        }

        $this->parmetersForListComponent = array();

        list($this->messageType, $this->message) = $this->getUser()->getFlash('templateMessage');
    }

    private function _setListComponent($reports, $noOfRecords, $pageNumber, $totalRecords, $permissions) {
        $configurationFactory = new ViewDefinedPredefinedReportsConfigurationFactory();
        
        $runtimeDefinitions = array();
        $buttons = array();

        if ($permissions->canCreate()) {
            $buttons['Add'] = array('label' => 'Add',
                'function' => 'addPredefinedReport');
        }

        if (!$permissions->canDelete()) {
            $runtimeDefinitions['hasSelectableRows'] = false;
        } else if ($permissions->canDelete()) {
            $buttons['Delete'] = array('label' => 'Delete',
                'type' => 'submit',
                'data-toggle' => 'modal',
                'data-target' => '#deleteConfModal',
                'class' => 'delete');
        }
        $runtimeDefinitions['buttons'] = $buttons;
        
        if($permissions->canUpdate()){
            $configurationFactory->setEdit(true);
        }else{
            $configurationFactory->setEdit(false);
        }
        
        $configurationFactory->setRuntimeDefinitions($runtimeDefinitions);
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setPageNumber($pageNumber);
        ohrmListComponent::setItemsPerPage($noOfRecords);
        ohrmListComponent::setNumberOfRecords($totalRecords);
        ohrmListComponent::setListData($reports);
    }

    private function _saveSearchParams($searchString, $pageNumber, $sortField, $sortOrder) {
        $searchParams = array('searchString' => $searchString,
            'pageNumber' => $pageNumber,
            'sortField' => $sortField,
            'sortOrder' => $sortOrder);
        $this->getUser()->setAttribute(self::SEARCH_PARAM_ATTR_NAME, $searchParams);
    }

    private function _getSearchParams(&$searchString, &$pageNumber, &$sortField, &$sortOrder) {
        if ($this->getUser()->hasAttribute(self::SEARCH_PARAM_ATTR_NAME)) {
            $searchParams = $this->getUser()->getAttribute(self::SEARCH_PARAM_ATTR_NAME);

            if (isset($searchParams['searchString'])) {
                $searchString = $searchParams['searchString'];
            }

            if (isset($searchParams['pageNumber'])) {
                $pageNumber = $searchParams['pageNumber'];
            }
            if (isset($searchParams['sortField'])) {
                $sortField = $searchParams['sortField'];
            }
            if (isset($searchParams['sortOrder'])) {
                $sortOrder = $searchParams['sortOrder'];
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

