<?php


namespace OrangeHRM\Pim\test\Service;


use OrangeHRM\Config\Config;
use OrangeHRM\Entity\ReportTo;
use OrangeHRM\Pim\Dao\EmployeeReportingMethodDao;
use OrangeHRM\Pim\Dto\EmployeeSubordinateSearchFilterParams;
use OrangeHRM\Pim\Dto\EmployeeSupervisorSearchFilterParams;
use OrangeHRM\Pim\Service\EmployeeReportingMethodService;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Service
 */
class EmployeeReportingMethodServiceTest extends TestCase
{
    private EmployeeReportingMethodService $employeeReportingMethodService;
    protected string $fixture;

    protected function setUp(): void
    {
        $this->employeeReportingMethodService = new EmployeeReportingMethodService();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPimPlugin/test/fixtures/EmployeeReportingMethodDao.yml';
        TestDataService::populate($this->fixture);
    }


    public function testGetEmployeeReportingMethodDao(): void
    {
        $employeeReportingMethodDao = $this->employeeReportingMethodService->getEmployeeReportingMethodDao();
        $this->assertTrue($employeeReportingMethodDao instanceof EmployeeReportingMethodDao);
    }

    public function testGetSubordinateListCountForEmployee(): void
    {
        $employeeSubordinateSearchFilterParams = new EmployeeSubordinateSearchFilterParams();
        $employeeReportingMethodDao = $this->getMockBuilder(EmployeeReportingMethodDao::class)->getMock();
        $employeeReportingMethodDao->expects($this->once())
            ->method('getSearchEmployeeSubordinatesCount')
            ->with($employeeSubordinateSearchFilterParams)
            ->will($this->returnValue(1));

        $this->employeeReportingMethodService->setEmployeeReportingMethodDao($employeeReportingMethodDao);
        $count = $this->employeeReportingMethodService->getSubordinateListCountForEmployee($employeeSubordinateSearchFilterParams);
        $this->assertEquals(1, $count);
    }

    public function testGetImmediateSupervisorListCountForEmployee(): void
    {
        $employeeSupervisorSearchFilterParams = new EmployeeSupervisorSearchFilterParams();
        $employeeReportingMethodDao = $this->getMockBuilder(EmployeeReportingMethodDao::class)->getMock();
        $employeeReportingMethodDao->expects($this->once())
            ->method('getSearchImmediateEmployeeSupervisorsCount')
            ->with($employeeSupervisorSearchFilterParams)
            ->will($this->returnValue(1));

        $this->employeeReportingMethodService->setEmployeeReportingMethodDao($employeeReportingMethodDao);
        $count = $this->employeeReportingMethodService->getImmediateSupervisorListCountForEmployee($employeeSupervisorSearchFilterParams);
        $this->assertEquals(1, $count);
    }

    public function testGetSubordinateListForEmployee(): void
    {

        $reportTo1 = new ReportTo();
        $reportTo1->getDecorator()->setSupervisorEmployeeByEmpNumber(1);
        $reportTo1->getDecorator()->setSubordinateEmployeeByEmpNumber(2);
        $reportTo1->getDecorator()->setReportingMethodByReportingMethodId(1);

        $reportTo2 = new ReportTo();
        $reportTo2->getDecorator()->setSupervisorEmployeeByEmpNumber(1);
        $reportTo2->getDecorator()->setSubordinateEmployeeByEmpNumber(3);
        $reportTo2->getDecorator()->setReportingMethodByReportingMethodId(1);

        $employeeSubordinateSearchFilterParams = new EmployeeSubordinateSearchFilterParams();
        $employeeReportingMethodDao = $this->getMockBuilder(EmployeeReportingMethodDao::class)->getMock();
        $employeeReportingMethodDao->expects($this->once())
            ->method('searchEmployeeSubordinates')
            ->with($employeeSubordinateSearchFilterParams)
            ->will($this->returnValue([$reportTo1, $reportTo2]));

        $this->employeeReportingMethodService->setEmployeeReportingMethodDao($employeeReportingMethodDao);
        $result = $this->employeeReportingMethodService->getSubordinateListForEmployee($employeeSubordinateSearchFilterParams);
        $this->assertEquals(2, sizeof($result));
        $this->assertEquals(1, $result[0]->getSupervisor()->getEmpNumber());
        $this->assertEquals(1, $result[1]->getSupervisor()->getEmpNumber());
        $this->assertEquals(2, $result[0]->getSubordinate()->getEmpNumber());
        $this->assertEquals(3, $result[1]->getSubordinate()->getEmpNumber());
    }

    public function testGetImmediateSupervisorListForEmployee(): void
    {

        $reportTo1 = new ReportTo();
        $reportTo1->getDecorator()->setSupervisorEmployeeByEmpNumber(1);
        $reportTo1->getDecorator()->setSubordinateEmployeeByEmpNumber(2);
        $reportTo1->getDecorator()->setReportingMethodByReportingMethodId(1);

        $reportTo2 = new ReportTo();
        $reportTo2->getDecorator()->setSupervisorEmployeeByEmpNumber(3);
        $reportTo2->getDecorator()->setSubordinateEmployeeByEmpNumber(2);
        $reportTo2->getDecorator()->setReportingMethodByReportingMethodId(1);

        $employeeSupervisorSearchFilterParams = new EmployeeSupervisorSearchFilterParams();
        $employeeReportingMethodDao = $this->getMockBuilder(EmployeeReportingMethodDao::class)->getMock();
        $employeeReportingMethodDao->expects($this->once())
            ->method('searchImmediateEmployeeSupervisors')
            ->with($employeeSupervisorSearchFilterParams)
            ->will($this->returnValue([$reportTo1, $reportTo2]));

        $this->employeeReportingMethodService->setEmployeeReportingMethodDao($employeeReportingMethodDao);
        $result = $this->employeeReportingMethodService->getImmediateSupervisorListForEmployee($employeeSupervisorSearchFilterParams);
        $this->assertEquals(2, sizeof($result));
        $this->assertEquals(1, $result[0]->getSupervisor()->getEmpNumber());
        $this->assertEquals(3, $result[1]->getSupervisor()->getEmpNumber());
        $this->assertEquals(2, $result[0]->getSubordinate()->getEmpNumber());
        $this->assertEquals(2, $result[1]->getSubordinate()->getEmpNumber());
    }
}
