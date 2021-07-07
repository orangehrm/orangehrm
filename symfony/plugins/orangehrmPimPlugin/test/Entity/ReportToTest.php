<?php


namespace Entity;

use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\ReportingMethod;
use OrangeHRM\Entity\ReportTo;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Entity
 */
class ReportToTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([ReportTo::class, Employee::class, ReportingMethod::class]);
    }

    public function testReportToEntity(): void
    {
        $employee1 = new Employee();
        $employee1->setFirstName('Kayla');
        $employee1->setLastName('Abbey');
        $employee1->setEmployeeId('0001');
        $this->persist($employee1);

        $employee2 = new Employee();
        $employee2->setFirstName('Andy');
        $employee2->setLastName('Smith');
        $employee2->setEmployeeId('0002');
        $this->persist($employee2);

        $reportingMethod = new ReportingMethod();
        $reportingMethod->setName('Direct');
        $this->persist($reportingMethod);

        $reportTo = new ReportTo();
        $reportTo->setSupervisor($employee1);
        $reportTo->setSubordinate($employee2);
        $reportTo->setReportingMethod($reportingMethod);
        $this->persist($reportTo);


        /** @var ReportTo[] $reportTo */
        $reportToEmployees = $this->getRepository(ReportTo::class)->findBy(['supervisor' => 1]);
        $reportToEmployee = $reportToEmployees[0];
        $this->assertEquals('0001', $reportToEmployee->getSupervisor()->getEmployeeId());
        $this->assertEquals('0002', $reportToEmployee->getSubordinate()->getEmployeeId());
        $this->assertEquals('Direct', $reportToEmployee->getReportingMethod()->getName());
    }
}
