<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BaseReviewStatus
 *
 * @author nadeera
 */
abstract class BaseReviewStatus  extends BasePerformanceStatus {     
   
    /**
     *
     * @return boolean 
     */
    public function isSaveEnabled(){
        return false;
    }

    /**
     *
     * @return boolean 
     */
    public function isActivateEnabled(){
         return false;
    } 
    
     /**
     *
     * @return boolean 
     */
    public function isEvaluationsCompleateEnabled(){
         return false;
    } 

    /**
     *
     * @return integer 
     */
    public function getNextStatus(){
        return $this->getStatusId();
    }
    
    /**
     *
     * @return boolean 
     */
    public function isEvaluationsEditable(){
        return false;
    }

     /**
     *
     * @return boolean
     */
    public function isFinalRatingVisible(){
        return false;
    }
}