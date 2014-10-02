<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of performAction
 * This is used to initiate a feedback for an employee.
 * @author indiran
 */
class addPerformanceTrackerAction extends basePerformanceAction {

    public $performanceTrack;

    public function preExecute() {
        
        $usrObj = $this->getUser()->getAttribute('user');
        if (!$usrObj->isAdmin()) {
            $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
        }
    }

    /**
     * @param sfForm $form
     * @return
     */
    public function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }

    public function execute($request) {

	$request->setParameter('initialActionName', 'addPerformanceTracker');
        //set performance track list
        $performanceTrackList = $this->getPerformanceTrackerService()->getPerformanceTrackList();
        $this->_setListComponent($performanceTrackList);
        $params = array();
        $this->parmetersForListCompoment = $params;

        $trackId = $request->getParameter('id');
            $this->setForm(new AddPerformanceTrackerForm( array(), array('trackId' => $trackId), null));        
        
        if ($request->isMethod('post')) {
            $this->form->bind($request->getParameter('addPerformanceTracker'));
            if ($this->form->isValid()) {
                
                $this->form->save();
                $this->getUser()->setFlash('success', __(TopLevelMessages::SAVE_SUCCESS));
                $this->redirect('performanceTracker/addPerformanceTracker');
            }
        } 

    }

    private function _setListComponent($performanceTrackList) {
        $configurationFactory = new PerformanceTrackListAdminConfigurationFactory();
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setActivePlugin('orangehrmPerformanceTrackerPlugin');
        ohrmListComponent::setListData($performanceTrackList);
    }


}

?>
