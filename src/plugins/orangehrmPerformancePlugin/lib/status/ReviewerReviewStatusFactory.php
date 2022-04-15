<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ReviewerReviewStatusFactory
 *
 * @author nadeera
 */
class ReviewerReviewStatusFactory {

    /**
     *
     * @param type $id
     * @return \ReviewStatusInactive|\ReviewStatusActivated|\ReviewStatusInProgress|\ReviewStatusApproved 
     */
    public function getStatus($id = null) {

        switch ($id) {
            case 1:
            case 'activated':
                return new ReviewerReviewStatusActivated();
                break;
            case 2:
            case 'inProgress':
                return new ReviewerReviewStatusInProgress();
                break;
            case 3:
            case 'completed':
                return new ReviewerReviewStatusCompleted();
                break;
            default:                
               return new ReviewerReviewStatusActivated();
               break;
        }
    }
    
    /**
     *
     * @return ReviewStatusFactory 
     */
    public static function getInstance(){
        return new ReviewerReviewStatusFactory();
    }

}

