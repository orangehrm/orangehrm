<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of viewMyPerformanceTrackerListAction
 *
 * @author chameera
 */
class viewMyPerformanceTrackerListAction extends viewPerformanceTrackerListAction{
        
    public function execute($request) {
         $page = $request->getParameter('hdnAction') == 'search' ? 1 : $request->getParameter('pageNo', 1);
        $this->setPageNumber($page);
        $listTitle = __('My Performance Trackers List');
        $this->setTitle($listTitle);
        $this->setPerformanceTrackList();
        $this->setTrackerListCount();
        $initalAction = 'viewMyPerformanceTrackerList';
        $this->setInitalAction($initalAction);
        parent::execute($request);
        $this->setTemplate('viewPerformanceTrackerList');
    }

    public function setPerformanceTrackList() {
        $auth = Auth::instance(); 
        $loggedInEmpNumber = $auth->getEmployeeNumber();
        $searchParameter = array('page' => $this->getPageNumber(), 'limit' => sfConfig::get('app_items_per_page'), 'employeeId' => $loggedInEmpNumber);
        $this->performanceTrackList = $this->getPerformanceTrackerService()->getPerformanceTrackerByEmployee($searchParameter);
    }

    public function setTrackerListCount() {
        $auth = Auth::instance(); 
        $loggedInEmpNumber = $auth->getEmployeeNumber();
        $searchParameter = array('limit' =>null, 'employeeId' => $loggedInEmpNumber);
        $this->trackListCount = $this->getPerformanceTrackerService()->getPerformanceTrackListCountByEmployee($searchParameter);
    }

}

?>
