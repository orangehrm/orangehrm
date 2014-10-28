<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PerforamanceReviewDaoTest
 *
 * @author nadeera
 */

/**
 * @group performance
 */
class PerforamanceReviewDaoTest extends PHPUnit_Framework_TestCase {

    protected function setUp() {
        TestDataService::truncateTables(array('PerformanceReview', 'Employee'));
        TestDataService::populate(sfConfig::get('sf_plugins_dir') . '/orangehrmPerformancePlugin/test/fixtures/performance_reviews.yml');
    }

    public function testSaveReview() {

        $dao = new PerformanceReviewDao();

        $review = new PerformanceReview();
        $review->setId(1);
        $review->setEmployeeNumber(1);

        $review = $dao->saveReview($review);
        $this->assertEquals(1, $review->getId());
    }
    
    public function testSearchReview() {
        $dao = new PerformanceReviewDao();
        $orderby['orderBy'] ='employeeId';
        $searchParams ['jobTitleCode'] = 1;
        $this->assertEquals(1, sizeof($dao->searchReview($searchParams, $orderby)));
    }
    
    public function testSearchReview1() {
        $dao = new PerformanceReviewDao();
        $this->assertEquals(3, sizeof($dao->searchReview(array())));
    }
    
    public function testSearchReview2() {
        $dao = new PerformanceReviewDao();
        $searchParams ['jobTitleCode'] = 2;
        $this->assertEquals(2, sizeof($dao->searchReview($searchParams)));
    }
   
    public function testSearchReview3() {
        $dao = new PerformanceReviewDao();
        $searchParams ['jobTitleCode'] = 2;
        $this->assertEquals(2, sizeof($dao->searchReview($searchParams)));
    }

    public function testDeleteReview() {
        $dao = new PerformanceReviewDao();
        $this->assertTrue($dao->deleteReview(array(1)));
    }
    
    public function testDeleteReviewers() {
        $dao = new PerformanceReviewDao();
        $this->assertTrue($dao->deleteReviewersByReviewId(1));
    }

    public function testGetReviewRating() {
        $dao = new PerformanceReviewDao();
        $this->assertEquals(3, sizeof($dao->searchRating((array()))));
    }
    
    public function testSearchReview4() {
        $dao = new PerformanceReviewDao();
        $searchParams ['id'] = 5;
        $result = $dao->searchReview($searchParams);
        $this->assertEquals(5, $result['id']);
    }
    
    public function testSearchReview5() {
        $dao = new PerformanceReviewDao();
        $searchParams ['employeeName'] = 'Kayla';
        $this->assertEquals(1, count($dao->searchReview($searchParams)));
    }
    
    public function testSearchReview6() {
        $dao = new PerformanceReviewDao();
        $searchParams ['from'] = '2011-01-02';
        $this->assertEquals(1, count($dao->searchReview($searchParams)));
    }
    
    public function testSearchReview7() {
        $dao = new PerformanceReviewDao();
        $searchParams ['to'] = '2011-01-01';
        $this->assertEquals(2, count($dao->searchReview($searchParams)));
    }
    
    public function testSearchReview8() {
        $dao = new PerformanceReviewDao();
        $searchParams ['employeeNumber'] = 1;
        $searchParams ['status'] = 1;
        $this->assertEquals(1, count($dao->searchReview($searchParams)));
    }
    
    public function testSearchReview9() {
        $dao = new PerformanceReviewDao();
        $searchParams ['employeeNotIn'] = 1;
        $this->assertEquals(2, count($dao->searchReview($searchParams)));
    }
    
    public function testSearchReview10() {
        $dao = new PerformanceReviewDao();
        $searchParams ['reviewerId'] = 2;
        $this->assertEquals(1, count($dao->searchReview($searchParams)));
    }
    
    public function testSearchReview11() {
        $dao = new PerformanceReviewDao();
        $searchParams ['limit'] = 1;
        $orderby['orderBy'] = 'due_date';
        $this->assertEquals(1, count($dao->searchReview($searchParams, $orderby)));
    }
    
    public function testGetReviwerEmployeeList(){
        $dao = new PerformanceReviewDao();
        $reviwerEmployeeId = 2;
        $this->assertEquals(1, count($dao->getReviwerEmployeeList($reviwerEmployeeId)));
    }
    
    public function testGetReviewRating1() {
        $parameters['id'] = 1;
        $dao = new PerformanceReviewDao();
        $result = $dao->searchRating($parameters);
        $this->assertEquals(1, $result['id']);
    }
    
    public function testGetReviewRating2() {
        $parameters['reviewId'] = 5;
        $parameters['id'] = 1;
        $dao = new PerformanceReviewDao();
        $result = $dao->searchRating($parameters);
        $this->assertEquals(1, count($result));
    }

}
