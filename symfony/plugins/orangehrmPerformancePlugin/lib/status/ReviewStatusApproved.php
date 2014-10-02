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
class ReviewStatusApproved extends BaseReviewStatus {
    
    /**
     *
     * @return int 
     */
    public function getStatusId() {
        return 4;
    }
    
    /**
     *
     * @return ReviewStatusApproved 
     */
    public static function getInstance(){
        return new ReviewStatusApproved();
    }
    
    /**
     *
     * @return string 
     */
    public function getName() {
        return "Approved";
    }

    /**
     *
     * @return boolean
     */
    public function isFinalRatingVisible(){
        return true;
    }
}

?>
