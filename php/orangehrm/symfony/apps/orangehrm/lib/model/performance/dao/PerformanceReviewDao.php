<?php
/* 
 * 
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 * 
 */

/**
 * PerformanceReview Dao class 
 *
 * @author Samantha Jayasinghe
 */
class PerformanceReviewDao extends BaseDao {
	
    /**
     * Save Performance Review
     * @param PerformanceReview $performanceReview
     * @return PerformanceReview
     */
    public function savePerformanceReview(PerformanceReview $performanceReview) {
        try {
            if ( $performanceReview->getId() == '') {
                $idGenService = new IDGeneratorService( );
                $idGenService->setEntity($performanceReview);
                $performanceReview->setId($idGenService->getNextID());
            }

            $performanceReview->save();
            return $performanceReview;

        } catch (Exception $e) {
            throw new DaoException ( $e->getMessage () );
        }
    }
    
    
 	/**
     * Read Performance Review
     * @param $reviewId
     * @return PerformanceReview
     */
    public function readPerformanceReview($reviewId) {

        try {
            $performanceReview = Doctrine::getTable('PerformanceReview')
            ->find($reviewId);
            return $performanceReview;
        } catch(Exception $e) {
            throw new DaoException ( $e->getMessage () );
        }
    }
    
    /**
     * Get Performance Review List
     * @return unknown_type
     */
    public function getPerformanceReviewList( )
    {
        try
        {
            $q = Doctrine_Query::create()
                ->from('PerformanceReview pr')
                ->orderBy('pr.id');

            $performanceReviewList = $q->execute();

            return  $performanceReviewList ;

        }catch( Exception $e)
        {
            throw new DaoException ( $e->getMessage() );
        }
    }

    /**
     * Delete PerformanceReview
     * @param array reviewList
     * @returns boolean
     * @throws PerformanceServiceException
     */
    public function deletePerformanceReview($reviewList) {

        try {

            $q = Doctrine_Query::create()
               ->delete('PerformanceReview')
               ->whereIn('id', $reviewList);
               $numDeleted = $q->execute();
            if($numDeleted > 0) {
               return true ;
            }
            return false;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Builds the search query that fetches all the
     * records for given search clues
     */
    private function _getSearchReviewQuery($clues) {

        try {

            $from = $clues['from'];
            $to = $clues['to'];
            $jobCode = $clues['jobCode'];
            $divisionId = $clues['divisionId'];
            $empId = $clues['empId'];
            $reviewerId = $clues['reviewerId'];

            if (isset($clues['loggedReviewerId']) && $clues['loggedReviewerId'] != $clues['empId']) {
                $reviewerId = $clues['loggedReviewerId'];
            }

            if (isset($clues['loggedEmpId'])) {
                $empId = $clues['loggedEmpId'];
            }

            $q = Doctrine_Query::create()
                 ->from('PerformanceReview');

            if (!empty($from)) {
                $q->andWhere("periodFrom >= ?", $from);
            }

            if (!empty($to)) {
                $q->andWhere("periodTo <= ?", $to);
            }

            if (!empty($empId)) {
                $q->andWhere("employeeId = ?", $empId);
            }

            if (!empty($reviewerId)) {

                /* $q->andWhere("reviewerId = ?", $reviewerId) throws
                 * "Invalid parameter number" error.
                 */

                if (empty($empId) && isset($clues['loggedReviewerId'])) {
                    $q->andWhere("(reviewerId = $reviewerId OR employeeId = $reviewerId)");
                } else {
                    $q->andWhere("reviewerId = ?", $reviewerId);
                }
            }

            if (!empty($jobCode)) {
                $q->andWhere("jobTitleCode = ?", $jobCode);
            }

            if (!empty($divisionId)) {
                $q->andWhere("subDivisionId = ?", $divisionId);
            }

            return $q;

        } catch(Exception $e) {
            throw new DaoException($e->getMessage());
        }

    }

    /**
     * Returns Object based on the combination of search
     * @param array $clues
     * @param array $offset
     * @param array $limit
     * @throws DaoException
     */
     
    public function searchPerformanceReview($clues, $offset=null, $limit=null) {

        try {

            $q = $this->_getSearchReviewQuery($clues);

            if (isset($offset) && isset($limit)) {
                $q->offset($offset)->limit($limit);
            }
            
            return $q->execute();

        } catch(Exception $e) {
            throw new PerformanceServiceException($e->getMessage());
        }

    }

    /**
     * Returns the count of records
     * that matched given $clues
     */

    public function countReviews($clues) {

        try {

            $q = $this->_getSearchReviewQuery($clues);

            return $q->count();

        } catch(Exception $e) {
            throw new PerformanceServiceException($e->getMessage());
        }

    }
    
     /**
     * Update status of performance review
     * @param array $clues
     * @param array $offset
     * @param array $limit
     * @throws DaoException
     */
    public function updatePerformanceReviewStatus( PerformanceReview $performanceReview , $status){
    	try {
             $q = Doctrine_Query::create()
				    ->update('PerformanceReview')
				    ->set("state='?'", $status)
				    ->where("id = ?",$performanceReview->getId());
                $q->execute();
                
                return true ;
			
        } catch(Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
}