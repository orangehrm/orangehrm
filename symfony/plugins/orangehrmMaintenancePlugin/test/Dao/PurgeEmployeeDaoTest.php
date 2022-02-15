<?php

namespace OrangeHRM\Tests\Maintenance\Dao;

use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Maintenance\Dao\PurgeEmployeeDao;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Maintenance
 * @group Dao
 */
class PurgeEmployeeDaoTest extends TestCase
{
    private PurgeEmployeeDao $employeePurgeDao;
    protected string $fixture;

    protected function setUp(): void
    {
        $this->employeePurgeDao = new PurgeEmployeeDao();
        $this->fixture = Config::get(
                Config::PLUGINS_DIR
            ) . '/orangehrmMaintenancePlugin/test/fixtures/PurgeEmployeeDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetEmployeePurgingList(): void
    {
        $employeePurgeList = $this->employeePurgeDao->getEmployeePurgingList();

        $this->assertCount(3, $employeePurgeList);
        $this->assertEquals(1, $employeePurgeList[0]->getEmpNumber());
        $this->assertEquals("Linda", $employeePurgeList[1]->getFirstName());
        $this->assertEquals(
            "Resigned",
            $employeePurgeList[2]->getEmployeeTerminationRecord()->getTerminationReason()->getName()
        );
    }

    public function testExtractDataFromEmpNumber(): void
    {
        $table = 'Employee';
        $matchByValues = ['empNumber' => '1'];

        $data = $this->employeePurgeDao->extractDataFromEmpNumber($matchByValues, $table);
        $this->assertTrue(sizeof($data) > 0);
        $this->assertEquals('Odis', $data[0]->getFirstName());
        $this->assertEquals('Alwin', $data[0]->getLastName());
        $this->assertEquals('Heath', $data[0]->getMiddleName());
    }

    public function testSaveEntity(): void
    {
        $employee = new Employee();
        $employee->setFirstName("Devi");
        $employee->setLastName("DS");
        $employee->setMiddleName("Nar");

        $saveResult = $this->employeePurgeDao->saveEntity($employee);
        $this->assertEquals("Devi", $saveResult->getFirstName());
        $this->assertEquals("DS", $saveResult->getLastName());
        $this->assertEquals("Nar", $saveResult->getMiddleName());
    }
}
