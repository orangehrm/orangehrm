<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PerformanceTrackService
 *
 * @author indiran
 */
class PerformanceTrackerService extends BaseService {

    /**
     * @ignore
     */
    private $employeeDao;
    private $performanceTrackerDao;

    /**
     * Construct
     * @ignore
     */
    public function __construct() {
        //$this->employeeDao = new EmployeeDao();
        $this->setPerformanceTrackDao(new PerformanceTrackerDao());
    }

    /**
     * Get Employee Dao
     * @return EmployeeDao
     * @ignore
     */
    public function getEmployeeDao() {
        return $this->employeeDao;
    }

    /**
     * Set Employee Dao
     * @param EmployeeDao $employeeDao
     * @return void
     * @ignore
     */
    public function setEmployeeDao(EmployeeDao $employeeDao) {
        $this->employeeDao = $employeeDao;
    }

    /**
     * Get PerformanceTrack Dao
     * @return PerformanceTrackerDao
     * @ignore
     */
    public function getPerformanceTrackDao() {
        return $this->performanceTrackerDao;
    }

    /**
     * Set PerformanceTrack Dao
     * @param PerformanceTrackerDao $performanceTrackDao
     * @return void
     * @ignore
     */
    public function setPerformanceTrackDao(PerformanceTrackerDao $PerformanceTrackDao) {
        $this->performanceTrackerDao = $PerformanceTrackDao;
    }

    public function savePerformanceTrack(PerformanceTrack $performanceTrack) {
        return $this->getPerformanceTrackDao()->savePerformanceTrack($performanceTrack);
    }

    public function savePerformanceTrackerLog(PerformanceTrackerLog $performanceTrackerLog) {
        //save performanceTrack
        return $this->getPerformanceTrackDao()->savePerformanceTrackerLog($performanceTrackerLog);
    }

    public function DeletePerformanceTracker($performanceTrackId) {
        //set performance track state to deleted 
        $performanceTrack = $this->getPerformanceTrack($performanceTrackId);
        if ($performanceTrack instanceof PerformanceTrack) {

            $performanceTrack->setStatus(PerformanceTrack::STATUS_DELETED);
            //set performance track logs state to deleted
            $loglist = $performanceTrack->getPerformanceTrackerLog();

            foreach ($loglist as $log) {
                if ($log instanceof PerformanceTrackerLog) {
                    $log->setStatus(PerformanceTrackerLog::STATUS_DELETED);
                    $loglist->add($log);
                }
            }
            $performanceTrack->setPerformanceTrackerLog($loglist);

            //set performance track reviewers state to deleted 
            $reviewers = $performanceTrack->getPerformanceTrackerReviewer();
            foreach ($reviewers as $reviewer) {
                if ($reviewer instanceof PerformanceTrackerReviewer) {
                    $reviewer->setStatus(PerformanceTrackerReviewer::STATUS_DELETED);
                    $reviewers->add($reviewer);
                }
            }
            $performanceTrack->setPerformanceTrackerReviewer($reviewers); 
            $this->savePerformanceTrack($performanceTrack);
        }
        return; // $this->savePerformanceTrack($performanceTrack);
    }

    /**
     * Retrieve PerformanceTrack by performanceTrackId
     * @param int $performanceTrackId
     * @returns boolean
     * @throws DaoException
     */
    public function getPerformanceTrack($performanceTrackId) {
        return $this->getPerformanceTrackDao()->getPerformanceTrack($performanceTrackId);
    }

    /**
     * Retrieve PerformanceTrackLog by performanceTrackLogId
     * @param int $performanceTrackLogId
     * @returns boolean
     * @throws DaoException
     */
    public function getPerformanceTrackerLog($performanceTrackLogId) {
        return $this->getPerformanceTrackDao()->getPerformanceTrackerLog($performanceTrackLogId);
    }

    public function getPerformanceReviewersIdListByTrackId($reviewId) {
        return $this->getPerformanceTrackDao()->getPerformanceReviewersIdListByTrackId($reviewId);
    }

