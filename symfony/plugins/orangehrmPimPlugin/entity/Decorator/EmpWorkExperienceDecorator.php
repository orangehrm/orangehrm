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

use DateTime;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmpWorkExperience;

class EmpWorkExperienceDecorator
{
    use EntityManagerHelperTrait;
    use DateTimeHelperTrait;

    /**
     * @var EmpWorkExperience
     */
    protected EmpWorkExperience $employeeWorkExperience;

    /**
     * @param EmpWorkExperience $employeeWorkExperience
     */
    public function __construct(EmpWorkExperience $employeeWorkExperience)
    {
        $this->employeeWorkExperience = $employeeWorkExperience;
    }

    /**
     * @return EmpWorkExperience
     */
    protected function getEmployeeWorkExperience(): EmpWorkExperience
    {
        return $this->employeeWorkExperience;
    }

    /**
     * @param int $empNumber
     */
    public function setEmployeeByEmpNumber(int $empNumber): void
    {
        /** @var Employee|null $employee */
        $employee = $this->getReference(Employee::class, $empNumber);
        $this->getEmployeeWorkExperience()->setEmployee($employee);
    }

    /**
     * @return string|null
     */
    public function getFromDate(): ?string
    {
        $date = $this->getEmployeeWorkExperience()->getFromDate();
        return $this->getDateTimeHelper()->formatDateTimeToYmd($date);
    }

    /**
     * @return string|null
     */
    public function getToDate(): ?string
    {
        $date = $this->getEmployeeWorkExperience()->getToDate();
        return $this->getDateTimeHelper()->formatDateTimeToYmd($date);
    }

    /**
     * @return string|null
     */
    public function getDuration(): ?string
    {
        $fromDate = $this->getEmployeeWorkExperience()->getFromDate();
        if ($fromDate instanceof DateTime) {
            $dateInterval = $this->getEmployeeWorkExperience()->getFromDate()->diff(
                $this->getEmployeeWorkExperience()->getToDate()
            );
            return round($dateInterval->days / 356, 1);
        }
        return null;
    }
}
