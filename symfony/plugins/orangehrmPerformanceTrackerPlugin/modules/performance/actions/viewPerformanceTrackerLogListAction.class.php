<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of viewFeedbackListAction
 *
 * @author indiran
 */
class viewPerformanceTrackerLogListAction extends basePerformanceAction{
    //TO DO move to a baseAction
    
    public $performanceTrack; 
    public $performanceTrackerLogList;
    

    public function execute($request) {
        
        $loggedInEmpNumber = $this->getUser()->getAttribute('auth.empNumber');        
        $usrObj = $this->getUser()->getAttribute('user');
        $isAdmin = $usrObj->isAdmin();
        
        $trackId = $request->getParameter('trackId');   
        $mode = $request->getParameter('mode');
        
        //Reviwer/admin clicks on the link viewPerformanceTrackerLogList?trackId= in viewPerformanceTrackerList
        if (!empty($trackId)) {
            //query for $trackId and $reviwerId
            //TO DO check whether have permission to view
            $performanceTrackerLogList = $this->getPerformanceTrackerService()->getPerformanceTrackerLogListByTrack($trackId);
        }
        elseif ($mode == PluginPerformanceTrackerLog::MODE_MY){                     
             //if mode is my
             // then get user
             // then get employee and empNumber
             //pass that value to the service function and get the  
             $performanceTrackerLogList = $this->getPerformanceTrackerService()->getPerformanceTrackerLogByEmployeeNumber($loggedInEmpNumber);
             
        }     
        //if user is admin get the list for $trackId
        //get all the log lists
        elseif($isAdmin){
            $performanceTrackerLogList = $this->getPerformanceTrackerService()->getPerformanceTrackerLogList();
        }else{
			$this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
		}
        $this->_setListComponent($performanceTrackerLogList);
        $params = array();
        $this->parmetersForListCompoment = $params; 
    }

    private function _setListComponent($performanceTrackerLogList) {
        $configurationFactory = new PerformanceTrackerLogListConfigurationFactory();
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setActivePlugin('orangehrmPerformanceTrackerPlugin');
        ohrmListComponent::setListData($performanceTrackerLogList);
    }
    
    protected function getListConfigurationFactory() {
        return new PerformanceTrackerLogListConfigurationFactory();
    }
}

?>
