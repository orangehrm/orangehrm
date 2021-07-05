<?php


namespace OrangeHRM\Entity\Decorator;


use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeSkill;
use OrangeHRM\Entity\ReportingMethod;
use OrangeHRM\Entity\ReportTo;
use OrangeHRM\Entity\Skill;

class ReportToDecorator
{
    use EntityManagerHelperTrait;

    /**
     * @var ReportTo
     */
    protected ReportTo $employeeReportTo;

    /**
     * @param ReportTo $employeeReportTo
     */
    public function __construct(ReportTo $employeeReportTo)
    {
        $this->employeeReportTo = $employeeReportTo;
    }

    /**
     * @return ReportTo
     */
    protected function getEmployeeReportTo(): ReportTo
    {
        return $this->employeeReportTo;
    }

    /**
     * @param int $empNumber
     */
    public function setSupervisorEmployeeByEmpNumber(int $empNumber): void
    {
        /** @var Employee|null $employee */
        $employee = $this->getReference(Employee::class, $empNumber);
        $this->getEmployeeReportTo()->setSupervisor($employee);
    }
    /**
     * @param int $empNumber
     */
    public function setSubordinateEmployeeByEmpNumber(int $empNumber): void
    {
        /** @var Employee|null $employee */
        $employee = $this->getReference(Employee::class, $empNumber);
        $this->getEmployeeReportTo()->setSubordinate($employee);
    }

    /**
     * @param int $reportingMethodId
     */
    public function setReportingMethodByReportingMethodId(int $reportingMethodId): void
    {
        /** @var ReportingMethod|null $reportingMethod */
        $reportingMethod = $this->getReference(ReportingMethod::class, $reportingMethodId);
        $this->getEmployeeReportTo()->setReportingMethod($reportingMethod);
    }

}