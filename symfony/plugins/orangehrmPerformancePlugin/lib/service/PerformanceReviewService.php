<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PerformanceReviewService
 *
 * @author nadeera
 */
class PerformanceReviewService {

    public $dao;

    /**
     *
     * @return PerformanceReviewDao
     */
    public function getDao() {
        if ($this->dao != null) {
            return $this->dao;
        } else {
            return new PerformanceReviewDao();
        }
    }

    /**
     *
     * @param KpiDao $dao 
     */
    public function setDao($dao) {
        $this->dao = $dao;
    }

    /**
     *
     * @param sfDoctrineRecord $review
     * @return PerformanceReview 
     */
    public function saveReview(sfDoctrineRecord $review){
        return $this->getDao()->saveReview($review);
    }
    
    /**
     *
     * @param array $parameters
     * @return Doctrine_Collection 
     */
    public function searchReview($parameters, $order = null){
        return $this->getDao()->searchReview($parameters, $order);
    }
    
    /**
     *
     * @return boolean 
     */
    public function deleteReview($ids){
        
        return $this->getDao()->deleteReview($ids);
    }
    
    /**
     *
     * @param integer $id
     * @return type 
     */
    public function deleteReviewersByReviewId($id){
        
        return $this->getDao()->deleteReviewersByReviewId($id);
    }
    
    /**
     *
     * @param type $id
     * @return type 
     */
    public function getReviewRating($id = null){
        $parameters ['id'] =  $id;
        return $this->getDao()->searchRating($parameters);
    }
    
    /**
     *
     * @param type $id
     * @return type 
     */
    public function searchReviewRating( $parameters ){        
        return $this->getDao()->searchRating($parameters);
    }
    
     public function getReviwerEmployeeList( $reviwerEmployeeId ){
         return $this->getDao()->getReviwerEmployeeList( $reviwerEmployeeId );
     }
     
     public function getCountReviewList($parameters){
         $reviewList = $this->getDao()->searchReview($parameters);
         return count($reviewList);
     }
     
     public function getReviewById($id){
         return $this->getDao()->getReviewById($id);
     }
     
     public function getReviewsByReviewerId($reviwerId){
         return $this->getDao()->getReviewsByReviewerId($reviwerId);
     }
}