<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PerforamanceReviewServiceTest
 *
 * @author nadeera
 */

/**
 * @group performance
 */
class PerforamanceReviewServiceTest extends PHPUnit_Framework_TestCase {

    public function testSaveReview() {

        $review = new PerformanceReview();
        $daoMock = $this->getMock("PerformanceReviewDao", array("saveReview"));
        $daoMock->expects($this->any())
                ->method('saveReview')
                ->will($this->returnValue($review));

        $service = new PerformanceReviewService();
        $service->setDao($daoMock);

        $review = $service->saveReview($review);
        $this->assertTrue(is_object($review));
    }

    public function testSearchReview() {

        $review = new PerformanceReview();
        $daoMock = $this->getMock("PerformanceReviewDao", array("searchReview"));
        $daoMock->expects($this->any())
                ->method('searchReview')
                ->will($this->returnValue(array($review)));

        $service = new PerformanceReviewService();
        $service->setDao($daoMock);

        $reviews = $service->searchReview(array('id' => 1));
        $this->assertEquals(1, sizeof($reviews));
    }

    public function testDeleteReview() {

        $daoMock = $this->getMock("PerformanceReviewDao", array("deleteReview"));
        $daoMock->expects($this->any())
                ->method('deleteReview')
                ->will($this->returnValue(true));

        $service = new PerformanceReviewService();
        $service->setDao($daoMock);

        $reviews = $service->deleteReview(array(1));
        $this->assertEquals(1, $service->deleteReview(1));
    }

    public function testDeleteReviewers() {

        $daoMock = $this->getMock("PerformanceReviewDao", array("deleteReviewersByReviewId"));
        $daoMock->expects($this->any())
                ->method('deleteReviewersByReviewId')
                ->will($this->returnValue(true));

        $service = new PerformanceReviewService();
        $service->setDao($daoMock);

        $this->assertEquals(1, $service->deleteReviewersByReviewId(1));
    }

    public function testSearchReviewRating() {
        $array = array(0 => 1);
        $daoMock = $this->getMock("PerformanceReviewDao", array("searchRating"));
        $daoMock->expects($this->any())
                ->method('searchRating')
                ->will($this->returnValue(array(1)));

        $service = new PerformanceReviewService();
        $service->setDao($daoMock);

        $this->assertEquals($array, $service->searchReviewRating(1));
    }

    public function testGetReviewRating() {
        $array = array(0 => 1);
        $daoMock = $this->getMock("PerformanceReviewDao", array("searchRating"));
        $daoMock->expects($this->any())
                ->method('searchRating')
                ->will($this->returnValue(array(1)));

        $service = new PerformanceReviewService();
        $service->setDao($daoMock);

        $this->assertEquals($array, $service->getReviewRating(1));
    }

    public function testGetReviwerEmployeeList() {
        $array = array(0 => 1);
        $daoMock = $this->getMock("PerformanceReviewDao", array("getReviwerEmployeeList"));
        $daoMock->expects($this->any())
                ->method('getReviwerEmployeeList')
                ->will($this->returnValue(array(1)));

        $service = new PerformanceReviewService();
        $service->setDao($daoMock);

        $result = $service->getReviwerEmployeeList(2);

        $this->assertEquals($array, $result);
    }

    public function testGetCountReviewList() {
        $serachParams['limit'] = null;
        $daoMock = $this->getMock("PerformanceReviewDao", array("getCountReviewList"));
        $daoMock->expects($this->any())
                ->method('getCountReviewList')
                ->will($this->returnValue(array(1)));

        $service = new PerformanceReviewService();
        $service->setDao($daoMock);

        $result = $service->getCountReviewList($serachParams);

        $this->assertEquals(3, $result);
    }

}
