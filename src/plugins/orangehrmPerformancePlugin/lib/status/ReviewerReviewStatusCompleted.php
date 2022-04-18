<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ReviewerReviewStatusCompleted
 *
 * @author nadeera
 */
class ReviewerReviewStatusCompleted extends BaseReviewStatus {
    
    /**
     *
     * @return int 
     */
    public function getStatusId() {
        return 3;
    }
    
    /**
     *
     * @return \ReviewStatusActivated 
     */
    public static function getInstance(){
        return new ReviewerReviewStatusCompleted();
    }
    
    /**
     *
     * @return string 
     */
    public function getName() {
        return __("Completed");
    }
    
    /**
     *
     * @return boolean 
     */
    public function isEvaluationFormEditable(){
        return false;
    }
}