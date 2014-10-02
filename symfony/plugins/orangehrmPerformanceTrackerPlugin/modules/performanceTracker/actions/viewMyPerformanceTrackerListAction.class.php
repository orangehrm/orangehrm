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
        $listTitle = __('My Performance Trackers List');
        $this->setTitle($listTitle);
        $this->setPerformanceTrackList();
        $initalAction = 'viewMyPerformanceTrackerList';
        $this->setInitalAction($initalAction);
        parent::execute($request);
        $this->setTemplate('viewPerformanceTrackerList');
    }

    public function setPerformanceTrackList() {
        $auth = Auth::instance(); 
        $loggedInEmpNumber = $auth->getEmployeeNumber();
        $this->performanceTrackList = $this->getPerformanceTrackerService()->getPerformanceTrackerByEmployee($loggedInEmpNumber);
    }



}

?>
