<?php

namespace OrangeHRM\Tests\Claim\Dao;

use OrangeHRM\Claim\Dao\ClaimEventDao;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\ClaimEvent;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

class ClaimEventDaoTest extends KernelTestCase
{
    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        //private ClaimEvent $claimEvent = new ClaimEvent();
        //$this->employeeDao = new EmployeeDao();
        $fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmClaimPlugin/test/fixtures/ClaimEvent.yaml';
        TestDataService::populate($fixture);
    }
    public function testSaveEvent():void{
        $claimEvent=new ClaimEvent();
        $claimEvent->setName("testname2");
        $claimEvent->setStatus(true);
        $dao=new ClaimEventDao();
        $result=$dao->saveEvent($claimEvent);
        $this->assertEquals("testname2",$result->getName());
        $this->assertEquals(true,$result->getStatus());
    }
}