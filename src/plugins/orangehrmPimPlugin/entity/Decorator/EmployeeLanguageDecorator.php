<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeLanguage;
use OrangeHRM\Entity\Language;

class EmployeeLanguageDecorator
{
    use EntityManagerHelperTrait;

    /**
     * @var EmployeeLanguage
     */
    protected EmployeeLanguage $employeeLanguage;

    /**
     * @param EmployeeLanguage $employeeSkill
     */
    public function __construct(EmployeeLanguage $employeeSkill)
    {
        $this->employeeLanguage = $employeeSkill;
    }

    /**
     * @return EmployeeLanguage
     */
    protected function getEmployeeLanguage(): EmployeeLanguage
    {
        return $this->employeeLanguage;
    }

    /**
     * @return string|null
     */
    public function getFluency(): ?string
    {
        $fluency = $this->getEmployeeLanguage()->getFluency();
        return EmployeeLanguage::FLUENCIES[$fluency] ?? null;
    }

    /**
     * @return string|null
     */
    public function getCompetency(): ?string
    {
        $competency = $this->getEmployeeLanguage()->getCompetency();
        return EmployeeLanguage::COMPETENCIES[$competency] ?? null;
    }

    /**
     * @param int $empNumber
     */
    public function setEmployeeByEmpNumber(int $empNumber): void
    {
        /** @var Employee|null $employee */
        $employee = $this->getReference(Employee::class, $empNumber);
        $this->getEmployeeLanguage()->setEmployee($employee);
    }

    /**
     * @param string $id
     */
    public function setLanguageById(string $id): void
    {
        /** @var Language|null $language */
        $language = $this->getReference(Language::class, $id);
        $this->getEmployeeLanguage()->setLanguage($language);
    }
}
