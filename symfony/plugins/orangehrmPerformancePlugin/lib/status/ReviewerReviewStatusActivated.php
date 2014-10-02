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
class ReviewerReviewStatusActivated extends BaseReviewStatus {
    
    /**
     *
     * @return int 
     */
    public function getStatusId() {
        return 1;
    }
    
    /**
     *
     * @return \ReviewStatusActivated 
     */
    public static function getInstance(){
        return new ReviewerReviewStatusActivated();
    }
    
    /**
     *
     * @return string 
     */
    public function getName() {
        return __("Activated");
    }
    
    /**
     *
     * @return boolean 
     */
    public function isEvaluationFormEditable(){
        return true;
    }
}