    public function getPerformanceTrackListByReviewer($searchParameter) {
        return $this->getPerformanceTrackDao()->getPerformanceTrackListByReviewer($searchParameter);
    }

    public function getPerformanceTrackerLogListByReviewer($reviewerId) {
        return $this->getPerformanceTrackDao()->getPerformanceTrackerLogListByReviewer($reviewerId);
    }

    public function getPerformanceTrackerLogListByTrack($trackId) {
        return $this->getPerformanceTrackDao()->getPerformanceTrackerLogListByTrack($trackId);
    }

    public function getPerformanceTrackList($searchParameter) {
        return $this->getPerformanceTrackDao()->getPerformanceTrackList($searchParameter);
    }

    public function getPerformanceTrackerLogList() {
        return $this->getPerformanceTrackDao()->getPerformanceTrackerLogList();
    }

    public function deleteReviweres($trackId, $reviwerArray) {
        return $this->getPerformanceTrackDao()->deleteReviweres($trackId, $reviwerArray);
    }

    public function getTrackReviewersIdListByReview($reviewId) {
        return $this->getPerformanceTrackDao()->getTrackReviewersIdListByReview($reviewId);
    }

    public function getPerformanceTrackerByEmployee($parameters) {
        return $this->getPerformanceTrackDao()->getPerformanceTrackerByEmployee($parameters);
    }

    public function getPerformanceTrackerLogByEmployeeNumber($empNumber) {
        return $this->getPerformanceTrackDao()->getPerformanceTrackerLogByEmployeeNumber($empNumber);
    }

    public function isTrackerExistForEmployee($empNumber) {
        return $this->getPerformanceTrackDao()->getPerformanceTrackerLogByEmployeeNumber($empNumber);
    }
    
    
    /**
     * This method is used to check whether a particular performance track is accessible by logged in employee.
     * @param type $performanceTrackId
     * @param type $loggedInEmpNumber
     * @return boolean
     */
    public function isTrackerAccessibleForEmployee($performanceTrackId,$loggedInEmpNumber){
        $isAccessible = false;        
        $reviwers = $this->getPerformanceReviewersIdListByTrackId($performanceTrackId);        
        if(in_array($loggedInEmpNumber, $reviwers)){
            $isAccessible = true;
        }
        return $isAccessible;
    }
    
	/**
     *
     * @param array $searchResult
     * @return string 
     */
    public function getCsvContentDetail($searchResult) {
        $headers = array("Reviewer", "Log", "Comment", "Performance", "Added Date", "Modified Date");
        foreach($headers as &$header){
        	$header = __($header);
        }
        
        $csvResultSet = array ();        
        foreach ($searchResult as $log) {
            $csvRow = array();
            $csvRow [] = $log->getReviewerName();
            $csvRow [] = $log->getLog();
            $csvRow [] = $log->getComment();
            $csvRow [] = $log->getAchievementText();
            $csvRow [] = set_datepicker_date_format($log->getAddedDate());
            $csvRow [] = set_datepicker_date_format($log->getModifiedDate());
            $csvResultSet[] = $csvRow;
        }
        
        $csvBuilder = new CSVBuilder();
        return $csvBuilder->createCSVString($headers, $csvResultSet);
    }
    
    public function getPerformanceTrackListCount() {
        $searchParameter = array('limit' => null);
        $trackerList = $this->getPerformanceTrackDao()->getPerformanceTrackList($searchParameter);
        return count($trackerList);
    }
    
    public function getPerformanceTrackListCountByReviewer($searchParameter){
        $searchParameter['limit'] =  null;
        $trackerList = $this->getPerformanceTrackDao()->getPerformanceTrackListByReviewer($searchParameter);
        return count($trackerList);
    }
    
    public function getPerformanceTrackListCountByEmployee($searchParameter){
        $searchParameter['limit'] =  null;
        $trackerList = $this->getPerformanceTrackDao()->getPerformanceTrackerByEmployee($searchParameter);
        return count($trackerList);
    }

}
?>





