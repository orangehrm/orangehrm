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
class ReviewStatusActivated extends BaseReviewStatus {
    
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
        return new ReviewStatusActivated();
    }
    
    /**
     *
     * @return string 
     */
    public function getName() {
        return "Activated";
    }
    
    /**
     *
     * @return integer 
     */
    public function getNextStatus(){
        return ReviewStatusInProgress::getInstance()->getStatusId();
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