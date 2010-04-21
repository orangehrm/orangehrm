<?php
/**
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
 * Service Class for Performance Review
 *
 * @author orange
 */
class PerformanceReviewService extends BaseService {

    const EMAIL_TEMPLATE_REVIWER_SUBMIT     =   'performance_submit.txt';
    const EMAIL_TEMPLATE_HRADMIN_APPROVE    =   'performance_approve.txt';
    const EMAIL_TEMPLATE_HRADMIN_REJECT     =   'performance_reject.txt';
    const EMAIL_TEMPLATE_ADD_REVIEW         =   'add-review.txt';

    public function savePerformanceReviews($reviews) {

        try {

            $idGeneratorService = new IDGeneratorService();

            foreach ($reviews as $review) {

                $idGeneratorService->setEntity($review);
                $review->setId($idGeneratorService->getNextID());
                $review->save();

            }

        } catch (Exception $e) {
            throw new PerformanceServiceException($e->getMessage());
        }

    }

    /**
     * Builds the search query that fetches all the
     * records for given search clues
     */

    private function _getSearchReviewQuery($clues) {

        try {

            $where	=	array();
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

            //$where = "periodFrom >= '$from' AND periodTo <= '$to'";
			
        	if (!empty($from)) {
                //$where .= " AND employeeId = $empId";
                array_push($where,"periodFrom >= '$from'");
            }
            
        	if (!empty($to)) {
                //$where .= " AND employeeId = $empId";
                array_push($where,"periodTo <= '$to'");
            }
            
            if (!empty($empId)) {
                //$where .= " AND employeeId = $empId";
                array_push($where,"employeeId = $empId");
            }

            if (!empty($reviewerId)) {
                //$where .= " AND reviewerId = $reviewerId";
                if (empty($empId) && isset($clues['loggedReviewerId'])) {
                	$wherePart = "(reviewerId = $reviewerId OR employeeId = $reviewerId)";
                } else {
                    $wherePart = "reviewerId = $reviewerId";
                }
                array_push($where, $wherePart);
            }

            if (!empty($jobCode)) {
               // $where .= " AND jobTitleCode = '$jobCode'";
                array_push($where,"jobTitleCode = '$jobCode'");
            }

            if (!empty($divisionId)) {
               // $where .= " AND subDivisionId = $divisionId";
                array_push($where,"subDivisionId = $divisionId");
            }

            $q = Doctrine_Query::create()
                 ->from('PerformanceReview');
            if (count($where) > 0) {
            	$q->where(implode(' AND ',$where));
            }
            
            return $q;

        } catch(Exception $e) {
            throw new PerformanceServiceException($e->getMessage());
        }

    }

    /**
     * Returns a portion of the matched records
     * based on $offset and $limit
     */

