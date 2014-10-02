<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2010 OrangeHRM Inc., http://www.orangehrm.com
 *
 * Please refer the file license/LICENSE.TXT for the license which includes terms and conditions on using this software.
 *
**/

class myPerformanceReviewAction extends basePeformanceAction {
    
   public $performanceReviewService;
    
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
    
    public function execute($request) {
        $request->setParameter('initialActionName', 'myPerformanceReview');
        
        $statusArray [] = $this->getReviewStatusFactory()->getStatus('activated')->getStatusId();
        $statusArray [] = $this->getReviewStatusFactory()->getStatus('inProgress')->getStatusId();
        $statusArray [] = $this->getReviewStatusFactory()->getStatus('approved')->getStatusId();
        
       
        $serachParams ['employeeNumber'] =  $this->getUser()->getEmployeeNumber();
        $serachParams ['status'] = $statusArray;
        $serachParams ['reviewerId'] = $this->getUser()->getEmployeeNumber();
        $reviews = $this->getPerformanceReviewService()->searchReview($serachParams);        

        $this->setListComponent($reviews);
        
    }

    /**
     *
     * @param Doctrine_Collection $reviews 
     */
    protected function setListComponent($reviews) {

        $configurationFactory = $this->getListConfigurationFactory();

        ohrmListComponent::setActivePlugin('orangehrmPerformancePlugin');
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setListData($reviews);
        ohrmListComponent::setPageNumber(0);
        $numRecords = count($reviews);
        ohrmListComponent::setItemsPerPage($numRecords);
        ohrmListComponent::setNumberOfRecords($numRecords);
    }

    /**
     *
     * @return \SearchReviewListConfigurationFactory 
     */
    protected function getListConfigurationFactory() {
        return new MyPerformanceReviewListConfigurationFactory();
    }
}