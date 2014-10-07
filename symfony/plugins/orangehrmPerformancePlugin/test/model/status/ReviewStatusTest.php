<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ReviewStatusTest
 * @group performance
 * @author nadeera
 */
class ReviewStatusTest extends PHPUnit_Framework_TestCase {

    public function testGetInstanceTest1() {
        $this->assertTrue(ReviewStatusActivated::getInstance() instanceof ReviewStatusActivated);
       
    }

    public function testGetInstanceTest2() {
        $this->assertTrue(ReviewStatusApproved::getInstance() instanceof ReviewStatusApproved);
       
    }

    public function testGetInstanceTest3() {
        $this->assertTrue(ReviewStatusInProgress::getInstance() instanceof ReviewStatusInProgress);
        
    }

    public function testGetInstanceTest4() {
        $this->assertTrue(ReviewStatusInactive::getInstance() instanceof ReviewStatusInactive);
       
    }

    public function testGetName1() {
        $status = new ReviewStatusActivated();
        $this->assertEquals("Activated", $status->getName());
    }

    public function testGetName2() {
        $status = new ReviewStatusApproved();
        $this->assertEquals("Approved", $status->getName());
    }

    public function testGetName3() {
        $status = new ReviewStatusInProgress();
        $this->assertEquals("In Progress", $status->getName());
    }

    public function testGetName4() {
        $status = new ReviewStatusInactive();
        $this->assertEquals("Inactive", $status->getName());
    }

    public function testGetStatusId1() {
        $status = new ReviewStatusActivated();
        $this->assertEquals(2, $status->getStatusId());
    }

    public function testGetStatusId2() {
        $status = new ReviewStatusApproved();
        $this->assertEquals(4, $status->getStatusId());
    }

    public function testGetStatusId3() {
        $status = new ReviewStatusInProgress();
        $this->assertEquals(3, $status->getStatusId());
    }

    public function testGetStatusId4() {
        $status = new ReviewStatusInactive();
        $this->assertEquals(1, $status->getStatusId());
    }

    public function testIsSaveEnabledForReviewStatusActivated() {
        $status = new ReviewStatusActivated();
        $this->assertFalse($status->isSaveEnabled());
    }

    public function testIsSaveEnabledForReviewStatusApproved() {
        $status = new ReviewStatusApproved();
        $this->assertFalse($status->isSaveEnabled());
    }

    public function testIsSaveEnabledForReviewStatusInProgress() {
        $status = new ReviewStatusInProgress();
        $this->assertTrue($status->isSaveEnabled());
    }

    public function testIsSaveEnabledForReviewStatusInactive() {
        $status = new ReviewStatusInactive();
        $this->assertTrue($status->isSaveEnabled());
    }   
    
    public function testIsActivateEnabledForReviewStatusActivated() {
        $status = new ReviewStatusActivated();
        $this->assertFalse($status->isActivateEnabled());
    }

    public function testIsActivateEnabledForReviewStatusApproved() {
        $status = new ReviewStatusApproved();
        $this->assertFalse($status->isActivateEnabled());
    }

    public function testIsActivateEnabledForReviewStatusInProgress() {
        $status = new ReviewStatusInProgress();
        $this->assertFalse($status->isActivateEnabled());
    }

    public function testIsActivateEnabledForReviewStatusInactive() {
        $status = new ReviewStatusInactive();
        $this->assertTrue($status->isActivateEnabled());
    }
    
    public function testGetNextStatusId1() {
        $status = new ReviewStatusActivated();
        $this->assertEquals(3, $status->getNextStatus());
    }

    public function testGetNextStatusId2() {
        $status = new ReviewStatusApproved();
        $this->assertEquals(4, $status->getNextStatus());
    }

    public function testGetNextStatusId3() {
        $status = new ReviewStatusInProgress();
        $this->assertEquals(3, $status->getNextStatus());
    }

    public function testGetNextStatusId4() {
        $status = new ReviewStatusInactive();
        $this->assertEquals(1, $status->getNextStatus());
    }
    
    public function testIsEvaluationsEditableForReviewStatusActivated() {
        $status = new ReviewStatusActivated();
        $this->assertTrue($status->isEvaluationsEditable());
    }

    public function testIsEvaluationsEditableForReviewStatusApproved() {
        $status = new ReviewStatusApproved();
        $this->assertFalse($status->isEvaluationsEditable());
    }

    public function testIsEvaluationsEditableForReviewStatusInProgress() {
        $status = new ReviewStatusInProgress();
        $this->assertTrue($status->isEvaluationsEditable());
    }

    public function testIsEvaluationsEditableForReviewStatusInactive() {
        $status = new ReviewStatusInactive();
        $this->assertFalse($status->isEvaluationsEditable());
    }
    
    public function testIsEvaluationsCompleateEnabledForReviewStatusActivated() {
        $status = new ReviewStatusActivated();
        $this->assertTrue($status->isEvaluationsCompleateEnabled());
    }

    public function testIsEvaluationsCompleateEnabledForrReviewStatusApproved() {
        $status = new ReviewStatusApproved();
        $this->assertFalse($status->isEvaluationsCompleateEnabled());
    }

    public function testIsEvaluationsCompleateEnabledForReviewStatusInProgress() {
        $status = new ReviewStatusInProgress();
        $this->assertTrue($status->isEvaluationsCompleateEnabled());
    }

    public function testIsEvaluationsCompleateEnabledForReviewStatusInactive() {
        $status = new ReviewStatusInactive();
        $this->assertFalse($status->isEvaluationsCompleateEnabled());
    }

}