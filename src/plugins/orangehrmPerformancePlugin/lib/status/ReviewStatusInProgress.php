<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ReviewStatusInactive
 *
 * @author nadeera
 */
class ReviewStatusInProgress extends BaseReviewStatus {
    
    /**
     *
     * @return int 
     */
    public function getStatusId() {
        return 3;
    }
    
    /**
     *
     * @return ReviewStatusInProgress 
     */
    public static function getInstance(){
        return new ReviewStatusInProgress();
    }
    
    /**
     *
     * @return string 
     */
    public function getName() {
        return "In Progress";
    }
    /**
     *
     * @return boolean 
     */    
    public function isSaveEnabled() {
        return true;
    }
    
    /**
     *
     * @return boolean 
     */
     public function isEvaluationsEditable(){
        return true;
    }
    
    /**
     *
     * @return boolean 
     */
    public function isEvaluationsCompleateEnabled(){
         return true;
    } 
}