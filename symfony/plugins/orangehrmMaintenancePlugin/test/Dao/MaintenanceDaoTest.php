<?php

namespace OrangeHRM\Maintenance\test\Dao;

use OrangeHRM\Config\Config;

use OrangeHRM\Maintenance\Dao\MaintenanceDao;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

class MaintenanceDaoTest extends TestCase
{
    protected MaintenanceDao $dao;
    protected string $fixture;


    protected  function setUp(): void
    {
        $this->dao=new MaintenanceDao();
        $this->fixture=Config::get(Config::PLUGINS_DIR).'/orangehrmMaintenancePlugin/test/fixtures/EmployeeDao.yml';
        TestDataService::populate($this->fixture);

    }


    public function testExtractDataFromEmpNumber():void{
        $result=$this->dao->extractDataFromEmpNumber(array("empNumber"=>"1"),"Employee");
        $this->assertEquals('Kaylas',$result[0]->getFirstName());
    }

}