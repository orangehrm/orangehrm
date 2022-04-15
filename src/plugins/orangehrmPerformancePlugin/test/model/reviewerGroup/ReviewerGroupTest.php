<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ReviewerGroupTest
 * @group performance
 * @author nadeera
 */
class ReviewerGroupTest extends PHPUnit_Framework_TestCase {

    public function testReviewGroupId1() {
        $group = new SelfReviewerGroup();
        $this->assertEquals(2, $group->getId());
    }
    
    public function testReviewGroupId4() {
        $group = new SupervisorReviewerGroup();
        $this->assertEquals(1, $group->getId());
    }
}