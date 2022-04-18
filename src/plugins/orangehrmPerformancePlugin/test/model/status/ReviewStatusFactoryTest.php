<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ReviewStatusFactoryTest
 * @group performance
 * @author nadeera
 */
class ReviewStatusFactoryTest extends PHPUnit_Framework_TestCase {
    
    
    public function testGetStatus1(){
        $statusFactory = new ReviewStatusFactory();
        $this->assertTrue($statusFactory->getStatus(1) instanceof ReviewStatusInactive);
        $this->assertEquals(1, $statusFactory->getStatus(1)->getStatusId());
    }
    
    public function testGetStatus2(){
        $statusFactory = new ReviewStatusFactory();
        $this->assertTrue($statusFactory->getStatus(2) instanceof ReviewStatusActivated);
        $this->assertEquals(2, $statusFactory->getStatus(2)->getStatusId());
    }
    
    public function testGetStatus3(){
        $statusFactory = new ReviewStatusFactory();
        $this->assertTrue($statusFactory->getStatus(3) instanceof ReviewStatusInProgress);
        $this->assertEquals(3, $statusFactory->getStatus(3)->getStatusId());
    }
    
    public function testGetStatus4(){
        $statusFactory = new ReviewStatusFactory();
        $this->assertTrue($statusFactory->getStatus(4) instanceof ReviewStatusApproved);
        $this->assertEquals(4, $statusFactory->getStatus(4)->getStatusId());
    }
    
    public function testGetStatus5(){
        $statusFactory = new ReviewStatusFactory();
        $this->assertTrue($statusFactory->getStatus() instanceof ReviewStatusInactive);
        $this->assertEquals(1, $statusFactory->getStatus()->getStatusId());
    }
    
  
}