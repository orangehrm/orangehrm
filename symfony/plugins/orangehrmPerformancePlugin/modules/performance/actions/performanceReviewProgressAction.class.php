<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2010 OrangeHRM Inc., http://www.orangehrm.com
 *
 * Please refer the file license/LICENSE.TXT for the license which includes terms and conditions on using this software.
 *
 * */
class performanceReviewProgressAction extends basePeformanceAction {

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
        if ($this->getUser()->getAttribute('auth.isSupervisor', false)) {
            $request->setParameter('initialActionName', 'searchEvaluatePerformancReview');
        } else {
            $request->setParameter('initialActionName', 'searchPerformancReview');
        }
        
        $this->backUrl = $request->getReferer();

        $id = $request->getParameter('id');
        $serachParams ['id'] = $id;

        $this->review = $this->getPerformanceReviewService()->searchReview($serachParams);
    }

}
