<?php


namespace Api\Model;


use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\ReportingMethod;
use OrangeHRM\Entity\ReportTo;
use OrangeHRM\Pim\Api\Model\EmployeeSubordinateModel;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Pim
 * @group Model
 */
class EmployeeSubordinateModelTest extends TestCase
{
    public function testToArray()
    {
        $resultArray = [
            "subordinate" => [
                "empNumber" => 2,
                "firstName" => 'Andy',
                "lastName" => "Smith",
                "middleName" => "",
            ],
            "reportingMethod" => [
                "id" => 1,
                "name" => 'Direct'
            ]
        ];

        $employee1 = new Employee();
        $employee1->setFirstName('Kayla');
        $employee1->setLastName('Abbey');
        $employee1->setEmployeeId('0001');
        $employee1->setEmpNumber(1);

        $employee2 = new Employee();
        $employee2->setFirstName('Andy');
        $employee2->setLastName('Smith');
        $employee2->setEmployeeId('0002');
        $employee2->setEmpNumber(2);

        $reportingMethod = new ReportingMethod();
        $reportingMethod->setName('Direct');
        $reportingMethod->setId(1);

        $reportTo = new ReportTo();
        $reportTo->setSupervisor($employee1);
        $reportTo->setSubordinate($employee2);
        $reportTo->setReportingMethod($reportingMethod);

        $employeeSubordinateModel = new EmployeeSubordinateModel($reportTo);
        $this->assertEquals($resultArray, $employeeSubordinateModel->toArray());
    }
}