    public function fetchReviews($clues, $offset, $limit) {

        try {

            $q = $this->_getSearchReviewQuery($clues);
            $q->offset($offset)->limit($limit);
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
            throw new PerformanceServiceException($e->getMessage());
        }
    }

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
            throw new PerformanceServiceException($e->getMessage());
        }
    }

    /**
     * Save Performance Review
     * @param PerformanceReview $performanceReview
     * @return PerformanceReview
     */
    public function changePerformanceStatus(PerformanceReview $performanceReview, $status)
    {
        try {

                switch($status)
                {
                    case PerformanceReview::PERFORMANCE_REVIEW_STATUS_SUBMITTED:
                        $this->sendReviwerSubmitEmail($performanceReview);
                        break;

                    case PerformanceReview::PERFORMANCE_REVIEW_STATUS_REJECTED:
                        $this->sendReviwRejectEmail($performanceReview);
                        break;

                    case PerformanceReview::PERFORMANCE_REVIEW_STATUS_APPROVED:
                        $this->sendReviwApproveEmail($performanceReview);
                        break;
                }

                $performanceReview->setState($status);      
				
                $q = Doctrine_Query::create()
				    ->update('PerformanceReview')
				    ->set("state='?'", $status)
				    ->where("id = ?",$performanceReview->getId());
                $q->execute();
                //$this->savePerformanceReview($performanceReview);
                return true ;
        } catch (Exception $e) {
            throw new PerformanceServiceException($e->getMessage());
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
            throw new AdminServiceException($e->getMessage());
        }
    }

    /**
     * Add New comments to performance review
     * @return unknown_type
     */
    public function addComment( PerformanceReview $performanceReview ,$comment ,$user){
        
        try {
        
            $performanceReviewComment = new PerformanceReviewComment();

            $performanceReviewComment->setPrId($performanceReview->getId());
            $performanceReviewComment->setComment($comment);
            if(is_numeric($user))
            	$performanceReviewComment->setEmployeeId($user);
            $performanceReviewComment->setCreateDate(date('Y-m-d'));
            $performanceReviewComment->save();

        } catch ( Exception $e) {
            throw new AdminServiceException($e->getMessage());
        }

    }

    /**
     * Send Reviwer Submit email
     * PerformanceReview $performanceReview
     * @return boolean
     */
    public function sendReviwerSubmitEmail( PerformanceReview $performanceReview ){
        try{
            $userEmail  =   $performanceReview->getCreator()->getEmail1();
            $workEmail  =   ($performanceReview->getCreator()->getEmployee() instanceof Employee )?$performanceReview->getCreator()->getEmployee()->getEmpWorkEmail():'';
            if ($userEmail !='' || $workEmail !='') {
                $content    =   file_get_contents(sfConfig::get('sf_root_dir')."/apps/orangehrm/templates/mail/".self::EMAIL_TEMPLATE_REVIWER_SUBMIT);
                $varibles   =   array('#reviwer'=>$performanceReview->getReviewer()->getFirstName().' '.$performanceReview->getReviewer()->getLastName(),
                                      '#employee'=>$performanceReview->getEmployee()->getFirstName().' '.$performanceReview->getEmployee()->getLastName());
                $mailBody   =   strtr($content, $varibles);
                $email      =   ( $userEmail != '')?$userEmail:$workEmail;
                $mailService    = new EmailService();
                $mailService->setTo(array($email));
                $mailService->setSubject("Performance Review Submitted");
                $mailService->setMailBody($mailBody);
                @$mailService->sendMail();
            }
            return true ;
        } catch( Exception $e) {
            throw new AdminServiceException($e->getMessage());
        }
    }

    /**
     * Send Reviwer reject email
     * PerformanceReview $performanceReview
     * @return boolean
     */
    public function sendReviwRejectEmail( PerformanceReview $performanceReview ){
        try{

            $email      =   $performanceReview->getReviewer()->getEmpWorkEmail();
            if ( $email != '') {

                $content    =   file_get_contents(sfConfig::get('sf_root_dir')."/apps/orangehrm/templates/mail/".self::EMAIL_TEMPLATE_HRADMIN_REJECT);
                $varibles   =   array('#comments'=>$performanceReview->getLatestComment(),
                                      '#employee'=>$performanceReview->getEmployee()->getFirstName().' '.$performanceReview->getEmployee()->getLastName());
                $mailBody   =   strtr($content, $varibles);

                $mailService    = new EmailService();
                $mailService->setTo(array($email));
                $mailService->setSubject("Performance Review Rejected");
                $mailService->setMailBody($mailBody);
                @$mailService->sendMail();
            }
            return true ;
        } catch (Exception $e) {
            throw new AdminServiceException($e->getMessage());
        }
    }

    /**
     * Send Reviwer approve email
     * PerformanceReview $performanceReview
     * @return boolean
     */
    public function sendReviwApproveEmail( PerformanceReview $performanceReview ){
      try{

        $email      =   $performanceReview->getReviewer()->getEmpWorkEmail();
        if ( $email != '') {
            $content    =   file_get_contents(sfConfig::get('sf_root_dir')."/apps/orangehrm/templates/mail/".self::EMAIL_TEMPLATE_HRADMIN_APPROVE);
            $varibles   =   array('#comments'=>$performanceReview->getLatestComment(),
                                  '#employee'=>$performanceReview->getEmployee()->getFirstName().' '.$performanceReview->getEmployee()->getLastName());
            $mailBody   =   strtr($content, $varibles);

            $mailService    = new EmailService();
            $mailService->setTo(array($email));
            $mailService->setSubject("Performance Review Approved");
            $mailService->setMailBody($mailBody);
            @$mailService->sendMail();
        }
            return true ;
        } catch ( Exception $e) {
            throw new AdminServiceException($e->getMessage());
        }
    }

    /**
     * Sends an email to reviewer when a Performance Review is added.
     *
     * @param PerformanceReviewService $review
     * @return null
     */

    public function informReviewer(PerformanceReview $review) {

        try {

            $reviewerEmail = $review->getReviewer()->getEmpWorkEmail();

            if ($reviewerEmail != '') {

                $content = file_get_contents(sfConfig::get('sf_root_dir')."/apps/orangehrm/modules/performance/templates/email/".self::EMAIL_TEMPLATE_ADD_REVIEW);
                $varibles = array('#reviewerName'=> $review->getReviewer()->getFirstName(),
                                  '#empName' => $review->getEmployee()->getFullName(),
                                  '#period' => $review->getPeriodFrom().' '.$review->getPeriodTo(),
                                  '#dueDate' => $review->getDueDate());
                $mailBody = strtr($content, $varibles);

                $mailService = new EmailService();
                $mailService->setTo(array($reviewerEmail));
                $mailService->setSubject("You Have Been Assigned a New Performance Review");
                $mailService->setMailBody($mailBody);
                @$mailService->sendMail();

            }

        } catch (Exception $e) {
            throw new PerformanceServiceException($e->getMessage());
        }

    }

    public function deleteReview($reviewList) {

        try {

            $q = Doctrine_Query::create()
            ->delete('PerformanceReview')
            ->whereIn('id', $reviewList);
            $numDeleted = $q->execute();

            return true ;

        } catch (Exception $e) {
            throw new PerformanceServiceException($e->getMessage());
        }
    }

    /**
     * Checks whether the given employee is a reviewer
     */

    public function isReviewer($empId) {

        try {

            $q = Doctrine_Query::create()
            ->from('PerformanceReview')
            ->where("reviewerId = $empId");

            $searchList = $q->execute();

            if (count($searchList) > 0) {
                return true;
            } else {
                return false;
            }

        } catch (Exception $e) {
            throw new PerformanceServiceException($e->getMessage());
        }

    }

    /**
     * Get reviewee list of given reviewer as json
     * If $addSelf is true then reviewer's details
     * will also be added.
     */
    public function getRevieweeListAsJson($reviewerId, $addSelf = false) {

        try {

            $q = Doctrine_Query::create()
            ->from('PerformanceReview')
            ->where("reviewerId = $reviewerId");

            $resultList = $q->execute();

			$empList = array();
			$empIds = array();

            /* Making sure employee list is unique: Begins */
            $i = 0;
            foreach ($resultList as $result) {
            	
            	$empId =  $result->getEmployee()->getEmpNumber();
            	
            	if (!in_array($empId, $empIds)) {
 	            	$empList[$i][0] = $result->getEmployee()->getFirstName().' '.$result->getEmployee()->getLastName();;
	                $empList[$i][1] = $empId;
	                $empIds[] = $empId;
 	               	$i++;
            	}
            	
            }
            /* Making sure employee list is unique: Ends */

            $jsonList = array();

            foreach ($empList as $emp) {
                $jsonList[] = "{name:'".$emp[0]."',id:'".$emp[1]."'}";
            }

            if ($addSelf) {
                $jsonList[] = "{name:'".$resultList[0]->getReviewer()->getFirstName().' '.$resultList[0]->getReviewer()->getLastName()."',id:'".$resultList[0]->getReviewer()->getEmpNumber()."'}";
            }

            $jsonString = "[".implode(",", $jsonList)."]";

            return $jsonString;

        } catch (Exception $e) {
            throw new PerformanceServiceException($e->getMessage());
        }

    }

}
