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

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\ReportToDecorator;

/**
 * @method ReportToDecorator getDecorator()
 *
 * @ORM\Table(name="hs_hr_emp_reportto")
 * @ORM\Entity
 */
class ReportTo
{
    use DecoratorTrait;

    /**
     * @var Employee
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee", cascade={"persist"})
     * @ORM\JoinColumn(name="erep_sup_emp_number", referencedColumnName="emp_number", nullable=false)
     * @ORM\Id
     */
    private Employee $supervisor;

    /**
     * @var Employee
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee", cascade={"persist"})
     * @ORM\JoinColumn(name="erep_sub_emp_number", referencedColumnName="emp_number", nullable=false)
     * @ORM\Id
     */
    private Employee $subordinate;

    /**
     * @var ReportingMethod
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\ReportingMethod", inversedBy="reportTos", cascade={"persist"})
     * @ORM\JoinColumn(name="erep_reporting_mode", referencedColumnName="reporting_method_id", nullable=false)
     * @ORM\Id
     */
    private ReportingMethod $reportingMethod;

    /**
     * @return Employee
     */
    public function getSupervisor(): Employee
    {
        return $this->supervisor;
    }

    /**
     * @param Employee $supervisor
     */
    public function setSupervisor(Employee $supervisor): void
    {
        $this->supervisor = $supervisor;
    }

    /**
     * @return Employee
     */
    public function getSubordinate(): Employee
    {
        return $this->subordinate;
    }

    /**
     * @param Employee $subordinate
     */
    public function setSubordinate(Employee $subordinate): void
    {
        $this->subordinate = $subordinate;
    }

    /**
     * @return ReportingMethod
     */
    public function getReportingMethod(): ReportingMethod
    {
        return $this->reportingMethod;
    }

    /**
     * @param ReportingMethod $reportingMethod
     */
    public function setReportingMethod(ReportingMethod $reportingMethod): void
    {
        $this->reportingMethod = $reportingMethod;
    }
}
