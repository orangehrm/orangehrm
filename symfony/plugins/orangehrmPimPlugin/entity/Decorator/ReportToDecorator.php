<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\ReportingMethod;
use OrangeHRM\Entity\ReportTo;

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
