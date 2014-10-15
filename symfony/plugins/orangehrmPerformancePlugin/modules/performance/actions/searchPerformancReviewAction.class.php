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
    private $pageNumber;

    public function getPageNumber() {
        return $this->pageNumber;
    }

    public function setPageNumber($pageNumber) {
        $this->pageNumber = $pageNumber;
    }

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
        
        $page = $request->getParameter('hdnAction') == 'search' ? 1 : $request->getParameter('pageNo', 1);
        
        $this->setPageNumber($page);

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
        $sortFeild = $request->getParameter('sortField');
        $sortOrder = $request->getParameter('sortOrder');
        $reviews = $form->searchReviews(sfConfig::get('app_items_per_page'), $page, $sortOrder, $sortFeild);
        $countReview = $form->getCountReviewList();
        $this->setListComponent($reviews,$countReview);
        $this->form = $form;
    }

    /**
     *
     * @param Doctrine_Collection $reviews 
     */
    protected function setListComponent($reviews,$countReview) {
        $pageNumber = $this->getPageNumber();

        $configurationFactory = $this->getListConfigurationFactory();

        ohrmListComponent::setActivePlugin('orangehrmPerformancePlugin');
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setListData($reviews);
        ohrmListComponent::setPageNumber($pageNumber);
        ohrmListComponent::setItemsPerPage(sfConfig::get('app_items_per_page'));
        ohrmListComponent::setNumberOfRecords($countReview);
    }

    /**
     *
     * @return \SearchReviewListConfigurationFactory 
     */
    protected function getListConfigurationFactory() {
        return new SearchReviewListConfigurationFactory();
    }
    
    protected function _checkAuthentication($request = null) {
        $user = $this->getUser()->getAttribute('user');
        if (!($user->isAdmin())) {
            $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
        }
    }


}
