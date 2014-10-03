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
        $searchParams ['jobTitleCode'] = 1;
        $this->assertEquals(1, sizeof($dao->searchReview($searchParams)));
    }
    
    public function testSearchReview1() {
        $dao = new PerformanceReviewDao();
        $this->assertEquals(3, sizeof($dao->searchReview($searchParams)));
    }
    
    public function testSearchReview2() {
        $dao = new PerformanceReviewDao();
        $searchParams ['jobTitleCode'] = 2;
        $searchParams ['departmentId'] = 2;
        $this->assertEquals(2, sizeof($dao->searchReview($searchParams)));
    }
   
    public function testSearchReview3() {
        $dao = new PerformanceReviewDao();
        $searchParams ['jobTitleCode'] = 1;
        $searchParams ['departmentId'] = 2;
        $this->assertEquals(0, sizeof($dao->searchReview($searchParams)));
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
        $this->assertEquals(2, sizeof($dao->searchRating(($parameters['id']))));
    }

}
