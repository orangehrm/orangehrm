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
        $listTitle = __('Performance Trackers List');
        $this->setTitle($listTitle);
        $this->setPerformanceTrackList();
        $initalAction = 'viewEmployeePerformanceTrackerList';
        $this->setInitalAction($initalAction);
        parent::execute($request);
        $this->setTemplate('viewPerformanceTrackerList');
    }

    public function setPerformanceTrackList() {
        $auth = Auth::instance();
        $loggedInEmpNumber = $auth->getEmployeeNumber();
        $isAdmin = $auth->hasRole(Auth::ADMIN_ROLE);

        if ($isAdmin) { 
            $this->performanceTrackList = $this->getPerformanceTrackerService()->getPerformanceTrackList();
        } else {
            $this->performanceTrackList = $this->getPerformanceTrackerService()->getPerformanceTrackListByReviewer($loggedInEmpNumber);
        }
    }
    
}

?>
