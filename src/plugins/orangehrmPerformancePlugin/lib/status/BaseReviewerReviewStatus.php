<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BaseReviewerReviewStatus
 *
 * @author nadeera
 */
abstract class BaseReviewerReviewStatus extends BasePerformanceStatus { 
 
    /**
     *
     * @return boolean 
     */
    public function isEvaluationFormEditable(){
        return false;
    }
    
}