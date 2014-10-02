<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of basePeformanceAction
 *
 * @author nadeera
 */
abstract class basePeformanceAction extends sfAction {

    public $kpiService;
    public $performanceReviewService;
    
    
    public $reviewStatusFactory;
    
    /**
     *
     * @return ReviewStatusFactory 
     */
    public function getReviewStatusFactory() {
        if($this->reviewStatusFactory == null){
            return new ReviewStatusFactory();
        } else {
            return $this->reviewStatusFactory;
        }       
    }

    /**
     *
     * @param ReviewStatusFactory $reviewStatusFactory 
     */
    public function setReviewStatusFactory($reviewStatusFactory) {
        $this->reviewStatusFactory = $reviewStatusFactory;
    }

    
    protected function _checkAuthentication($request = null) {
        $user = $this->getUser()->getAttribute('user');
        $isSupervisor = $this->getUser()->getAttribute('auth.isSupervisor',false);
        if (!($user->isAdmin() || $isSupervisor)) {
            $this->redirect('pim/viewPersonalDetails');
        }
    }

    /**
     *
     * @return \PerformanceReviewService 
     */
    public function getPerformanceReviewService() {
        if ($this->performanceReviewService == null) {
            return new PerformanceReviewService();
        } else {
            return $this->performanceReviewService;
        }
    }

    /**
     *
     * @param \PerformanceReviewService $performanceReviewService 
     */
    public function setPerformanceReviewService($performanceReviewService) {
        $this->performanceReviewService = $performanceReviewService;
    }

    /**
     *
     * @return \KpiService 
     */
    public function getKpiService() {
        if ($this->kpiService == null) {
            return new KpiService();
        } else {
            return $this->kpiService;
        }
    }

    /**
     *
     * @param \KpiService $kpiService 
     */
    public function setKpiService($kpiService) {
        $this->kpiService = $kpiService;
    }

}