<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of searchPerformancReviewAction
 *
 * @author nadeera
 */
class searchPerformancReviewAction extends basePeformanceAction {

    public $searchReviewForm;
    
    
    public function preExecute() {
       $this->_checkAuthentication();
    }

    /**
     *
     * @return \PerformanceReviewSearchForm 
     */
    public function getSearchReviewForm() {
        if ($this->searchReviewForm == null) {
            return new PerformanceReviewSearchForm();
        } else {
            return $this->searchReviewForm;
        }
    }

    /**
     *
     * @param \PerformanceReviewSearchForm $searchReviewForm 
     */
    public function setSearchReviewForm($searchReviewForm) {
        $this->searchReviewForm = $searchReviewForm;
    }

    public function execute($request) {

        $form = $this->getSearchReviewForm();

        if ($request->isMethod('post')) {
            $form->bind($request->getParameter($form->getName()));
            if ($form->isValid()) {
                try {
                    
                } catch (LeaveAllocationServiceException $e) {
                    $this->templateMessage = array('WARNING', __($e->getMessage()));
                }
            }
        }

        $message = $this->getUser()->getFlash('templateMessage');
        $this->messageType = (isset($message[0])) ? strtolower($message[0]) : "";
        $this->message = (isset($message[1])) ? $message[1] : "";


        if ($this->getUser()->hasFlash('templateMessage')) {
            $this->templateMessage = $this->getUser()->getFlash('templateMessage');
            $this->getUser()->setFlash('templateMessage', array());
        }

        $form->setUser($this->getUser());
        $reviews = $form->searchReviews();
        $this->setListComponent($reviews);
        $this->form = $form;
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
        return new SearchReviewListConfigurationFactory();
    }

}