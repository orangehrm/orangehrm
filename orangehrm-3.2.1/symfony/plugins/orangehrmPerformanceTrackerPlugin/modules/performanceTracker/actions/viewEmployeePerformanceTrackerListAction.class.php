<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of viewEmployeePerformanceTrackerListAction
 *
 * @author chameera
 */
class viewEmployeePerformanceTrackerListAction extends viewPerformanceTrackerListAction {

    public function execute($request) {
        $page = $request->getParameter('hdnAction') == 'search' ? 1 : $request->getParameter('pageNo', 1);
        $this->setPageNumber($page);
        $listTitle = __('Performance Trackers');
        $this->setTitle($listTitle);
        $this->setPerformanceTrackList();
        $this->setTrackerListCount();
        $initalAction = 'viewEmployeePerformanceTrackerList';
        $this->setInitalAction($initalAction);
        parent::execute($request);
        $this->setTemplate('viewPerformanceTrackerList');
    }

    public function setPerformanceTrackList() {
        $auth = Auth::instance();
        $loggedInEmpNumber = $auth->getEmployeeNumber();
        $isAdmin = $auth->hasRole(Auth::ADMIN_ROLE);

        $searchParameter = array('page' => $this->getPageNumber(), 'limit' => sfConfig::get('app_items_per_page'), 'reviewerId' => $loggedInEmpNumber);

        if ($isAdmin) {
            $this->performanceTrackList = $this->getPerformanceTrackerService()->getPerformanceTrackList($searchParameter);
        } else {
            $this->performanceTrackList = $this->getPerformanceTrackerService()->getPerformanceTrackListByReviewer($searchParameter);
        }
    }

    public function setTrackerListCount() {
        $auth = Auth::instance();
        $loggedInEmpNumber = $auth->getEmployeeNumber();
        $isAdmin = $auth->hasRole(Auth::ADMIN_ROLE);
        $searchParameter = array('reviewerId' => $loggedInEmpNumber);
        if ($isAdmin) {
            $this->trackListCount = $this->getPerformanceTrackerService()->getPerformanceTrackListCount();
        } else {
            $this->trackListCount = $this->getPerformanceTrackerService()->getPerformanceTrackListCountByReviewer($searchParameter);
        }
    }

}

?>
