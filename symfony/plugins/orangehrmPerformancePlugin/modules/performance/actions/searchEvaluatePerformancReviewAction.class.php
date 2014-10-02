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
        
        $isSupervisor = $this->getUser()->getAttribute('auth.isSupervisor', false);

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
        $reviews = $form->searchReviews();
        $this->setListComponent($reviews, $isSupervisor);
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
    protected function setListComponent($reviews, $isSupervisor) {

        $configurationFactory = $this->getListConfigurationFactory($isSupervisor);

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
     * @return \EvaluateSearchReviewListConfigurationFactory 
     */
    protected function getListConfigurationFactory($isSupervisor) {
            return new EvaluateSearchReviewListConfigurationFactory($isSupervisor);
    }

}
