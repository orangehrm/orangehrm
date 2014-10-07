<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ReviewerGroupFactoryTest
 * @group performance
 * @author nadeera
 */
class ReviewerGroupFactoryTest extends PHPUnit_Framework_TestCase {

    public function testGetInstance() {
        $groupFactory = ReviewerGroupFactory::getInstance();
        $this->assertTrue($groupFactory instanceof ReviewerGroupFactory);
    }

    public function testTestGetGroupObjectByType2() {
        $groupFactory = new ReviewerGroupFactory();
        $this->assertTrue($groupFactory->getReviewer('supervisors') instanceof SupervisorReviewerGroup);
    }

    public function testTestGetGroupObjectByType3() {
        $groupFactory = new ReviewerGroupFactory();
        $this->assertTrue($groupFactory->getReviewer('selfReviewer') instanceof SelfReviewerGroup);
    }

}
