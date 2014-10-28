<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of searchEvaluatePerformancReviewAction
 *
 * @author nadeera
 */
class searchEvaluatePerformancReviewAction extends basePeformanceAction {

    public $searchReviewForm;
    private $pageNumber;

    public function getPageNumber() {
        return $this->pageNumber;
    }

    public function setPageNumber($pageNumber) {
        $this->pageNumber = $pageNumber;
    }

    /**
     *
     * @return \EvaluatePerformanceReviewSearchForm 
     */
    public function getSearchReviewForm() {
        if ($this->searchReviewForm == null) {
            return new EvaluatePerformanceReviewSearchForm();
        } else {
            return $this->searchReviewForm;
        }
    }

    /**
     *
     * @param \EvaluatePerformanceReviewSearchForm $searchReviewForm 
     */
    public function setSearchReviewForm($searchReviewForm) {
        $this->searchReviewForm = $searchReviewForm;
    }

    public function execute($request) {

        $form = $this->getSearchReviewForm();  

        $isReviewer = $this->isReviewer();
        $page = $request->getParameter('hdnAction') == 'search' ? 1 : $request->getParameter('pageNo', 1);

        if ($request->isMethod('post')) {
            $form->bind($request->getParameter($form->getName()));
            if ($form->isValid()) {
                try {
                    
                } catch (LeaveAllocationServiceException $e) {
                    $this->templateMessage = array('WARNING', __($e->getMessage()));
                }
            }
        }

        $form->setUser($this->getUser());
        $reviews = $form->searchReviews($page);
        $reviewsCount = $form->getCountReviewList();
        $this->setListComponent($reviews, $isReviewer, $reviewsCount);
        $this->form = $form;

        $message = $this->getUser()->getFlash('templateMessage');
        $this->messageType = (isset($message[0])) ? strtolower($message[0]) : "";
        $this->message = (isset($message[1])) ? $message[1] : "";


        if ($this->getUser()->hasFlash('templateMessage')) {
            $this->templateMessage = $this->getUser()->getFlash('templateMessage');
            $this->getUser()->setFlash('templateMessage', array());
        }
    }

    /**
     *
     * @param Doctrine_Collection $reviews 
     */
    protected function setListComponent($reviews, $isReviewer, $reviewsCount) {
        $pageNumber = $this->getPageNumber();
        $configurationFactory = $this->getListConfigurationFactory($isReviewer);

        ohrmListComponent::setActivePlugin('orangehrmPerformancePlugin');
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setListData($reviews);
        ohrmListComponent::setPageNumber($pageNumber);
        $numRecords = $reviewsCount;
        ohrmListComponent::setItemsPerPage(sfConfig::get('app_items_per_page'));
        ohrmListComponent::setNumberOfRecords($numRecords);
    }

    /**
     *
     * @return \EvaluateSearchReviewListConfigurationFactory 
     */
    protected function getListConfigurationFactory($isReviewer) {
        return new EvaluateSearchReviewListConfigurationFactory($isReviewer);
    }

    public function isReviewer() {
        $valid = false;
        $reviews = $this->getPerformanceReviewService()->getReviewsByReviewerId($this->getUser()->getAttribute('auth.empNumber'));
        foreach ($reviews as $review){
            $valid = $valid || $this->checkIsReviwer($review->getId());            
        }
        return $valid;
    }

}
