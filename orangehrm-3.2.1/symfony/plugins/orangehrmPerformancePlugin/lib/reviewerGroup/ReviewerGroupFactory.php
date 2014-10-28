<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ReviewerGroupFactory
 *
 * @author nadeera
 */
class ReviewerGroupFactory {


    public static function getInstance(){
        return new ReviewerGroupFactory();
    }
    /**
     *
     * @param type $reviewerIdentifier
     * @return \SubordinateReviewerGroup|\PeerReviewerGroup|\SupervisorReviewerGroup|\SelfReviewerGroup 
     */
    public function getReviewer($reviewerIdentifier){        
        switch ($reviewerIdentifier) {
            case 'supervisors':
            case SupervisorReviewerGroup::getInstance()->getId();
                return new SupervisorReviewerGroup();
                break;
            case 'selfReviewer':
            case SelfReviewerGroup::getInstance()->getId();
                return new SelfReviewerGroup();
                break;
            default:
                break;
        }
    }    
}