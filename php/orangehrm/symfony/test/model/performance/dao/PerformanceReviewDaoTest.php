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
 * Kpi Dao Test class 
 *
 * @author Samantha Jayasinghe
 */
require_once 'PHPUnit/Framework.php';

class PerformanceReviewDaoTest extends PHPUnit_Framework_TestCase{
	
	private $testCases;
	private $performanceReviewDao ;
	
	/**
     * PHPUnit setup function
     */
    public function setup() {

       $configuration 		= ProjectConfiguration::getApplicationConfiguration('orangehrm', 'test', true);       
       $this->testCases 	= sfYaml::load(sfConfig::get('sf_test_dir') . '/fixtures/performance/performanceReview.yml');
       $this->performanceReviewDao	=	new PerformanceReviewDao();
	   
    }
    
	/**
     * Test Save Performance Review
     *
     */
    public function testSavePerformanceReview()
    {
    	
    	foreach ($this->testCases['PerformanceReview'] as $key=>$testCase) {
			$performanceReview	=	new PerformanceReview();
    		$performanceReview->setEmployeeId( $testCase['employeeId']);
    		$performanceReview->setReviewerId( $testCase['reviewerId']);
    		$performanceReview->setPeriodFrom( date('y-m-d',strtotime($testCase['periodFrom'])));
    		$performanceReview->setPeriodTo( date('y-m-d',strtotime($testCase['periodTo'])));
    		$performanceReview->setKpis( $testCase['kpis']);
    		
    		$performanceReview	=	$this->performanceReviewDao->savePerformanceReview( $performanceReview );
    		$result	=	($performanceReview instanceof PerformanceReview)?true:false;
			$this->assertTrue($result);
			
			$this->testCases['PerformanceReview'][$key]["id"] =  $performanceReview->getId();
    	}

    	file_put_contents(sfConfig::get('sf_test_dir') . '/fixtures/performance/performanceReview.yml',sfYaml::dump($this->testCases));
    }
    
	/**
	 * Test Read Performance Review
	 * @return unknown_type
	 */
	public function testReadKpi(){
		foreach ($this->testCases['PerformanceReview'] as $key=>$testCase) {
			$result	=	$this->performanceReviewDao->readPerformanceReview( $testCase['id']);
			$this->assertTrue($result instanceof PerformanceReview);
		}
		
	}
	
	/**
	 * Test Get default Kpi rating
	 */
	public function testGetPerformanceReviewList(  ){
		$performanceReviewList	=	$this->performanceReviewDao->getPerformanceReviewList( );
		foreach( $performanceReviewList as $result){
			$this->assertTrue($result instanceof PerformanceReview);
		}
		
	}
}