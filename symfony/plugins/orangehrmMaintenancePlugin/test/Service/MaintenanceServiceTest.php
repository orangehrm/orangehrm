<?php

namespace OrangeHRM\Maintenance\test\Service;

use OrangeHRM\Config\Config;
use OrangeHRM\Maintenance\Service\MaintenanceService;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

class MaintenanceServiceTest extends TestCase
{
    private string $fixture;
    private MaintenanceService $maintenanceService;

    protected  function setUp(): void
    {
        $this->maintenanceService=new MaintenanceService();
        $this->fixture=Config::get(Config::PLUGINS_DIR).'/orangehrmMaintenancePlugin/test/fixtures/EmployeeDao.yml';
        TestDataService::populate($this->fixture);

    }

    public function testAccessEmployeeData():void {
        $result=$this->maintenanceService->accessEmployeeData(1);
//        var_dump($result);
//        die;
        $this->assertEquals('Kayla',$result['Employee'][0]['firstName']);

    }
}