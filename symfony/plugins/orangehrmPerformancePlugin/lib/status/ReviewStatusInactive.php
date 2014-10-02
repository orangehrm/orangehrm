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
class ReviewStatusInactive extends BaseReviewStatus {
    
    /**
     *
     * @return int 
     */
    public function getStatusId() {
        return 1;
    }
    
    /**
     *
     * @return \ReviewStatusInactive 
     */
    public static function getInstance(){
        return new ReviewStatusInactive();
    }
    
    /**
     *
     * @return string 
     */
    public function getName() {
        return "Inactive";
    }
    
     /**
     *
     * @return boolean 
     */
    public function isSaveEnabled(){
        return true;
    }
    
    /**
     *
     * @return boolean 
     */
    public function isActivateEnabled(){
         return true;
    }
}