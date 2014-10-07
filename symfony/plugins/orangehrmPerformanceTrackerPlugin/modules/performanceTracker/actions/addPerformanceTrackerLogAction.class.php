<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of addReviewAction
 *
 * @author indiran
 */
class addPerformanceTrackerLogAction extends basePerformanceAction {

    public $performanceTrack;
    public $performanceTrackerLogList;
    public $employeeName;
    private $loggedInEmpNumber;
    private $title = "";
    private $logId;
    private $isAdmin;
    private $trackOwner;
    private $isTrackOwner = false;

    /**
     * 
     */
    public function preExecute() {
        $this->loggedInEmpNumber = $this->getUser()->getAttribute('auth.empNumber');
        $usrObj = $this->getUser()->getAttribute('user');
        $this->isAdmin = $usrObj->isAdmin();
        $this->logId = $this->getRequest()->getParameter('logId');
        $this->trackId = $this->getRequest()->getParameter('trackId');
        $valid = false;
        if (!empty($this->logId)) {
            $performanceTrackLog = $this->getPerformanceTrackerService()->getPerformanceTrackerLog($this->logId);
            if (($performanceTrackLog instanceof PerformanceTrackerLog) && ($performanceTrackLog->canUpdate()) && ($this->trackId == $performanceTrackLog->getPerformanceTrackId())) {
                $valid = true;
            }
        } else if (!empty($this->trackId)) {
            $valid = $this->canLogsAccessed();
        }
        if ($valid == false) {
            $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
        }
    }

    public function execute($request) {
        
        $saved = false;
        if ($this->isAdmin && !$this->isTrackOwner) {
            $request->setParameter('initialActionName', 'viewEmployeePerformanceTrackerList');
        } else if ($this->isTrackOwner) {
            $request->setParameter('initialActionName', 'viewMyPerformanceTrackerList');
        } else {
            $request->setParameter('initialActionName', 'viewEmployeePerformanceTrackerList');
        }

        $this->setForm(new AddPerformanceTrackerLogForm());

        if ($request->isMethod('post')) {
            $this->form->bind($request->getParameter('addperformanceTrackerLog'));
            $performanceTrackerLogList = $this->getPerformanceTrackerService()->getPerformanceTrackerLogListByTrack($this->trackId);

            $this->_setListComponent($performanceTrackerLogList);
            $params = array();
            $this->parmetersForListCompoment = $params;

            if ($this->form->isValid()) {
                //add to db                        
                $performanceTrackerLogNew = $this->form->getPerformanceTrackerLog();
                $id = $performanceTrackerLogNew->getId();
                $userId = $this->getUser()->getAttribute('auth.userId');
                if (!empty($id)) {

                    $performanceTrackerLog = $this->getPerformanceTrackerService()->getPerformanceTrackerLog($id);

                    $reviewerUserId = $performanceTrackerLog->getUserId();
                    if ($reviewerUserId == $userId || $this->isAdmin) {
                        $performanceTrackerLog->setUserId($userId);
                        if (!$this->isAdmin) {
                            $performanceTrackerLog->setReviewerId($this->loggedInEmpNumber);
                        }
                        $performanceTrackerLog->setLog($performanceTrackerLogNew->getLog());
                        $performanceTrackerLog->setAchievement($performanceTrackerLogNew->getAchievement());
                        $performanceTrackerLog->setComment($performanceTrackerLogNew->getComment());
                        $performanceTrackerLog->setModifiedDate(date(PerformanceTrack::DATE_FORMAT));
                        $this->getPerformanceTrackerService()->savePerformanceTrackerLog($performanceTrackerLog);
                        $saved = true;
                    }
                } else {
                    $performanceTrackerLogNew->setReviewerId($this->loggedInEmpNumber);
                    $performanceTrackerLogNew->setUserId($userId);

                    $performanceTrackerLogNew->setAddedDate(date(PerformanceTrack::DATE_FORMAT));
                    $performanceTrackerLogNew->setStatus(PerformanceTrackerLog::STATUS_ACTIVE);
                    $this->getPerformanceTrackerService()->savePerformanceTrackerLog($performanceTrackerLogNew);
                    $saved = true;
                }
                //redirect to list view.
                $trackId = $performanceTrackerLogNew->getPerformanceTrackId();
                $performanceTrackerLogList = $this->getPerformanceTrackerService()->getPerformanceTrackerLogListByTrack($trackId);
                $this->_setListComponent($performanceTrackerLogList);
                $params = array();
                $this->parmetersForListCompoment = $params;
                if ($saved) {
                    $this->getUser()->setFlash('success', __(TopLevelMessages::SAVE_SUCCESS));
                }

                $this->redirect('performanceTracker/addPerformanceTrackerLog?trackId=' . $trackId);
            }
        } else {

            $this->form->setDefaultValues($this->trackId, $this->logId);
            $performanceTrack = $this->getPerformanceTrackerService()->getPerformanceTrack($this->trackId);
            if ($performanceTrack instanceof PerformanceTrack) {
                if ($performanceTrack->getEmpNumber() == $this->loggedInEmpNumber) {
                    $this->title = __('My Tracker Logs') . ' - ' . $performanceTrack->getTrackerName();
                } else {
                    $this->employeeName = $performanceTrack->getEmployee()->getFirstAndLastNames();
                    $this->title = $performanceTrack->getTrackerName().__(' - Tracker Log ') . "( ".$this->employeeName." )";
                }
            }

            $this->performanceTrackerLogList = $this->getPerformanceTrackerService()->getPerformanceTrackerLogListByTrack($this->trackId);

            $params = array();
            $this->parmetersForListCompoment = $params;
            $this->_setListComponent($this->performanceTrackerLogList);
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

    /**
     * 
     * @param type $performanceTrackerLogList
     */
    private function _setListComponent($performanceTrackerLogList) {
        PerformanceTrackerLogListConfigurationFactory::setLoggedInEmpNumber($this->loggedInEmpNumber);
        $configurationFactory = new PerformanceTrackerLogListConfigurationFactory($this->title);
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setActivePlugin('orangehrmPerformanceTrackerPlugin');
        ohrmListComponent::setListData($performanceTrackerLogList);
    }

    /**
     * This method checks whether tracker logs accessible by logged in user or not.
     * @return boolean
     */
    private function canLogsAccessed() {
        $canAccess = false;
        $reviwers = $this->getPerformanceTrackerService()->getPerformanceReviewersIdListByTrackId($this->trackId);
        $this->performanceTrack = $this->getPerformanceTrackerService()->getPerformanceTrack($this->trackId);
        $this->trackOwner = (!empty($this->performanceTrack)) ? $this->performanceTrack->getEmpNumber() : "";
        
        if ($this->trackOwner == $this->loggedInEmpNumber) {
            $this->isTrackOwner = true;
            
        }

        if (!empty($reviwers) && count($reviwers) > 0) { 
            if (!$this->trackOwner || $this->isAdmin || in_array($this->loggedInEmpNumber, $reviwers)) {
                $canAccess = true; 
            } else if($this->isTrackOwner){
                $canAccess = true;
            }
        }
        return $canAccess;
    }

}

?>
