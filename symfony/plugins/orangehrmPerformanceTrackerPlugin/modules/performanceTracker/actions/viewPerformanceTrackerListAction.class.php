<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of viewPerformanceTrackerListAction
 *
 * @author indiran
 */
class viewPerformanceTrackerListAction extends basePerformanceAction {

    
    public $performanceTrackerService;
    public $performanceTrack;
    protected $initialActionName;
    protected $title = "";
    protected $performanceTrackList;
    /**
     * 
     * @param type $request
     */
    public function execute($request) {
        $request->setParameter('initialActionName', $this->getInitalAction());
  
        $this->_setListComponent($this->getPerformanceTrackList());

        $params = array();
        $this->parmetersForListCompoment = $params;
        
    }
    
    /**
     * 
     * @param type $performanceTrackList
     */
    private function _setListComponent($performanceTrackList) {
        $configurationFactory = new PerformanceTrackListConfigurationFactory();
        $configurationFactory->setRuntimeDefinitions(array('title'=>  $this->title));
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setActivePlugin('orangehrmPerformanceTrackerPlugin');
        ohrmListComponent::setListData($performanceTrackList);
    }
    
    /**
     * 
     * @param type $initalAction
     */
    protected function setInitalAction($initalAction){
        $this->initialActionName = $initalAction;
    }

    /**
     * 
     * @return type
     */
    protected function getInitalAction(){
        if($this->initialActionName == null || $this->initialActionName == ""){
            $this->initialActionName = "viewPerformanceTrackerList";
        }
        return $this->initialActionName;
    }
    
    /**
     * 
     * @param type $listTitle
     */
    protected function setTitle($listTitle){
        $this->title = $listTitle;
    }
    
    /**
     * 
     * @return type
     */
    protected function getTitle(){
        return $this->title;
    }
    
    public function getPerformanceTrackList() {
        return $this->performanceTrackList;
    }

    
}

?>
