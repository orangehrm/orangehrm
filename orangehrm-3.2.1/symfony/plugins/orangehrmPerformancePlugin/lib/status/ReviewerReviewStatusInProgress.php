<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ReviewerReviewStatusInProgress
 *
 * @author nadeera
 */
class ReviewerReviewStatusInProgress extends BaseReviewStatus {
    
    /**
     *
     * @return int 
     */
    public function getStatusId() {
        return 2;
    }
    
    /**
     *
     * @return \ReviewStatusActivated 
     */
    public static function getInstance(){
        return new ReviewerReviewStatusInProgress();
    }
    
    /**
     *
     * @return string 
     */
    public function getName() {
        return __("In Progress");
    }
    
    /**
     *
     * @return boolean 
     */
    public function isEvaluationFormEditable(){
        return true;
    }
}