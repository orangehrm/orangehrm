<?php
/**
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
 */
class PerformanceReview extends BasePerformanceReview {

	const PERFORMANCE_REVIEW_STATUS_SCHDULED			=	1 ;
	const PERFORMANCE_REVIEW_STATUS_BEING_REVIWED		=	3 ;
	const PERFORMANCE_REVIEW_STATUS_SUBMITTED			=	5 ;
	const PERFORMANCE_REVIEW_STATUS_REJECTED			=	7 ;
	const PERFORMANCE_REVIEW_STATUS_APPROVED			=	9 ;
	
	const PERFORMANCE_REVIEW_STATUS_TEXT_SCHDULED		=	'Scheduled' ;
	const PERFORMANCE_REVIEW_STATUS_TEXT_BEING_REVIWED	=	'Being Reviewed' ;
	const PERFORMANCE_REVIEW_STATUS_TEXT_SUBMITTED		=	'Submitted' ;
	const PERFORMANCE_REVIEW_STATUS_TEXT_REJECTED		=	'Rejected' ;
	const PERFORMANCE_REVIEW_STATUS_TEXT_APPROVED		=	'Approved' ;
	
	
	private $latestComment ;
	
	/**
	 * Returns the full name of employee, (first middle last)
	 * 
	 * @return String Full Name 
	 */
	public function getFullName() {
		
	    $fullName = trim($this->firstName) . " " . trim($this->middleName);
	    $fullName = trim( trim($fullName) . " " . trim($this->lastName) ); 
		
		return $fullName;
	}
	
	/**
	 *  Get Latest comment
	 */
	public function getLatestComment( )
	{
		return $this->latestComment ;
	}
	
	/**
	 * Set Latest Comment
	 * @return unknown_type
	 */
	public function setLatestComment( $latestComment)
	{
		$this->latestComment	=	$latestComment ;
	}
	
	
	/**
	 * Gets the names of all the supervisors of this employee as a comma separated string
	 * Only the first and last name are used.
	 * 
	 * @return String String containing comma separated list of supervisor names. 
	 *                Empty string if employee has no supervisors
	 */
	public function getSupervisorNames() {
	    $supervisorNames = array();
	    
	    foreach ($this->supervisors as $supervisor ){
	        $supervisorNames[] = trim($supervisor->firstName . ' ' . $supervisor->lastName); 
	    }
	    
	    return implode(',', $supervisorNames);
	}
	
	/**
	 * Returns the review period of employee, (from to)
	 * 
	 * @return String $reviewPeriod
	 */
	public function getReviewPeriod() {
		
	    $reviewPeriod = trim($this->datefrom) . " - " . trim($this->dateto); 
		
		return $reviewPeriod;
	}
	
    /**
     * Get Text status
     */
    public function getTextStatus( )
    {
    	$textStatus	=	'';
    	switch( $this->getState() )
    	{
    		case self::PERFORMANCE_REVIEW_STATUS_SUBMITTED:
    			$textStatus	=	self::PERFORMANCE_REVIEW_STATUS_TEXT_SUBMITTED;
    		break;
    		
    		case self::PERFORMANCE_REVIEW_STATUS_BEING_REVIWED:
    			$textStatus	=	self::PERFORMANCE_REVIEW_STATUS_TEXT_BEING_REVIWED;
    		break;
    		
    		case self::PERFORMANCE_REVIEW_STATUS_SCHDULED:
    			$textStatus	=	self::PERFORMANCE_REVIEW_STATUS_TEXT_SCHDULED;
    		break;
    		
    		case self::PERFORMANCE_REVIEW_STATUS_REJECTED:
    			$textStatus	=	self::PERFORMANCE_REVIEW_STATUS_TEXT_REJECTED;
    		break;
    		
    		case self::PERFORMANCE_REVIEW_STATUS_APPROVED:
    			$textStatus	=	self::PERFORMANCE_REVIEW_STATUS_TEXT_APPROVED;
    		break;
    		
    	}
    	return $textStatus;
    }
    
    /**
     * 
     */
}